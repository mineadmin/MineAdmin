<?php

declare(strict_types=1);

use Hyperf\Database\Seeders\Seeder;
use Hyperf\DbConnection\Db;

class SystemDeptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function run()
    {
        $id = snowflake_id();
        Db::table('system_dept')->truncate();
        Db::table('system_dept')->insert(
            [
                'id' => $id,
                'parent_id' => 0,
                'level' => '0',
                'name' => '曼艺科技',
                'leader' => '曼艺',
                'phone' => '16888888888',
                'created_by' => env('SUPER_ADMIN', 1),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );
        Db::table('system_user')
            ->where('id', env('SUPER_ADMIN', 1))
            ->update(['dept_id' => $id]);
    }
}
