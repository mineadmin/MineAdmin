<?php

declare(strict_types=1);
/**
 * This file is part of the zhh/scoring plugin.
 *
 * 計分業務插件菜單與權限初始化（方案 §8.3）。
 *
 * 注意 `component` 的寫法：web/src/store/modules/useRouteStore.ts 用 import.meta.glob
 * 掃描 plugins 目錄下的 views 文件，並拼成 `../../plugins/${component}${meta.componentSuffix ?? '.vue'}`，
 * 因此這裡必須寫成 `zhh/scoring/views/room/index`（不帶 plugins/ 前綴、不帶 .vue 後綴）。
 */

use App\Model\Permission\Menu;
use App\Model\Permission\Meta;
use Hyperf\Database\Seeders\Seeder;

class MenuSeeder extends Seeder
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

    public function run(): void
    {
        $this->create($this->data());
    }

    public function data(): array
    {
        return [
            [
                'name' => 'scoring',
                'path' => '/scoring',
                'meta' => new Meta([
                    'title' => '打牌記賬',
                    'icon' => 'solar:home-smile-bold',
                    'type' => 'M',
                    'hidden' => 0,
                    'breadcrumbEnable' => 1,
                    'copyright' => 1,
                    'cache' => 1,
                    'affix' => 0,
                ]),
                'children' => [
                    [
                        'name' => 'scoring:room',
                        'path' => '/scoring/room',
                        'component' => 'zhh/scoring/views/room/index',
                        'meta' => new Meta([
                            'title' => '房間管理',
                            'icon' => 'solar:home-smile-bold',
                            'type' => 'M',
                            'hidden' => 0,
                            'breadcrumbEnable' => 1,
                            'copyright' => 1,
                            'cache' => 1,
                            'affix' => 0,
                        ]),
                        'children' => [
                            [
                                'name' => 'scoring:room:index',
                                'meta' => new Meta(['title' => '房間列表', 'type' => 'B']),
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    public function create(array $data, int $parent_id = 0): void
    {
        foreach ($data as $v) {
            $_v = $v;
            if (isset($v['children'])) {
                unset($_v['children']);
            }
            $_v['parent_id'] = $parent_id;

            // 冪等：同名菜單已存在則復用，避免重複安裝產生重複菜單
            $menu = Menu::query()->where('name', $_v['name'])->first();
            if ($menu === null) {
                $menu = Menu::create(array_merge(self::BASE_DATA, $_v));
            }

            if (isset($v['children']) && count($v['children'])) {
                $this->create($v['children'], (int) $menu->id);
            }
        }
    }
}
