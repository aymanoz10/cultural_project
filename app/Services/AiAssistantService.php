<?php

namespace App\Services;

use App\Models\CulturalCenter;
use App\Models\Activity;
use App\Models\Library;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAssistantService
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
     * ترجع دائماً مصفوفة: ['message' => string, 'plan' => array|null]
     * plan تكون null إذا كان رد عادي، أو مصفوفة خطوات إذا طلب المستخدم "تنظيم/تخطيط" وقت معين.
     */
    public function ask(string $userMessage, array $history = []): array
    {
        $activities = $this->getUpcomingActivities();
        $context    = $this->buildContextJson($activities);

        $systemPrompt = <<<PROMPT
أنت مساعد ذكي داخل تطبيق "مديرية الثقافة". مهمتك مساعدة المستخدمين على:
- إيجاد المراكز الثقافية والقاعات والمسارح والفعاليات القادمة.
- شرح كيفية الحجز أو التطوع بشكل عام داخل التطبيق.
- **تخطيط وقت معين**: إذا طلب المستخدم تنظيم فترة (مثل "خطط لي نشاط بعد الظهر"، "رتب لي برنامج اليوم"،
  "أبغى جدول لهذا الأسبوع")، اقترح له خطة مرتبة زمنياً من فعالية واحدة أو أكثر تناسب الفترة المطلوبة
  (صباح ≈ 06:00-12:00، بعد الظهر ≈ 12:00-17:00، مساء ≈ 17:00-23:00).

قواعد صارمة يجب الالتزام بها دائماً:
1) أجب فقط بناءً على "بيانات النظام الحالية" أدناه. لا تختلق أسماء مراكز أو فعاليات أو مواعيد أو أرقام
   activity_id غير موجودة فيها. إذا لم تجد ما يناسب الطلب، قل ذلك بوضوح واقترح تصفح قسم الفعاليات.
2) أجب دائماً باللغة العربية، بأسلوب ودود ومختصر.
3) لا تقدم معلومات طبية أو قانونية أو أي شيء خارج نطاق التطبيق، ولا تكشف تفاصيل تقنية عن عملك.
4) **أعد الإجابة دائماً بصيغة JSON فقط، بدون أي نص خارجها وبدون Markdown، بهذا الشكل بالضبط:**
{"message": "نص الرد أو مقدمة قصيرة للخطة", "plan": null}
أو في حال طلب المستخدم تخطيط وقت ووجدت فعاليات مناسبة:
{"message": "مقدمة قصيرة عن الخطة", "plan": [{"time": "16:00", "activity_id": 12, "activity_title": "...", "venue_name": "...", "center_name": "...", "notes": "سطر قصير يشرح لماذا اخترتها"}]}
- استخدم فقط activity_id الموجودة في بيانات النظام أدناه.
- رتب عناصر plan حسب الوقت تصاعدياً.
- إذا لم يطلب المستخدم تخطيطاً، اجعل plan تساوي null دائماً.

