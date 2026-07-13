<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            
            // البيانات الشخصية الأساسية
            $table->string('name');                             // الاسم بالكامل
            $table->date('date_of_birth');                      // تاريخ الميلاد
            $table->string('gender');                           // الجنس (ذكر / أنثى)
            $table->string('avatar')->nullable();               // الصورة الشخصية (nullable لأنها اختيارية)
            
            // بيانات التوثيق والتسجيل
            $table->string('phone')->unique();

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};