<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('volunteerings', function (Blueprint $table) {
            $table->dropForeign(['volunteering_activity_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'volunteering_activity_id', 'form_data']);

            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('whatsapp_number');
            $table->date('birthday_date');
            $table->string('address');
            $table->string('education_level');
            $table->boolean('has_volunteered_before')->default(false);
            $table->text('previous_experiences')->nullable();
            $table->text('why_volunteer');
            $table->string('volunteering_interest');
            $table->string('tools')->nullable();
            $table->string('center');
            $table->string('available_times');
            $table->text('notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('volunteerings', function (Blueprint $table) {
            $table->dropColumn([
                'first_name', 'last_name', 'email', 'whatsapp_number',
                'birthday_date', 'address', 'education_level',
                'has_volunteered_before', 'previous_experiences',
                'why_volunteer', 'volunteering_interest', 'tools',
                'center', 'available_times', 'notes',
            ]);

            $table->foreignId('volunteering_activity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('form_data')->nullable();
        });
    }
};
