<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiRecommendationService
{
    protected string $apiKey;
    protected string $model;
    protected string $endpoint = 'https://openrouter.ai/api/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.key');
        $this->model  = config('services.openrouter.model', 'openai/gpt-4o-mini');
    }

    /**
     * يرجع مصفوفة من: ['activity' => Activity, 'reason' => string]
     * مرتبة حسب الأنسب للمستخدم.
     */
    public function recommend(User $user, int $limit = 5): array
    {
        $candidates = $this->getCandidateActivities($user);

        if ($candidates->isEmpty()) {
            return [];
        }

        $userInterests = $this->getUserInterestsSummary($user);

        $pickedIds = $this->askModelToPick($userInterests, $candidates, $limit);

        if (empty($pickedIds)) {
            // fallback بسيط: لو فشل الذكاء أو ما رجع نتيجة صالحة، رجّع أقرب الفعاليات زمنياً
            return $candidates->take($limit)->map(fn ($a) => [
                'activity' => $a,
                'reason'   => 'فعالية قادمة قد تهمك',
            ])->values()->all();
        }

        $byId = $candidates->keyBy('id');

        return collect($pickedIds)
            ->filter(fn ($item) => $byId->has($item['activity_id']))
            ->map(fn ($item) => [
                'activity' => $byId[$item['activity_id']],
                'reason'   => $item['reason'] ?? 'فعالية قادمة قد تهمك',
            ])
            ->take($limit)
            ->values()
            ->all();
    }

    /**
     * الفعاليات القادمة (30 يوم) اللي المستخدم ما حجزها من قبل.
     */
    protected function getCandidateActivities(User $user)
    {
        return Activity::query()
            ->with(['culturalCenter:id,name,location', 'activityType:id,title', 'venue:id,name'])
            ->where('start_time', '>=', now())
            ->where('start_time', '<=', now()->addDays(30))
            ->whereDoesntHave('reservations', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('status', '!=', 'cancelled');
            })
            ->orderBy('start_time')
            ->limit(30)
            ->get();
    }

    /**
     * ملخص مختصر عن اهتمامات المستخدم: أنواع الفعاليات اللي حجزها أو قيّمها بتقييم عالي.
     */
    protected function getUserInterestsSummary(User $user): array
    {
        $reservedTypes = $user->reservations()
            ->whereNotNull('activity_id')
            ->with('activity:id,activity_type_id,title', 'activity.activityType:id,title')
            ->get()
            ->pluck('activity')
            ->filter()
            ->map(fn ($a) => ['title' => $a->title, 'type' => $a->activityType->title ?? null]);

        $likedTypes = $user->ratings()
            ->where('value', '>=', 4)
            ->where('rateable_type', Activity::class)
            ->with('rateable:id,activity_type_id,title', 'rateable.activityType:id,title')
            ->get()
            ->pluck('rateable')
            ->filter()
            ->map(fn ($a) => ['title' => $a->title, 'type' => $a->activityType->title ?? null]);

        return [
            'past_reservations' => $reservedTypes->values(),
            'highly_rated'      => $likedTypes->values(),
        ];
    }

    /**
     * يرسل الاهتمامات + الفعاليات المرشحة للموديل، ويطلب منه JSON فقط.
     */
    protected function askModelToPick(array $userInterests, $candidates, int $limit): array
    {
        $candidatesPayload = $candidates->map(fn ($a) => [
            'activity_id' => $a->id,
            'title'       => $a->title,
            'type'        => $a->activityType->title ?? null,
            'description' => str($a->description)->limit(150)->toString(),
            'center'      => $a->culturalCenter->name ?? null,
            'start_time'  => optional($a->start_time)->format('Y-m-d H:i'),
        ]);

        $systemPrompt = <<<PROMPT
أنت محرك توصيات لتطبيق ثقافي. مهمتك اختيار أفضل {$limit} فعاليات من قائمة "الفعاليات المرشحة"
بناءً على "اهتمامات المستخدم السابقة".

قواعد صارمة:
1) اختر فقط من ضمن activity_id الموجودة في قائمة المرشحين، لا تخترع أي id.
2) إذا لم تكن هناك اهتمامات سابقة واضحة، اختر أكثر الفعاليات تنوعاً أو الأقرب زمنياً.
3) لكل فعالية مختارة اكتب سبب قصير جداً (جملة واحدة، بالعربي) لماذا قد تعجب المستخدم.
4) أعد الإجابة بصيغة JSON فقط بدون أي نص إضافي أو Markdown، بهذا الشكل بالضبط:
[{"activity_id": 1, "reason": "..."}, {"activity_id": 2, "reason": "..."}]

اهتمامات المستخدم (JSON):
{$this->toJson($userInterests)}

الفعاليات المرشحة (JSON):
{$this->toJson($candidatesPayload)}
PROMPT;

        try {
            $response = Http::withToken($this->apiKey)
                ->withHeaders([
                    'HTTP-Referer' => config('app.url'),
                    'X-Title'      => config('app.name'),
                ])
                ->timeout(12) // ⬇ كانت 30 ثانية — أطول من مهلة اتصال التطبيق غالباً
                ->post($this->endpoint, [
                    'model'    => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => 'اعطني التوصيات الآن بصيغة JSON فقط.'],
                    ],
                    'temperature' => 0.3,
                ]);

            if ($response->failed()) {
                Log::error('AiRecommendationService: OpenRouter failed', ['body' => $response->body()]);
                return [];
            }

            $raw = $response->json('choices.0.message.content') ?? '';
            $raw = trim(preg_replace('/^```json|```$/m', '', $raw));

            $decoded = json_decode($raw, true);

            if (!is_array($decoded)) {
                return [];
            }

            return array_filter($decoded, fn ($item) => isset($item['activity_id']));

        } catch (\Throwable $e) {
            Log::error('AiRecommendationService error: ' . $e->getMessage());
            return [];
        }
    }

    protected function toJson($data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}