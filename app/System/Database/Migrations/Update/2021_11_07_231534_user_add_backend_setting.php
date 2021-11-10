<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class UserAddBackendSetting extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('system_user', function (Blueprint $table) {
            if (! Schema::hasColumn('system_user','backend_setting')) {
                $table->addColumn('string', 'backend_setting', ['length' => 500])
                    ->comment('后台设置数据')
                    ->after('login_time')
                    ->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_user', function (Blueprint $table) {
            $table->dropColumn(['backend_setting']);
        });
    }
}
