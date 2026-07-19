<?php

namespace App\Http\Controllers;

use App\Models\Volunteering;
use App\Http\Resources\VolunteeringResource;
use Illuminate\Http\Request;

class VolunteeringController extends Controller
{
    private const EDUCATION_LEVELS = ['ثانوي', 'اعدادي', 'جامعي', 'بكالوريوس', 'ماجستير', 'دكتوراة'];

    private const VOLUNTEERING_INTERESTS = [
        'اعلام (تصميم - تصوير - مونتاج)',
        'المساعدة في الانشطة',
        'المشاركة في تنسيق الفعاليات',
        'تقديم التدريب للاطفال',
        'المشاركة في اقامة الورشات الفنية',
        'العلاقات العامة',
    ];

    private const TOOLS = ['كاميرا', 'لابتوب', 'لا يملك اي معدات'];

    private const CENTERS = [
        'فريق مديرية الثقافة',
        'مركز الميدان',
        'مركز ابو رمانة',
        'مركز كفرسوسة',
        'مركز العدوي',
        'مركز برزة',
    ];

    private const AVAILABLE_TIMES = [
        'اي وقت',
        'الجمعة والسبت',
        'من الاحد للخميس صباحاً',
        'من الاحد للخميس مساءاً',
        'اونلاين',
    ];

    public function add(Request $request)
    {
        $request->validate([
            'first_name'             => 'required|string|max:255',
            'last_name'              => 'required|string|max:255',
            'email'                  => 'required|email|max:255',
            'whatsapp_number'        => 'required|string|max:255',
            'birthday_date'          => 'required|date|before:today',
            'address'                => 'required|string|max:255',
            'education_level'        => 'required|string|max:255',
            'has_volunteered_before' => 'required|in:true,false,1,0',
            'previous_experiences'   => 'nullable|string',
            'why_volunteer'          => 'required|string',
            'volunteering_interest'  => 'required|string|max:255',
            'tools'                  => 'nullable|string|max:255',
            'center'                 => 'required|string|in:' . implode(',', self::CENTERS),
            'available_times'        => 'required|string|max:255',
            'notes'                  => 'nullable|string',
        ]);

        $exists = Volunteering::where('whatsapp_number', $request->whatsapp_number)
            ->whereIn('status', ['pending', 'accepted'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'لقد تقدمت مسبقاً بطلب تطوع'], 422);
        }

        $volunteering = Volunteering::create([
            'first_name'               => $request->first_name,
            'last_name'                => $request->last_name,
            'email'                    => $request->email,
            'whatsapp_number'          => $request->whatsapp_number,
            'birthday_date'            => $request->birthday_date,
            'address'                  => $request->address,
            'education_level'          => $request->education_level,
            'has_volunteered_before'   => $request->has_volunteered_before,
            'previous_experiences'     => $request->previous_experiences,
            'why_volunteer'            => $request->why_volunteer,
            'volunteering_interest'    => $request->volunteering_interest,
            'tools'                    => $request->tools,
            'center'                   => $request->center,
            'available_times'          => $request->available_times,
            'notes'                    => $request->notes,
            'status'                   => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال طلب التطوع بنجاح',
            'data'    => new VolunteeringResource($volunteering),
        ], 201);
    }

    public function index(Request $request)
    {
        $query = Volunteering::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('center')) {
            $query->where('center', $request->center);
        }

        return VolunteeringResource::collection($query->latest()->get());
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,accepted,rejected',
        ]);

        $volunteering = Volunteering::findOrFail($id);
        $volunteering->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الحالة بنجاح',
            'data'    => new VolunteeringResource($volunteering->fresh()),
        ]);
    }
}
