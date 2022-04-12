<?php

declare(strict_types=1);

use Hyperf\Database\Seeders\Seeder;
use Hyperf\DbConnection\Db;

class UpdateVersion063 extends Seeder
{
    /**
     * Run the database seeds.
     * 0.6.2版本升级SQL
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        try {
            Db::beginTransaction();
            foreach ($this->getCrontab() as $menu) {
                Db::insert($menu);
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollBack();
        }
    }

    protected function getCrontab(): array
    {
        $tableName = env('DB_PREFIX') . \App\Setting\Model\SettingCrontab::getModel()->getTable();
        return [
            "INSERT INTO `{$tableName}`(`id`, `name`, `type`, `target`, `parameter`, `rule`, `singleton`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `remark`) VALUES (10794906676384, '每月1号清理所有日志', '2', 'App\\System\\Crontab\\ClearLogCrontab', '', '0 0 0 1 * *', '1', '1', NULL, NULL, '2022-04-11 11:15:48', '2022-04-11 11:15:48', '')"
        ];
    }
}
