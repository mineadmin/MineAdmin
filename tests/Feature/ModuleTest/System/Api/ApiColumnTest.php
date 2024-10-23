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
use App\System\Model\SystemApiColumn;
use Hyperf\Collection\Arr;
use Hyperf\Stringable\Str;

beforeEach(function () {
    $this->prefix = '/system/apiColumn';
});

test('Api Column get test', function () {
    $this->actionTest([
        $this->buildTest('getNoParamsTest') => 'index',
        $this->buildTest('getNoParamsTest') => 'recycle',
    ]);
    $this->remoteTest();
});
test('Api Column put test', function () {
    $successParam = [
        'name' => Str::random(5),
        'api_id' => 1,
        'type' => 1,
        'data_type' => 1,
        'is_required' => 1,
    ];
    $failParams = [
        Arr::only($successParam, 'name'),
        Arr::only($successParam, 'api_id'),
        Arr::only($successParam, 'type'),
        Arr::only($successParam, 'data_type'),
        Arr::only($successParam, 'is_required'),
    ];
    $updateSuccessParam = [
        'name' => Str::random(5),
        'api_id' => 1,
        'type' => 1,
        'data_type' => 1,
        'is_required' => 1,
    ];
    $updateFailParams = [
        Arr::only($updateSuccessParam, 'name'),
        Arr::only($updateSuccessParam, 'api_id'),
        Arr::only($updateSuccessParam, 'type'),
        Arr::only($updateSuccessParam, 'data_type'),
        Arr::only($updateSuccessParam, 'is_required'),
    ];
    expect($this->prefix)->toBeSaveAndUpdate($successParam, $failParams, $updateSuccessParam, $updateFailParams);
    $id = SystemApiColumn::query()->first()->id;
    $this->actionTest([
        $this->buildTest('getNoParamsTest') => 'read/' . $id,
    ]);

    $this->changeStatusTest($id);
    $this->recoveryAndDeleteTest([$id]);
});
