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
        ];
    }
}
