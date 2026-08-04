<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * فهارس على مسارات الاستعلام الساخنة + قيود فرادة تمنع الازدواج على مستوى قاعدة البيانات
 * (خطّ دفاع ثانٍ لا يعتمد على فحص التطبيق).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->index(['activity_id', 'status']);
            // منع الحجز المزدوج لنفس المستخدم على نفس النشاط
            $table->unique(['user_id', 'activity_id']);
        });

        Schema::table('venue_reservations', function (Blueprint $table) {
            $table->index(['venue_id', 'status', 'start_time', 'end_time']);
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->index(['start_time', 'end_time']);
        });

        Schema::table('ratings', function (Blueprint $table) {
            // تقييم واحد لكل مستخدم لكل عنصر
            $table->unique(['user_id', 'rateable_type', 'rateable_id']);
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'activity_id']);
            $table->dropIndex(['activity_id', 'status']);
        });

        Schema::table('venue_reservations', function (Blueprint $table) {
            $table->dropIndex(['venue_id', 'status', 'start_time', 'end_time']);
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex(['start_time', 'end_time']);
        });

        Schema::table('ratings', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'rateable_type', 'rateable_id']);
        });
    }
};
