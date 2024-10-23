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
use App\System\Model\SystemUser;
use Hyperf\Collection\Arr;
use Hyperf\Stringable\Str;

beforeEach(function () {
    $this->prefix = '/system/user';
});

test('user controller testing', function () {
    $this->actionTest([
        $this->buildTest('getNoParamsTest') => 'recycle',
        $this->buildTest('getNoParamsTest') => 'index',
    ]);
    $this->remoteTest();
    $successParam = [
        'username' => Str::random(5),
        'password' => Str::random(6),
        'dept_ids' => [1, 2, 3],
        'role_ids' => [1, 2, 3],
    ];
    $failParams = [
        Arr::only($successParam, 'username'),
        Arr::only($successParam, 'password'),
        Arr::only($successParam, 'dept_ids'),
        Arr::only($successParam, 'role_ids'),
    ];
    $updateSuccessParam = [
        'username' => Str::random(5),
        'password' => Str::random(6),
        'dept_ids' => [1, 2, 3],
        'role_ids' => [1, 2, 3],
    ];
    $updateFailParams = [
        Arr::only($updateSuccessParam, 'username'),
        Arr::only($updateSuccessParam, 'password'),
        Arr::only($updateSuccessParam, 'dept_ids'),
        Arr::only($updateSuccessParam, 'role_ids'),
    ];
    expect($this->prefix)->toBeSaveAndUpdate($successParam, $failParams, $updateSuccessParam, $updateFailParams);
    $id = SystemUser::query()->value('id');
    $this->actionTest([
        $this->buildTest('getNoParamsTest') => 'read/' . $id,
    ]);
    expect($this->post($this->prefix . '/clearCache', ['id' => $id]))->toBeHttpSuccess()
        ->and($this->post($this->prefix . '/setHomePage', ['id' => $id, 'dashboard' => 'xxx.vue']))->toBeHttpSuccess()
        ->and($this->post($this->prefix . '/updateInfo', ['dashboard' => 'xxx.vue']))->toBeHttpSuccess()
        ->and($this->post($this->prefix . '/modifyPassword', [
            'newPassword' => '123456',
            'newPassword_confirmation' => '123456',
            'oldPassword' => $this->password,
        ]))->toBeHttpSuccess();
    $this->password = '123456';
    expect($this->put($this->prefix . '/initUserPassword', ['id' => $id]))->toBeHttpSuccess();

    $this->changeStatusTest($id);
    $this->recoveryAndDeleteTest([$id]);
});
