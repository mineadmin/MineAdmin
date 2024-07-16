<?php

declare(strict_types=1);

use Hyperf\Database\Seeders\Seeder;
use Hyperf\DbConnection\Db;

class SystemUserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Db::table('system_user_role')->truncate();
        Db::table('system_user_role')->insert([
            'user_id' => 1,
            'role_id' => 1
        ]);
    }
}
