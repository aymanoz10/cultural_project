<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('ticket_id')->unique();
            $table->text('qr_code')->nullable();
            $table->foreignId('hall_id')->nullable()->constrained();
            $table->foreignId('theater_id')->nullable()->constrained();
            $table->foreignId('activity_id')->constrained();
            $table->foreignId('library_id')->nullable()->constrained();
            $table->date('reservation_date');
            $table->string('status')->default('confirmed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
