<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cultural_center_id')->constrained()->onDelete('cascade');
            $table->foreignId('activity_type_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('venue_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('presenter_name')->nullable();
            $table->string('presenter_avatar')->nullable();
            $table->text('description');
            $table->decimal('ticket_price', 10, 2)->nullable();
            $table->integer('capacity')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
