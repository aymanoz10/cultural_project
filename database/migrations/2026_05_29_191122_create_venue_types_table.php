<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venue_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // رمز مميز بالإنجليزية مثل: hall, theater, workshop_room
            $table->string('name'); // الاسم المعروض بالعربية مثل: قاعة عامة، مسرح
            $table->text('description')->nullable(); // وصف اختياري لنوع القاعة
            $table->boolean('is_active')->default(true); // تفعيل / تعطيل النوع
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venue_types');
    }
};