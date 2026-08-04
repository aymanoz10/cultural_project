<?php

namespace Database\Seeders;

use App\Models\VenueType;
use Illuminate\Database\Seeder;

class VenueTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['code' => 'hall', 'name' => 'قاعة عامة', 'description' => 'قاعة متعددة الاستخدامات'],
            ['code' => 'theater', 'name' => 'مسرح', 'description' => 'مساحة مجهزة بعرض مسرحي ومدرجات'],
            ['code' => 'workshop_room', 'name' => 'غرفة ورش عمل', 'description' => 'مساحة صغيرة مخصصة للورش والتدريب'],
            ['code' => 'outdoor', 'name' => 'مساحة خارجية / هواءطلق', 'description' => 'حدائق ومساحات مكشوفة'],
        ];

        foreach ($types as $type) {
            VenueType::updateOrCreate(['code' => $type['code']], $type);
        }
    }
}