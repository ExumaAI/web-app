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
        Schema::table('settings_two', function (Blueprint $table) {
            $table->boolean('chatbot_show_timestamp')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings_two', function (Blueprint $table) {
            $table->dropColumn('chatbot_show_timestamp');
        });
    }
};
