<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActivityResource;
use App\Services\AiRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AiRecommendationController extends Controller
{
    protected AiRecommendationService $recommender;

    public function __construct(AiRecommendationService $recommender)
    {
        $this->recommender = $recommender;
    }

    public function forYou(Request $request)
    {
        $limit = (int) $request->query('limit', 5);
        $limit = max(1, min($limit, 10));
        $user  = $request->user();

        // كاش لكل مستخدم لمدة 6 ساعات - يمنع استدعاء الموديل بكل مرة يفتح فيها الهوم
        // ويقلل التكلفة بشكل كبير. أضف ?refresh=1 لإجبار تحديث فوري.
        $cacheKey = "ai_recommendations:user:{$user->id}:limit:{$limit}";

        if ($request->boolean('refresh')) {
            Cache::forget($cacheKey);
        }

        $data = Cache::remember($cacheKey, now()->addHours(6), function () use ($user, $limit) {
            $results = $this->recommender->recommend($user, $limit);

            return collect($results)
                ->map(fn ($item) => [
                    'reason'   => $item['reason'],
                    'activity' => (new ActivityResource($item['activity']))->resolve(),
                ])
                // ✅ حاسم: map() تحافظ على مفاتيح المصفوفة الأصلية (قد تكون
                // غير متسلسلة بسبب array_filter() بخدمة التوصيات). بدون
                // values() لإعادة الترقيم من الصفر، PHP يُرمِّز أي مصفوفة
                // بمفاتيح غير متسلسلة كـ JSON Object {} بدل Array []
                // بشكل متذبذب حسب أي عنصر استُبعد بالتصفية تحديداً — وهذا
                // بالضبط سبب فشل الفلاتر أحياناً بخطأ type cast.
                ->values();
        });

        return response()->json(['data' => $data]);
    }
}