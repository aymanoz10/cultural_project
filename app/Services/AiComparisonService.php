<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\CulturalCenter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiComparisonService
{
    protected string $apiKey;
    protected string $model;
    protected string $endpoint = 'https://openrouter.ai/api/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.key');
        $this->model  = config('services.openrouter.model', 'openai/gpt-4o-mini');
    }

    public function compareActivities(int $id1, int $id2): array
    {
        $activities = Activity::with('culturalCenter:id,name,location')
            ->whereIn('id', [$id1, $id2])
            ->get()
            ->keyBy('id');

        if (!$activities->has($id1) || !$activities->has($id2)) {
            abort(404, 'إحدى الفعاليتين غير موجودة');
        }

        $a = $activities[$id1];
        $b = $activities[$id2];

        // الجدول الحقيقي يُبنى من قاعدة البيانات مباشرة - بدون أي تدخل من الذكاء الاصطناعي
        // هذا يضمن أن الأرقام والتواريخ صحيحة 100%.
        $criteria = [
            $this->row('العنوان', $a->title, $b->title),
            $this->row('النوع', $a->type, $b->type),
            $this->row('المركز الثقافي', $a->culturalCenter->name ?? '-', $b->culturalCenter->name ?? '-'),
            $this->row('الموقع', $a->culturalCenter->location ?? '-', $b->culturalCenter->location ?? '-'),
            $this->row('التاريخ والوقت', optional($a->start_time)->format('Y-m-d H:i') ?? '-', optional($b->start_time)->format('Y-m-d H:i') ?? '-'),
            $this->row('سعر التذكرة', $a->ticket_price !== null ? $a->ticket_price . ' ' : 'مجاني', $b->ticket_price !== null ? $b->ticket_price . ' ' : 'مجاني'),
            $this->row('المقاعد المتاحة', $a->capacity !== null ? max(0, $a->capacity - $a->confirmedReservationsCount()) : 'غير محدد', $b->capacity !== null ? max(0, $b->capacity - $b->confirmedReservationsCount()) : 'غير محدد'),
        ];

        $verdict = $this->generateVerdict('activity', $a->title, $b->title, $criteria);

        return ['criteria' => $criteria, 'verdict' => $verdict];
    }

    public function compareCenters(int $id1, int $id2): array
    {
        $centers = CulturalCenter::withCount([
                'venues',
                'activities as upcoming_activities_count' => fn ($q) => $q->where('start_time', '>=', now()),
            ])
            ->whereIn('id', [$id1, $id2])
            ->get()
            ->keyBy('id');

        if (!$centers->has($id1) || !$centers->has($id2)) {
            abort(404, 'أحد المركزين غير موجود');
        }

        $a = $centers[$id1];
        $b = $centers[$id2];

        $criteria = [
            $this->row('الاسم', $a->name, $b->name),
            $this->row('الموقع', $a->location ?? '-', $b->location ?? '-'),
            $this->row('عدد القاعات/المسارح', (string) $a->venues_count, (string) $b->venues_count),
            $this->row('الفعاليات القادمة', (string) $a->upcoming_activities_count, (string) $b->upcoming_activities_count),
            $this->row('المميزات', is_array($a->features) ? implode('، ', $a->features) : '-', is_array($b->features) ? implode('، ', $b->features) : '-'),
        ];

        $verdict = $this->generateVerdict('center', $a->name, $b->name, $criteria);

        return ['criteria' => $criteria, 'verdict' => $verdict];
    }

    protected function row(string $label, $item1, $item2): array
    {
        return ['label' => $label, 'item_1' => (string) $item1, 'item_2' => (string) $item2];
    }

    /**
     * الذكاء الاصطناعي هنا يكتب فقط ملخص/رأي مبني على الجدول الجاهز،
     * ولا يُسمح له بإضافة أي رقم أو تاريخ من عنده.
     */
    protected function generateVerdict(string $type, string $nameA, string $nameB, array $criteria): string
    {
        $label = $type === 'activity' ? 'فعاليتين' : 'مركزين ثقافيين';

        $systemPrompt = <<<PROMPT
أنت مساعد يساعد مستخدم تطبيق ثقافي يتردد بين {$label}: "{$nameA}" و"{$nameB}".
لديك جدول مقارنة حقيقي مبني من قاعدة البيانات (لا تغيّر أي رقم أو تاريخ فيه):

{$this->toJson($criteria)}

اكتب فقرة قصيرة جداً (3-4 أسطر كحد أقصى) بالعربي، أسلوب ودود، تلخص أهم الفروقات
وتقترح بشكل غير ملزم أيهما قد يناسب المستخدم أكثر حسب المعطيات فقط (مثلاً لو واحد أرخص
أو أقرب موعداً أو فيه مقاعد أكثر). لا تخترع أي معلومة غير موجودة بالجدول. لا تستخدم Markdown.
PROMPT;

        try {
            $response = Http::withToken($this->apiKey)
                ->withHeaders([
                    'HTTP-Referer' => config('app.url'),
                    'X-Title'      => config('app.name'),
                ])
                ->timeout(30)
                ->post($this->endpoint, [
                    'model'    => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => 'اعطني الملخص الآن.'],
                    ],
                    'temperature' => 0.4,
                ]);

            if ($response->failed()) {
                Log::error('AiComparisonService: OpenRouter failed', ['body' => $response->body()]);
                return 'قارن بين الخيارين حسب الجدول أعلاه واختر الأنسب لك.';
            }

            return trim($response->json('choices.0.message.content') ?? '')
                ?: 'قارن بين الخيارين حسب الجدول أعلاه واختر الأنسب لك.';

        } catch (\Throwable $e) {
            Log::error('AiComparisonService error: ' . $e->getMessage());
            return 'قارن بين الخيارين حسب الجدول أعلاه واختر الأنسب لك.';
        }
    }

    protected function toJson($data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
