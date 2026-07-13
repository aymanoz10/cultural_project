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
        Schema::create('ratings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    
    // التقييم: يجب أن يكون رقمياً من 1 إلى 5
    $table->unsignedTinyInteger('value'); 
    $table->text('comment')->nullable();
    
    // الـ Morph لربط التقييم بأي "هدف" (نشاط، مركز، كتاب)
    $table->morphs('rateable'); 
    
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
