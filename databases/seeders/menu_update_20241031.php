<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */
use App\Model\Permission\Menu;
use Hyperf\Database\Seeders\Seeder;
use Hyperf\DbConnection\Db;

class MenuUpdate20241031 extends Seeder
{
    public const BASE_DATA = [
        'name' => '',
        'path' => '',
        'component' => '',
        'redirect' => '',
        'created_by' => 0,
        'updated_by' => 0,
        'remark' => '',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (env('DB_DRIVER') === 'odbc-sql-server') {
            Db::unprepared('SET IDENTITY_INSERT [' . Menu::getModel()->getTable() . '] ON;');
        }
        $this->create($this->data());
        if (env('DB_DRIVER') === 'odbc-sql-server') {
            Db::unprepared('SET IDENTITY_INSERT [' . Menu::getModel()->getTable() . '] OFF;');
        }
    }

    public function data(): array
    {
        return [
            'permission:get:role' => 'permission:role:getMenu',
            'permission:set:role' => 'permission:role:setMenu',
            'user:get:roles' => 'permission:user:getRole',
            'user:set:roles' => 'permission:user:setRole',
        ];
    }

    public function create(array $data): void
    {
        foreach ($data as $originValue => $newValue) {
            Menu::query()->where('name', $originValue)->update(['name' => $newValue]);
        }
    }
}
