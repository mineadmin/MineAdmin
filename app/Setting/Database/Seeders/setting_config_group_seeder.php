<?php
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

declare(strict_types=1);

use Hyperf\Database\Seeders\Seeder;
use Hyperf\DbConnection\Db;

class SettingConfigGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Db::table('setting_config_group')->truncate();
        $tableName = env('DB_PREFIX') . \App\Setting\Model\SettingConfig::getModel()->getTable();
        $sql = [
            "INSERT INTO `{$tableName}`(`id`, `name`, `code`, `created_by`, `updated_by`, `created_at`, `updated_at`, `remark`) VALUES (1, '站点配置', 'site_config', 1, 1, '2022-07-23 15:08:44', '2022-07-23 15:08:44', NULL)",
            "INSERT INTO `{$tableName}`(`id`, `name`, `code`, `created_by`, `updated_by`, `created_at`, `updated_at`, `remark`) VALUES (2, '上传配置', 'upload_config', 1, 1, '2022-07-23 15:09:31', '2022-07-23 15:09:33', NULL)",

        ];
        foreach ($sql as $item) {
            Db::insert($item);
        }
    }
}
