<!DOCTYPE html>
<html lang="ar" dir="rtl"
      xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:w="urn:schemas-microsoft-com:office:word"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
  <meta charset="utf-8">
  <title>تقرير منظومة الثقافة {{ $from->format('Y-m-d') }} — {{ $to->format('Y-m-d') }}</title>
  @php
    $vrLabels = ['pending' => 'قيد الانتظار', 'accepted' => 'مقبول', 'approved' => 'معتمد', 'rejected' => 'مرفوض', 'cancelled' => 'ملغى'];
    $trLabels = ['CONFIRMED' => 'مؤكّد', 'WAIT_LIST' => 'قائمة انتظار', 'CANCELLED' => 'ملغى'];
    $s = $report['summary'];
    $scopeName = $is_super ? 'كل المراكز الثقافية' : ($center->name ?? 'غير محدد');
  @endphp
  <style>
    * { box-sizing: border-box; }
    body { font-family: 'Tajawal', 'Cairo', 'Arial', sans-serif; direction: rtl; color: #1a1a1a; margin: 24px; font-size: 13px; line-height: 1.6; }
    h1, h2, h3 { margin: 0; }
    .doc-header { border-bottom: 3px solid #0F4C3A; padding-bottom: 14px; margin-bottom: 18px; }
    .doc-header .brand { color: #0F4C3A; font-size: 15px; font-weight: 800; }
    .doc-header .title { font-size: 22px; font-weight: 900; margin-top: 6px; color: #0F4C3A; }
    .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .meta-table td { padding: 4px 8px; font-size: 12px; }
    .meta-table .lbl { color: #64748b; font-weight: 700; width: 90px; }
    .meta-table .val { font-weight: 800; color: #0f172a; }
    .section-title { font-size: 15px; font-weight: 900; color: #0F4C3A; border-right: 4px solid #0F4C3A; padding-right: 8px; margin: 22px 0 10px; }
    .summary { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
    .summary td { border: 1px solid #cbd5e1; padding: 10px; text-align: center; width: 16.6%; }
    .summary .num { font-size: 20px; font-weight: 900; color: #0F4C3A; }
    .summary .cap { font-size: 11px; color: #475569; font-weight: 700; }
    table.data { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
    table.data th { background: #0F4C3A; color: #fff; padding: 8px; font-size: 12px; text-align: right; border: 1px solid #0F4C3A; }
    table.data td { border: 1px solid #cbd5e1; padding: 7px 8px; font-size: 12px; }
    table.data tr:nth-child(even) td { background: #f1f5f9; }
    .two-col { width: 100%; }
    .two-col td { vertical-align: top; width: 50%; padding-left: 10px; }
    .muted { color: #94a3b8; font-size: 12px; padding: 8px; }
    .doc-footer { margin-top: 28px; border-top: 1px solid #cbd5e1; padding-top: 10px; font-size: 11px; color: #64748b; text-align: center; }
    .print-bar { background: #0F4C3A; color: #fff; padding: 12px 16px; text-align: center; margin-bottom: 18px; border-radius: 8px; }
    .print-bar button { background: #fff; color: #0F4C3A; border: 0; font-weight: 800; padding: 8px 18px; border-radius: 6px; cursor: pointer; font-size: 13px; }
    @media print { .no-print { display: none !important; } body { margin: 0; } @page { size: A4; margin: 1.4cm; } }
  </style>
</head>
<body>

  @if($mode === 'pdf')
    <div class="print-bar no-print">
      لحفظ التقرير كـ PDF اختر «الوجهة: حفظ كـ PDF» في حوار الطباعة.
      &nbsp; <button onclick="window.print()">طباعة / حفظ PDF</button>
    </div>
  @endif

  <!-- الترويسة -->
  <div class="doc-header">
    <div class="brand">منظومة الثقافة — مديرية الثقافة بدمشق</div>
    <div class="title">تقرير {{ $is_super ? 'عام' : 'المركز' }} للنشاطات والحجوزات</div>
  </div>

  <table class="meta-table">
    <tr>
      <td class="lbl">النطاق:</td><td class="val">{{ $scopeName }}</td>
      <td class="lbl">الفترة:</td><td class="val">{{ $from->format('Y-m-d') }} — {{ $to->format('Y-m-d') }}</td>
    </tr>
    <tr>
      <td class="lbl">أُنشئ في:</td><td class="val">{{ $generated_at->format('Y-m-d H:i') }}</td>
      <td class="lbl">بواسطة:</td><td class="val">{{ $admin->name ?? '—' }}</td>
    </tr>
  </table>

  <!-- الملخّص -->
  <div class="section-title">ملخّص التقرير</div>
  <table class="summary">
    <tr>
      <td><div class="num">{{ number_format($s['activities']) }}</div><div class="cap">الفعاليات</div></td>
      <td><div class="num">{{ number_format($s['venue_reservations']) }}</div><div class="cap">حجوزات القاعات</div></td>
      <td><div class="num">{{ number_format($s['ticket_reservations']) }}</div><div class="cap">حجوزات التذاكر</div></td>
      <td><div class="num">{{ number_format($s['ticket_seats']) }}</div><div class="cap">المقاعد المحجوزة</div></td>
      <td><div class="num">{{ number_format($s['books_added']) }}</div><div class="cap">كتب مضافة</div></td>
      <td><div class="num">{{ number_format($s['centers']) }}</div><div class="cap">{{ $is_super ? 'المراكز' : 'المركز' }}</div></td>
    </tr>
  </table>

  <!-- التوزيعات -->
  <table class="two-col">
    <tr>
      <td>
        <div class="section-title">الفعاليات حسب النوع</div>
        <table class="data">
          <tr><th>النوع</th><th style="width:70px;text-align:center">العدد</th></tr>
          @forelse($report['activities_by_type'] as $type => $count)
            <tr><td>{{ $type }}</td><td style="text-align:center">{{ $count }}</td></tr>
          @empty
            <tr><td colspan="2" class="muted">لا توجد فعاليات ضمن المدة.</td></tr>
          @endforelse
        </table>
      </td>
      <td>
        @if($is_super)
          <div class="section-title">الفعاليات حسب المركز</div>
          <table class="data">
            <tr><th>المركز</th><th style="width:70px;text-align:center">العدد</th></tr>
            @forelse($report['activities_by_center'] as $c => $count)
              <tr><td>{{ $c }}</td><td style="text-align:center">{{ $count }}</td></tr>
            @empty
              <tr><td colspan="2" class="muted">لا توجد بيانات.</td></tr>
            @endforelse
          </table>
        @endif

        <div class="section-title">حجوزات القاعات حسب الحالة</div>
        <table class="data">
          <tr><th>الحالة</th><th style="width:70px;text-align:center">العدد</th></tr>
          @forelse($report['venue_res_by_status'] as $status => $count)
            <tr><td>{{ $vrLabels[$status] ?? $status }}</td><td style="text-align:center">{{ $count }}</td></tr>
          @empty
            <tr><td colspan="2" class="muted">لا توجد.</td></tr>
          @endforelse
        </table>

        <div class="section-title">حجوزات التذاكر حسب الحالة</div>
        <table class="data">
          <tr><th>الحالة</th><th style="width:70px;text-align:center">العدد</th></tr>
          @forelse($report['ticket_res_by_status'] as $status => $count)
            <tr><td>{{ $trLabels[$status] ?? $status }}</td><td style="text-align:center">{{ $count }}</td></tr>
          @empty
            <tr><td colspan="2" class="muted">لا توجد.</td></tr>
          @endforelse
        </table>
      </td>
    </tr>
  </table>

  <!-- تفصيل الفعاليات -->
  <div class="section-title">تفصيل الفعاليات ضمن المدة ({{ number_format($report['activities']->count()) }})</div>
  <table class="data">
    <tr>
      <th>#</th>
      <th>الفعالية</th>
      <th>النوع</th>
      @if($is_super)<th>المركز</th>@endif
      <th>القاعة</th>
      <th>التاريخ</th>
    </tr>
    @forelse($report['activities'] as $i => $a)
      <tr>
        <td style="text-align:center">{{ $i + 1 }}</td>
        <td>{{ $a->title }}</td>
        <td>{{ $a->activityType->title ?? '—' }}</td>
        @if($is_super)<td>{{ $a->culturalCenter->name ?? '—' }}</td>@endif
        <td>{{ $a->venue->name ?? '—' }}</td>
        <td>{{ optional($a->start_time)->format('Y-m-d H:i') }}</td>
      </tr>
    @empty
      <tr><td colspan="{{ $is_super ? 6 : 5 }}" class="muted">لا توجد فعاليات ضمن المدة المحددة.</td></tr>
    @endforelse
  </table>

  <!-- تفصيل حجوزات القاعات -->
  <div class="section-title">تفصيل حجوزات القاعات ({{ number_format($report['venue_reservations']->count()) }})</div>
  <table class="data">
    <tr>
      <th style="width:28px;text-align:center">#</th><th>مقدّم الطلب</th><th>الرقم الوطني</th><th>القاعة</th>
      @if($is_super)<th>المركز</th>@endif<th>من</th><th>إلى</th><th>الحالة</th>
    </tr>
    @forelse($report['venue_reservations'] as $i => $vr)
      <tr>
        <td style="text-align:center">{{ $i + 1 }}</td>
        <td>{{ $vr->applicant_name ?: '—' }}</td>
        <td>{{ $vr->national_id_number }}</td>
        <td>{{ $vr->venue->name ?? '—' }}</td>
        @if($is_super)<td>{{ $vr->venue->culturalCenter->name ?? '—' }}</td>@endif
        <td>{{ optional($vr->start_time)->format('Y-m-d H:i') }}</td>
        <td>{{ optional($vr->end_time)->format('Y-m-d H:i') }}</td>
        <td>{{ $vrLabels[$vr->status] ?? $vr->status }}</td>
      </tr>
    @empty
      <tr><td colspan="{{ $is_super ? 8 : 7 }}" class="muted">لا توجد حجوزات قاعات ضمن المدة.</td></tr>
    @endforelse
  </table>

  <!-- تفصيل حجوزات التذاكر -->
  <div class="section-title">تفصيل حجوزات التذاكر ({{ number_format($report['ticket_reservations']->count()) }})</div>
  <table class="data">
    <tr>
      <th style="width:28px;text-align:center">#</th><th>رقم التذكرة</th><th>المستخدم</th><th>الفعالية</th>
      <th style="width:56px;text-align:center">المقاعد</th><th>التاريخ</th><th>الحالة</th>
    </tr>
    @forelse($report['ticket_reservations'] as $i => $tr)
      <tr>
        <td style="text-align:center">{{ $i + 1 }}</td>
        <td>{{ $tr->ticket_id }}</td>
        <td>{{ $tr->user->name ?? '—' }}</td>
        <td>{{ $tr->activity->title ?? '—' }}</td>
        <td style="text-align:center">{{ $tr->seats_count }}</td>
        <td>{{ $tr->reservation_date ?? optional($tr->created_at)->format('Y-m-d') }}</td>
        <td>{{ $trLabels[$tr->status] ?? $tr->status }}</td>
      </tr>
    @empty
      <tr><td colspan="7" class="muted">لا توجد حجوزات تذاكر ضمن المدة.</td></tr>
    @endforelse
  </table>

  <!-- تفصيل الكتب المضافة -->
  <div class="section-title">تفصيل الكتب المضافة ({{ number_format($report['books']->count()) }})</div>
  <table class="data">
    <tr>
      <th style="width:28px;text-align:center">#</th><th>العنوان</th><th>المؤلف</th><th>التصنيف</th>
      <th>المكتبة</th><th>الحالة</th><th>أُضيف في</th>
    </tr>
    @forelse($report['books'] as $i => $bk)
      <tr>
        <td style="text-align:center">{{ $i + 1 }}</td>
        <td>{{ $bk->title }}</td>
        <td>{{ $bk->author }}</td>
        <td>{{ $bk->category }}</td>
        <td>{{ $bk->library->name ?? '—' }}</td>
        <td>{{ $bk->is_available ? 'متاحة' : 'غير متاحة' }}</td>
        <td>{{ optional($bk->created_at)->format('Y-m-d') }}</td>
      </tr>
    @empty
      <tr><td colspan="7" class="muted">لا توجد كتب مضافة ضمن المدة.</td></tr>
    @endforelse
  </table>

  <!-- المكتبات + المراكز -->
  <table class="two-col">
    <tr>
      <td>
        <div class="section-title">المكتبات ({{ number_format($report['libraries']->count()) }})</div>
        <table class="data">
          <tr><th>المكتبة</th><th>المركز</th><th style="width:56px;text-align:center">الكتب</th></tr>
          @forelse($report['libraries'] as $lib)
            <tr><td>{{ $lib->name }}</td><td>{{ $lib->culturalCenter->name ?? '—' }}</td><td style="text-align:center">{{ $lib->books_count }}</td></tr>
          @empty
            <tr><td colspan="3" class="muted">لا توجد.</td></tr>
          @endforelse
        </table>
      </td>
      <td>
        <div class="section-title">المراكز الثقافية ({{ number_format($report['centers_detail']->count()) }})</div>
        <table class="data">
          <tr><th>المركز</th><th style="width:56px;text-align:center">القاعات</th><th style="width:56px;text-align:center">الفعاليات</th></tr>
          @forelse($report['centers_detail'] as $ctr)
            <tr><td>{{ $ctr->name }}</td><td style="text-align:center">{{ $ctr->venues_count }}</td><td style="text-align:center">{{ $ctr->activities_count }}</td></tr>
          @empty
            <tr><td colspan="3" class="muted">لا توجد.</td></tr>
          @endforelse
        </table>
      </td>
    </tr>
  </table>

  <div class="doc-footer">
    منظومة الثقافة — مديرية الثقافة بدمشق &nbsp;•&nbsp; تقرير أُنشئ آلياً في {{ $generated_at->format('Y-m-d H:i') }}
  </div>

  @if($mode === 'pdf')
    <script>window.addEventListener('load', function () { setTimeout(function () { window.print(); }, 400); });</script>
  @endif

</body>
</html>
