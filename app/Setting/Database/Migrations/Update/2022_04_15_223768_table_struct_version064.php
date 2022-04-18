<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class TableStructVersion064 extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('setting_config', function (Blueprint $table) {
            $table->string('key', 32)->change();
        });

        Schema::table('setting_generate_columns', function (Blueprint $table) {
            if (! Schema::hasColumn('setting_generate_columns','allow_roles')) {
                $table->addColumn('string', 'allow_roles', ['length' => 255])
                    ->comment('允许查看该字段的角色')
                    ->after('dict_type')
                    ->nullable();
            }
            if (! Schema::hasColumn('setting_generate_columns','options')) {
                $table->addColumn('string', 'options', ['length' => 1000])
                    ->comment('字段其他设置')
                    ->after('allow_roles')
                    ->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setting_config', function (Blueprint $table) {
            $table->string('key', 255)->change();
        });

        Schema::table('setting_generate_columns', function (Blueprint $table) {
            $table->dropColumn(['allow_roles', 'options']);
        });
    }
}
