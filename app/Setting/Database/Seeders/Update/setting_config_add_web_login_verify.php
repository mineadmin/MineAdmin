<?php

declare(strict_types=1);

use Hyperf\Database\Seeders\Seeder;
use Hyperf\DbConnection\Db;

class SettingConfigAddWebLoginVerify extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $tableName = env('DB_PREFIX') . \App\Setting\Model\SettingConfig::getModel()->getTable();
        $sql = "INSERT INTO `{$tableName}` VALUES ('web_login_verify', '0', '后台登录验证码方式', 'extend', 0, '0 前端验证，1 后端验证')";
        try {
            Db::beginTransaction();
            Db::insert($sql);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollBack();
        }
    }
}
