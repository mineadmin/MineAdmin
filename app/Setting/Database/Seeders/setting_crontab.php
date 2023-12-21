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
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */
use Hyperf\Database\Seeders\Seeder;
use Hyperf\DbConnection\Db;

class SettingCrontab extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Db::table('setting_crontab')->truncate();
        foreach ($this->getData() as $item) {
            Db::insert($item);
        }
    }

    public function getData(): array
    {
        $tableName = env('DB_PREFIX') . \App\Setting\Model\SettingCrontab::getModel()->getTable();
        if (env('DB_DRIVER') == 'pgsql') {
            Db::select("SELECT setval('{$tableName}_id_seq', 2)");
            return [
                "INSERT INTO \"{$tableName}\" VALUES (1, 'urlCrontab', '3', 'http://127.0.0.1:9501/', '', '59 */1 * * * *', 2, 2, NULL, NULL, '2021-08-07 23:28:28', '2021-08-07 23:44:55', '请求127.0.0.1')",
                "INSERT INTO \"{$tableName}\" VALUES (2, '每月1号清理所有日志', '2', 'App\\System\\Crontab\\ClearLogCrontab', '', '0 0 0 1 * *', 2, 2, NULL, NULL, '2022-04-11 11:15:48', '2022-04-11 11:15:48', '')",
            ];
        }
        return [
            "INSERT INTO `{$tableName}` VALUES (1, 'urlCrontab', '3', 'http://127.0.0.1:9501/', '', '59 */1 * * * *', 2, 2, NULL, NULL, '2021-08-07 23:28:28', '2021-08-07 23:44:55', '请求127.0.0.1')",
            "INSERT INTO `{$tableName}` VALUES (2, '每月1号清理所有日志', '2', 'App\\System\\Crontab\\ClearLogCrontab', '', '0 0 0 1 * *', 2, 2, NULL, NULL, '2022-04-11 11:15:48', '2022-04-11 11:15:48', '')",
        ];
    }
}
