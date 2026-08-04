<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\Admin;
use App\Models\CulturalCenter;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedUsers();
        $this->seedActivityTypes();
        $this->seedCenters();
        $this->seedVenues();
        $this->seedActivities();
    }

    private function seedUsers(): void
    {
        Admin::firstOrCreate(
            ['phone' => '01000000000'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password'),
                'role'     => 'super',
            ]
        );

        User::firstOrCreate(
            ['phone' => '01111111111'],
            [
                'name'         => 'Test User',
                'date_of_birth'=> '1998-05-15',
                'gender'       => 'male',
            ]
        );
    }

    private function seedActivityTypes(): void
    {
        $types = [
            ['title' => 'ورشة عمل',     'description' => 'ورش تعليمية تفاعلية'],
            ['title' => 'محاضرة',       'description' => 'محاضرات وندوات ثقافية'],
            ['title' => 'حفل موسيقي',   'description' => 'حفلات وموسيقى حية'],
            ['title' => 'معرض فني',     'description' => 'معارض الفنون'],
            ['title' => 'عرض مسرحي',    'description' => 'عروض مسرحية'],
        ];

        foreach ($types as $type) {
            ActivityType::firstOrCreate(['title' => $type['title']], $type);
        }
    }

    private function seedCenters(): void
    {
        $centers = [
            [
                'name'         => 'مركز أدهم إسماعيل للفنون',
                'location'     => 'دمشق - أبو رمانة',
                'map_location' => 'https://maps.google.com/?q=33.5138,36.2765',
                'description'  => 'مركز متخصص في الفنون التشكيلية والموسيقية',
                'features'     => ['مكتبة', 'قاعة عرض', 'استوديو موسيقى'],
            ],
            [
                'name'         => 'دار الأسد للثقافة والفنون',
                'location'     => 'دمشق - كفرسوسة',
                'map_location' => 'https://maps.google.com/?q=33.5075,36.2939',
                'description'  => 'مسرح ودار أوبرا حديثة',
                'features'     => ['مسرح', 'قاعات مؤتمرات'],
            ],
            [
                'name'         => 'مركز الميدان الثقافي',
                'location'     => 'حلب - الميدان',
                'map_location' => 'https://maps.google.com/?q=36.2021,37.1343',
                'description'  => 'مركز ثقافي شعبي',
                'features'     => ['مكتبة', 'قاعة محاضرات'],
            ],
        ];

        foreach ($centers as $center) {
            CulturalCenter::firstOrCreate(['name' => $center['name']], $center);
        }
    }

    private function seedVenues(): void
    {
        $centers = CulturalCenter::all();

        foreach ($centers as $index => $center) {
            $venueCount = ($index === 0) ? 2 : 1;

            for ($i = 1; $i <= $venueCount; $i++) {
                Venue::firstOrCreate(
                    ['cultural_center_id' => $center->id, 'name' => "قاعة {$center->name} - {$i}"],
                    [
                        'type'     => $i % 2 === 0 ? 'theater' : 'hall',
                        'capacity' => 50 + ($i * 20),
                        'features' => ['تكييف', 'صوتيات'],
                    ]
                );
            }
        }
    }

    private function seedActivities(): void
    {
        $centers = CulturalCenter::all();
        $types   = ActivityType::all();
        $venues  = Venue::all();

        if ($centers->isEmpty() || $types->isEmpty() || $venues->isEmpty()) {
            return;
        }

        $titles = [
            'مقدمة في التصوير الفوتوغرافي',
            'محاضرة: تاريخ الموسيقى العربية',
            'حفل موسيقي للفرقة الوطنية',
            'معرض فنون تشكيلية',
            'ورشة الخط العربي',
            'عرض مسرحي: حكايات من التراث',
            'ندوة حول الرواية السورية',
            'أمسية شعرية',
            'ورشة الرسم للأطفال',
            'محاضرة عن الفن الإسلامي',
            'حفل عزف على العود',
            'معرض صور فوتوغرافية',
            'ورشة النحت على الخشب',
            'عرض للأفلام القصيرة',
            'محاضرة: الذكاء الاصطناعي والثقافة',
            'حفل جاز',
            'ورشة الطبخ التراثي',
            'معرض الخط والزخرفة',
            'عرض مسرحي للأطفال',
            'محاضرة عن الحضارة السورية',
            'أمسية موسيقية شعبية',
            'ورشة صناعة الفخار',
            'ندوة عن المسرح العربي',
            'حفل كورال',
            'معرض الكتاب المستعمل',
        ];

        $presenters = [
            'أحمد علي',
            'د. سامر حداد',
            'الفرقة الوطنية',
            'الفنانة لينا عثمان',
            'د. ريم الحلبي',
            'ميساء الخطيب',
            'عبد الرحمن عثمان',
            'نورا شهاب',
            'باسل يوسف',
            'غادة المصري',
        ];

        // 50 فعالية: أول 15 في الماضي (منتهية)، والباقي في المستقبل (قادمة)
        $total    = 50;
        $finished = 15;

        foreach (range(1, $total) as $i) {
            $center = $centers->get(($i - 1) % $centers->count());
            $type   = $types->get(($i - 1) % $types->count());
            $venue  = $venues->get(($i - 1) % $venues->count());

            $offset = $i <= $finished
                ? -($i * 3)                // الماضي: -3, -6, -9 ... -45 يوم
                : (($i - $finished) * 2);  // المستقبل: 2, 4, 6 ... 70 يوم

            $baseDate = now()->startOfDay()->addDays($offset)->setHour(10)->setMinute(0);

            $title = $titles[($i - 1) % count($titles)];
            $presenter = $presenters[($i - 1) % count($presenters)];

            Activity::firstOrCreate(
                ['title' => $title . ' (' . $i . ')'],
                [
                    'cultural_center_id' => $center->id,
                    'activity_type_id'   => $type->id,
                    'venue_id'           => $venue->id,
                    'presenter_name'     => $presenter,
                    'description'        => 'وصف الفعالية رقم ' . $i,
                    'ticket_price'       => $i % 3 === 0 ? null : 25 * ($i % 10),
                    'capacity'           => 20 + ($i % 5) * 10,
                    'start_time'         => $baseDate,
                    'end_time'           => $baseDate->copy()->addHours(1 + ($i % 4)),
                ]
            );
        }
    }
}
