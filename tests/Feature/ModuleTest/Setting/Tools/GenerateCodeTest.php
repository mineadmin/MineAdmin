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
use App\Setting\Model\SettingGenerateColumns;
use App\Setting\Model\SettingGenerateTables;
use App\System\Model\SystemUser;
use Hyperf\Stringable\Str;
use Mine\Mine;

beforeEach(function () {
    $this->prefix = '/setting/code';
    SettingGenerateTables::truncate();
    $this->table = SystemUser::getModel()->getTable();
    $this->mock = SettingGenerateTables::create([
        'table_name' => $this->table,
        'table_comment' => Str::random(4),
        'module_name' => 'system',
        'namespace' => Str::random(4),
        'menu_name' => Str::random(4),
        'belong_menu_id' => 0,
        'package_name' => Str::random(4),
        'type' => 'single',
        'generate_type' => 1,
        'generate_menus' => 'import,delete,read',
        'build_menu' => 1,
        'component_type' => 1,
        'options' => [],
    ]);
})->skip(version_compare(swoole_version(),'6.0','>'),'swoole version must be less than 6.0');

test('generator code test', function () {
    if (env('DB_DRIVER') !== 'mysql') {
        return;
    }
    $table = SystemUser::getModel()->getTable();
    expect($this->post($this->prefix . '/loadTable', [
        'source' => Mine::getMineName(),
        'names' => [
            [
                'name' => $table,
                'comment' => 'test',
            ],
        ],
    ]))->toBeHttpSuccess();
    $this->actionTest([
        $this->buildTest('getNoParamsTest') => 'index',
        $this->buildTest('getNoParamsTest') => 'getDataSourceList',
        $this->buildTest('getNoParamsTest') => 'getTableColumns',
        $this->buildTest('getNoParamsTest') => 'readTable',
    ]); /*
    testSuccessResponse($this->get($this->prefix . '/preview', [
        'id' => $this->mock->id,
    ]));*/

    expect($this->post($this->prefix . '/update', [
        'id' => $this->mock->id,
        'table_name' => $table,
        'table_comment' => Str::random(4),
        'module_name' => 'system',
        'namespace' => 'App\\System\\Controller\\Test',
        'menu_name' => Str::random(4),
        'belong_menu_id' => 0,
        'package_name' => 'App\\System\\Controller\\Test',
        'columns' => [
            [
                'id' => SettingGenerateColumns::query()->first()->getKey(),
                'column_name' => 'xxxx',
                'is_insert' => true,
                'is_edit' => true,
                'is_list' => true,
                'is_query' => true,
                'is_sort' => true,
                'is_required' => true,
            ],
        ],
        'type' => 'single',
        'generate_type' => 1,
        'generate_menus' => explode(',', 'import,delete,read'),
        'build_menu' => 1,
        'component_type' => 1,
        'options' => [],
    ]))->toBeHttpSuccess();
});

test('sync delete', function () {
    if (env('DB_DRIVER') !== 'mysql') {
        return;
    }
    //    testSuccessResponse($this->put($this->prefix . '/sync/' . $this->mock->id));

    expect($this->delete($this->prefix . '/delete', [
        'ids' => [$this->mock->id],
    ]))->toBeHttpSuccess();
});

//
// test('generator', function () {
//    var_dump($this->post($this->prefix . '/generate', [
//        'ids' => [
//            $this->mock->id,
//        ],
//    ]));
//    expect(true)->toBeTrue();
// //    testSuccessResponse($this->post($this->prefix . '/generate', [
// //        'ids' => [
// //            $this->mock->id,
// //        ],
// //    ]));
// });
