<?php

declare(strict_types=1);

use App\Setting\Model\SettingConfig;
use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class UpdateSettingConfigTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('setting_config')) {
            Schema::table('setting_config', function (Blueprint $table) {
                $table->addColumn('json', 'config_select_data', ['comment' => '配置选项数据'])->nullable()->change();
            });
            SettingConfig::where([
                'group_id' => 2,
                'key' => 'upload_mode'
            ])->update([
                'config_select_data' => json_encode([
                    ['label' => '本地上传', 'value' => '1'],
                    ['label' => '阿里云OSS', 'value' => '2'],
                    ['label' => '七牛云', 'value' => '3'],
                    ['label' => '腾讯云COS', 'value' => '4'],
                ], JSON_UNESCAPED_UNICODE)
            ]);
        }
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('setting_config')) {
            Schema::table('setting_config', function (Blueprint $table) {
                $table->addColumn('string', 'config_select_data', ['length' => 500, 'comment' => '配置选项数据'])->nullable()->change();
                
            });
            SettingConfig::where([
                'group_id' => 2,
                'key' => 'upload_mode'
            ])->update([
                'config_select_data' => '[{"label":"本地上传","value":"1"},{"label":"阿里云OSS","value":"2"},{"label":"七牛云","value":"3"},{"label":"腾讯云COS","value":"4"}]'
            ]);
        }
    }
}