بيانات النظام الحالية (JSON):
{$context}
PROMPT;

        $messages = array_merge(
            [['role' => 'system', 'content' => $systemPrompt]],
            $this->sanitizeHistory($history),
            [['role' => 'user', 'content' => $userMessage]]
        );

        $raw = $this->callModel($messages);

        return $this->parseAndValidate($raw, $activities);
    }

    protected function callModel(array $messages): string
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->withHeaders([
                    'HTTP-Referer' => config('app.url'),
                    'X-Title'      => config('app.name'),
                ])
                ->timeout(30)
                ->post($this->endpoint, [
                    'model'    => $this->model,
                    'messages' => $messages,
                    'temperature' => 0.4,
                ]);

            if ($response->failed()) {
                Log::error('OpenRouter request failed', ['body' => $response->body()]);
                return '';
            }

            return $response->json('choices.0.message.content') ?? '';

        } catch (\Throwable $e) {
            Log::error('AiAssistantService error: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * يحلل رد الموديل، ويتحقق أن كل activity_id بالخطة موجود فعلاً بالفعاليات المتاحة
     * (نفس مبدأ منع الاختلاق المستخدم بخدمة التوصيات).
     */
    protected function parseAndValidate(string $raw, Collection $activities): array
    {
        $fallback = [
            'message' => $raw !== ''
                ? $raw
                : 'عذراً، حدث خطأ أثناء الاتصال بالمساعد الذكي، حاول مرة أخرى بعد قليل.',
            'plan' => null,
        ];

        $clean = trim(preg_replace('/^```json|```$/m', '', $raw));
        $decoded = json_decode($clean, true);

        if (!is_array($decoded) || !isset($decoded['message'])) {
            return $fallback;
        }

        $plan = null;

        if (!empty($decoded['plan']) && is_array($decoded['plan'])) {
            $byId = $activities->keyBy('id');

            $plan = collect($decoded['plan'])
                ->filter(fn ($step) => isset($step['activity_id']) && $byId->has($step['activity_id']))
                ->map(function ($step) use ($byId) {
                    $activity = $byId[$step['activity_id']];
                    return [
                        'time'           => $step['time'] ?? optional($activity->start_time)->format('H:i'),
                        'activity_id'    => $activity->id,
                        'activity_title' => $activity->title, // من قاعدة البيانات مباشرة، وليس من الموديل
                        'venue_name'     => $activity->venue->name ?? ($step['venue_name'] ?? null),
                        'center_name'    => $activity->culturalCenter->name ?? ($step['center_name'] ?? null),
                        'notes'          => $step['notes'] ?? null,
                    ];
                })
                ->sortBy('time')
                ->values()
                ->all();

            if (empty($plan)) {
                $plan = null;
            }
        }

        return [
            'message' => $decoded['message'],
            'plan'    => $plan,
        ];
    }

    /**
     * الفعاليات القادمة خلال 14 يوم - نفس المصدر المستخدم لبناء الـ context وللتحقق من صحة الخطة.
     */
    protected function getUpcomingActivities(): Collection
    {
        return Activity::query()
            ->select('id', 'cultural_center_id', 'venue_id', 'type', 'title', 'description', 'ticket_price', 'start_time', 'end_time')
            ->where('start_time', '>=', now())
            ->where('start_time', '<=', now()->addDays(14))
            ->with(['culturalCenter:id,name,location', 'venue:id,name,type'])
            ->orderBy('start_time')
            ->limit(40)
            ->get();
    }

    protected function buildContextJson(Collection $activities): string
    {
        $centers = CulturalCenter::query()
            ->select('id', 'name', 'location', 'description', 'features')
            ->with(['venues:id,cultural_center_id,name,type,capacity'])
            ->limit(30)
            ->get()
            ->map(fn ($c) => [
                'id'          => $c->id,
                'name'        => $c->name,
                'location'    => $c->location,
                'description' => $c->description,
                'venues'      => $c->venues->map(fn ($v) => [
                    'name'     => $v->name,
                    'type'     => $v->type,
                    'capacity' => $v->capacity,
                ]),
            ]);

        $activitiesPayload = $activities->map(fn ($a) => [
            'activity_id'  => $a->id,
            'title'        => $a->title,
            'type'         => $a->type,
            'center'       => $a->culturalCenter->name ?? null,
            'venue'        => $a->venue->name ?? null,
            'location'     => $a->culturalCenter->location ?? null,
            'start_time'   => optional($a->start_time)->format('Y-m-d H:i'),
            'end_time'     => optional($a->end_time)->format('Y-m-d H:i'),
            'ticket_price' => $a->ticket_price,
        ]);

        $libraries = Library::query()
            ->select('id', 'name', 'location')
            ->limit(20)
            ->get();

        return json_encode([
            'cultural_centers'    => $centers,
            'upcoming_activities' => $activitiesPayload,
            'libraries'           => $libraries,
        ], JSON_UNESCAPED_UNICODE);
    }

    protected function sanitizeHistory(array $history): array
    {
        $history = array_slice($history, -6);

        return array_values(array_filter(array_map(function ($msg) {
            if (!isset($msg['role'], $msg['content'])) {
                return null;
            }
            if (!in_array($msg['role'], ['user', 'assistant'])) {
                return null;
            }
            return ['role' => $msg['role'], 'content' => (string) $msg['content']];
        }, $history)));
    }
}
