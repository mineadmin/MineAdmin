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
use App\System\Model\SystemNotice;
use Hyperf\Collection\Arr;
use Hyperf\Stringable\Str;

beforeEach(function () {
    $this->prefix = '/system/notice';
});

test('notice test', function () {
    $this->actionTest([
        $this->buildTest('getNoParamsTest') => 'index',
        $this->buildTest('getNoParamsTest') => 'recycle',
    ]);
    $this->remoteTest();
    $successParam = [
        'title' => Str::random(5),
        'type' => 1,
        'content' => Str::random(6),
    ];
    $failParams = [
        Arr::only($successParam, 'title'),
        Arr::only($successParam, 'type'),
        Arr::only($successParam, 'content'),
    ];
    $updateSuccessParam = [
        'title' => Str::random(5),
        'type' => 1,
        'content' => Str::random(6),
    ];
    $updateFailParams = [
        Arr::only($updateSuccessParam, 'title'),
        Arr::only($updateSuccessParam, 'type'),
        Arr::only($updateSuccessParam, 'content'),
    ];
    expect($this->prefix)->toBeSaveAndUpdate($successParam, $failParams, $updateSuccessParam, $updateFailParams);
    $id = SystemNotice::query()->first()->value('id');
    $this->actionTest([
        $this->buildTest('getNoParamsTest') => 'read/' . $id,
    ]);
    $this->recoveryAndDeleteTest([$id]);
});
