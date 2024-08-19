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
use App\Setting\Model\SettingConfig;
use Hyperf\Database\Seeders\Seeder;

class SettingConfigSeederUpdate extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $setting = SettingConfig::query()->where([
            'key' => 'upload_mode',
            'group_id' => 2,
        ])->first();
        if ($setting && isset($setting->config_select_data) && is_string($setting->config_select_data)) {
            $setting->config_select_data = json_decode($setting->config_select_data, true);
            $setting->save();
        }
    }
}
