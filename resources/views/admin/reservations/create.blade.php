@extends('layouts.app')

@section('title', 'تأكيد الحجز')

@section('content')
<div class="container-fluid px-4 py-8" dir="rtl">
    <div class="max-w-xl mx-auto bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-1">حجز مقاعد: {{ $activity->title }}</h2>
        <p class="text-sm text-gray-500 mb-6">الحد الأقصى المسموح به لهذا النشاط: {{ $activity->max_seats_per_user ?? 5 }} مقاعد</p>

        @if ($errors->any())
            <div class="bg-red-50 border-r-4 border-red-500 text-red-700 p-4 rounded-lg mb-6 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="bookingForm" method="POST" action="{{ route('reservations.store') }}" class="space-y-5">
            @csrf
            
            <input type="hidden" name="activity_id" value="{{ $activity->id }}">
            @if($activity->venue_id)
                <input type="hidden" name="venue_id" value="{{ $activity->venue_id }}">
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الحجز</label>
                <input 
                    type="date" 
                    name="reservation_date" 
                    value="{{ old('reservation_date', date('Y-m-d')) }}" 
                    min="{{ date('Y-m-d') }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    required
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">عدد المقاعد المطلوبة</label>
                <input 
                    type="number" 
                    name="seats_count" 
                    min="1" 
                    max="10" 
                    value="{{ old('seats_count', 1) }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    required
                >
            </div>

            <div class="flex items-start gap-3 pt-2">
                <input 
                    type="checkbox" 
                    id="allow_partial" 
                    name="allow_partial" 
                    value="1" 
                    {{ old('allow_partial') ? 'checked' : '' }}
                    class="mt-1 w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                >
                <label for="allow_partial" class="text-sm text-gray-600 cursor-pointer select-none">
                    <span class="font-medium text-gray-800 block">السماح بالتجزئة</span>
                    في حال عدم توفر كافة المقاعد، سيتم تأكيد المتاح وإدراج المقاعد المتبقية في قائمة الانتظار.
                </label>
            </div>

            <div class="pt-4 flex gap-3">
                <button 
                    type="submit" 
                    id="submitBtn"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-lg font-medium transition shadow-sm"
                >
                    تأكيد طلب الحجز
                </button>
                <a 
                    href="{{ url()->previous() }}" 
                    class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition text-center"
                >
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('bookingForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerText = 'جاري الحجز...';

        const idempotencyInput = document.createElement('input');
        idempotencyInput.type = 'hidden';
        idempotencyInput.name = '_idempotency_key';
        idempotencyInput.value = crypto.randomUUID();
        this.appendChild(idempotencyInput);
    });
</script>
@endsection