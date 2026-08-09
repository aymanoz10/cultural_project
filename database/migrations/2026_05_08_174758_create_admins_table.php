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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('avatar')->nullable();
            $table->string('phone')->unique();
            $table->string('password');
            $table->unsignedBigInteger('center_id')->nullable();
            $table->string('status')->default('active'); // active, pending, banned
            // 🟢 القيمة الافتراضية الآمنة (الأقل امتيازًا) بدل منح السوبر ضمنيًا
            $table->string('role')->default('ticketsAdmin'); // super | admin | ticketsAdmin
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
