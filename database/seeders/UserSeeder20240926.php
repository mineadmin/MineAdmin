<?php

namespace Database\Seeders;

use App\Models\Permission\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder20240926 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::truncate();
        Role::truncate();
        DB::table('user_belongs_role')->delete();

        $entity = User::create([
            'username' => 'admin',
            'password' => '123456',
            'user_type' => '100',
            'nickname' => '创始人',
            'email' => 'admin@adminmine.com',
            'phone' => '16858888988',
            'signed' => '广阔天地，大有所为',
            'created_by' => 0,
            'updated_by' => 0,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $role = Role::create([
            'name' => '超级管理员',
            'code' => 'SuperAdmin',
        ]);
        $entity->roles()->sync($role);
    }
}
