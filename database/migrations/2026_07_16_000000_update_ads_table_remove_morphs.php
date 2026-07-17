<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->dropIndex('ads_advertable_type_advertable_id_index');
            $table->dropColumn(['advertable_id', 'advertable_type']);
        });
    }

    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->morphs('advertable');
        });
    }
};
