<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('volunteering_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cultural_center_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('location');
            $table->dateTime('start_time');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('volunteering_activities');
    }
};
