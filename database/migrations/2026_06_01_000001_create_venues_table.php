<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cultural_center_id')->constrained('cultural_centers')->onDelete('cascade');
            
            // الربط بجدول venue_types
            $table->foreignId('venue_type_id')->constrained('venue_types')->onDelete('restrict');
            
            $table->string('name');
            $table->integer('capacity');
            $table->json('features')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};