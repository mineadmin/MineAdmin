<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class TableStructVersion061 extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('setting_crontab', function (Blueprint $table) {
            $table->string('rule', 32)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setting_crontab', function (Blueprint $table) {
            $table->string('rule', 15)->change();
        });
    }
}
