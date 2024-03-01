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
use App\System\Model\SystemDictData;
use Hyperf\Collection\Arr;
use Hyperf\Stringable\Str;

beforeEach(function () {
    SystemDictData::class;
    $this->prefix = '/system/dataDict';
});

test('DictData test', function () {
    $this->actionTest([
        $this->buildTest('getNoParamsTest') => 'index',
        $this->buildTest('getNoParamsTest') => 'list',
        $this->buildTest('getNoParamsTest') => 'lists',
        $this->buildTest('getNoParamsTest') => 'recycle',
    ]);
    testSuccessResponse($this->post($this->prefix . '/clearCache'));
    $successParam = [
        'label' => Str::random(5),
        'code' => Str::random(6),
        'value' => Str::random(6),
        'type_id' => 1,
    ];
    $failParams = [
        Arr::only($successParam, 'label'),
        Arr::only($successParam, 'code'),
        Arr::only($successParam, 'value'),
    ];
    $updateSuccessParam = [
        'label' => Str::random(5),
        'code' => Str::random(6),
        'value' => Str::random(6),
        'type_id' => 1,
    ];
    $updateFailParams = [
        Arr::only($updateSuccessParam, 'label'),
        Arr::only($updateSuccessParam, 'code'),
        Arr::only($updateSuccessParam, 'value'),
    ];
    $id = $this->saveAndUpdate($successParam, $failParams, $updateSuccessParam, $updateFailParams);
    $this->actionTest([
        $this->buildTest('getNoParamsTest') => 'read/' . $id,
    ]);
    testSuccessResponse($this->put($this->prefix . '/numberOperation', [
        'id' => $id,
        'numberName' => 'status',
    ]));
    $this->changeStatusTest($id);
    $this->recoveryAndDeleteTest([$id]);
});
