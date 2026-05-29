<?php

namespace Database\Seeders;

use App\Models\Permission\Menu;
use Illuminate\Database\Seeder;

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
        $this->create($this->data());
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
