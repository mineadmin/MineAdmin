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

        Schema::table('setting_generate_tables', function (Blueprint $table) {
            if (!Schema::hasColumn('setting_generate_tables', 'generate_menus')) {
                $table->addColumn('string', 'generate_menus', ['length' => 255])
                    ->comment('生成菜单列表')
                    ->after('generate_type')
                    ->nullable();
            }
            if (!Schema::hasColumn('setting_generate_tables', 'build_menu')) {
                $table->addColumn('char', 'build_menu', ['length' => 1, 'default' => '0'])
                    ->comment('是否构建菜单')
                    ->after('generate_type')
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

        Schema::table('setting_generate_tables', function (Blueprint $table) {
            $table->dropColumn(['generate_menus', 'build_menu']);
        });

        Schema::table('setting_generate_columns', function (Blueprint $table) {
            $table->dropColumn(['allow_roles', 'options']);
        });
    }
}
