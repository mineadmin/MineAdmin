<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MenuSeeder20240926::class,
            UserSeeder20240926::class,
            MenuUpdate20241029::class,
            MenuUpdate20241031::class,
            UserDept20250310::class,
        ]);
    }
}
