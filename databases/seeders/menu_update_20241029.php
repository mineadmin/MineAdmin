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
use App\Model\Permission\Meta;
use Hyperf\Database\Seeders\Seeder;
use Hyperf\DbConnection\Db;

class MenuUpdate20241029 extends Seeder
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
            [
                'name' => 'dataCenter',
                'path' => '/dataCenter',
                'meta' => new Meta([
                    'title' => '数据中心',
                    'i18n' => 'baseMenu.dataCenter.index',
                    'icon' => 'ri:database-line',
                    'type' => 'M',
                    'hidden' => 0,
                    'componentPath' => 'modules/',
                    'componentSuffix' => '.vue',
                    'breadcrumbEnable' => 1,
                    'copyright' => 1,
                    'cache' => 1,
                    'affix' => 0,
                ]),
                'children' => [
                    [
                        'name' => 'dataCenter:attachment',
                        'path' => '/dataCenter/attachment',
                        'component' => 'base/views/dataCenter/attachment/index',
                        'meta' => new Meta([
                            'title' => '附件管理',
                            'type' => 'M',
                            'hidden' => 0,
                            'icon' => 'ri:attachment-line',
                            'i18n' => 'baseMenu.dataCenter.attachment',
                            'componentPath' => 'modules/',
                            'componentSuffix' => '.vue',
                            'breadcrumbEnable' => 1,
                            'copyright' => 1,
                            'cache' => 1,
                            'affix' => 0,
                        ]),
                        'children' => [
                            [
                                'name' => 'dataCenter:attachment:list',
                                'meta' => new Meta([
                                    'title' => '附件列表',
                                    'i18n' => 'baseMenu.dataCenter.attachmentList',
                                    'type' => 'B',
                                ]),
                            ],
                            [
                                'name' => 'dataCenter:attachment:upload',
                                'meta' => new Meta([
                                    'title' => '上传附件',
                                    'i18n' => 'baseMenu.dataCenter.attachmentUpload',
                                    'type' => 'B',
                                ]),
                            ],
                            [
                                'name' => 'dataCenter:attachment:delete',
                                'meta' => new Meta([
                                    'title' => '删除附件',
                                    'i18n' => 'baseMenu.dataCenter.attachmentDelete',
                                    'type' => 'B',
                                ]),
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
            $menu = Menu::create(array_merge(self::BASE_DATA, $_v));
            if (isset($v['children']) && count($v['children'])) {
                $this->create($v['children'], $menu->id);
            }
        }
    }
}
