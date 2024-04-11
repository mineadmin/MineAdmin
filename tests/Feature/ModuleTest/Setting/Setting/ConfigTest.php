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
use App\Setting\Model\SettingConfig;
use Hyperf\Collection\Arr;
use Hyperf\Stringable\Str;

beforeEach(function () {
    $this->prefix = '/setting/config';
});
test('setting config controller test', function () {
    $this->actionTest([
        $this->buildTest('getNoParamsTest') => 'index',
    ]);
    $this->remoteTest();

    $successParam = [
        'group_id' => 1,
        'key' => Str::random(32),
        'name' => Str::random(6),
    ];
    $failParams = [
        Arr::only($successParam, 'group_id'),
        Arr::only($successParam, 'key'),
        Arr::only($successParam, 'name'),
    ];
    $updateSuccessParam = [
        'group_id' => 2,
        'key' => $successParam['key'],
        'name' => Str::random(3),
    ];
    expect($this->post($this->prefix . '/save', $successParam))->toBeHttpSuccess();
    foreach ($failParams as $failParam) {
        expect($this->post($this->prefix . '/save', $failParam))->toBeHttpFail();
    }
    expect($this->post($this->prefix . '/update', $updateSuccessParam))->toBeHttpSuccess()
        ->and($this->post($this->prefix . '/updateByKeys', [
            $successParam['key'] => '123321',
        ]))->toBeHttpSuccess();
    $id = SettingConfig::query()->where('key', $successParam['key'])->select(['key'])->value('key');

    expect($this->delete(
        $this->prefix . '/delete',
        [
            'ids' => [$id],
        ]
    ))->toBeHttpSuccess()
        ->and($this->post($this->prefix . '/clearCache', []))->toBeHttpSuccess();
});
