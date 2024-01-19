<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */
use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

class UpdateVersion101 extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('setting_generate_columns', function (Blueprint $table) {
            if (! Schema::hasColumn('setting_generate_columns', 'is_sort')) {
                $table->addColumn('smallInteger', 'is_sort')
                    ->comment('1 不排序 2 排序字段')
                    ->default(1)
                    ->after('is_query')
                    ->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setting_generate_columns', function (Blueprint $table) {
            $table->dropColumn(['is_sort']);
        });
    }
}
