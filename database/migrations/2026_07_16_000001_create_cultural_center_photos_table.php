<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cultural_center_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cultural_center_id')->constrained()->cascadeOnDelete();
            $table->string('photo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cultural_center_photos');
    }
};
