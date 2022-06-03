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

class SettingConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Db::table('setting_config')->truncate();
        $tableName = env('DB_PREFIX') . \App\Setting\Model\SettingConfig::getModel()->getTable();
        $sql = [
            "INSERT INTO `{$tableName}` VALUES ('web_close', '1', '网站是否关闭', 'extend', 0, '关闭网站后将无法访问')",
            "INSERT INTO `{$tableName}` VALUES ('site_copyright', NULL, '版权信息', 'system', 96, NULL)",
            "INSERT INTO `{$tableName}` VALUES ('site_desc', NULL, '网站描述', 'system', 97, NULL)",
            "INSERT INTO `{$tableName}` VALUES ('site_keywords', NULL, '网站关键字', 'system', 98, NULL)",
            "INSERT INTO `{$tableName}` VALUES ('site_name', NULL, '网站名称', 'system', 99, NULL)",
            "INSERT INTO `{$tableName}` VALUES ('site_record_number', NULL, '网站备案号', 'system', 95, NULL)",
            "INSERT INTO `{$tableName}` VALUES ('site_storage_mode', 'local', '上传存储模式', 'system', 93, NULL)",
            "INSERT INTO `{$tableName}` VALUES ('web_login_verify', '0', '后台登录验证码方式', 'extend', 0, '0 前端验证，1 后端验证')"
        ];
        foreach ($sql as $item) {
            Db::insert($item);
        }
    }
}
