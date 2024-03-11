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
use App\Setting\Model\SettingCrontabLog;
use Hyperf\Collection\Arr;
use Hyperf\Stringable\Str;

beforeEach(function () {
    $this->prefix = '/setting/crontab';
});

test('crontab test', function () {
    $this->actionTest([
        $this->buildTest('getNoParamsTest') => 'index',
        $this->buildTest('getNoParamsTest') => 'logPageList',
    ]);
    $this->remoteTest();
});

test('data change test', function () {
    $successParam = [
        'name' => Str::random(5),
        'type' => 1,
        'rule' => 1,
        'target' => 'describe:routes',
    ];
    $failParams = [
        Arr::only($successParam, 'name'),
        Arr::only($successParam, 'type'),
        Arr::only($successParam, 'rule'),
        Arr::only($successParam, 'target'),
    ];
    $updateSuccessParam = [
        'name' => Str::random(5),
        'type' => 1,
        'rule' => 1,
        'target' => 'describe:routes',
    ];
    $updateFailParams = [
        Arr::only($updateSuccessParam, 'name'),
        Arr::only($updateSuccessParam, 'type'),
        Arr::only($updateSuccessParam, 'rule'),
        Arr::only($updateSuccessParam, 'target'),
    ];
    $id = $this->saveAndUpdate($successParam, $failParams, $updateSuccessParam, $updateFailParams);
    $this->actionTest([
        $this->buildTest('getNoParamsTest') => 'read/' . $id,
    ]);

    testSuccessResponse($this->post($this->prefix . '/run', [
        'id' => $id,
    ]));
    SettingCrontabLog::create([
        'crontab_id' => 1,
        'name' => 'xxx',
        'target' => 'ls',
        'parameter' => 'xxx',
        'exception_info' => 'xxx',
        'status' => 1,
    ]);
    testSuccessResponse($this->delete($this->prefix . '/deleteCrontabLog', [
        'ids' => array_column(SettingCrontabLog::query()->get()->toArray(), 'id'),
    ]));
    $this->changeStatusTest($id);
    $this->recoveryAndDeleteTest([$id], ['delete']);
});
