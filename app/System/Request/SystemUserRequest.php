<?php
declare(strict_types=1);
namespace App\System\Request;

use App\System\Service\SystemUserService;
use Mine\MineFormRequest;

class SystemUserRequest extends MineFormRequest
{
    /**
     * 公共规则
     */
    public function commonRules(): array
    {
        return [];
    }


    /**
     * 新增数据验证规则
     * return array
     */
    public function saveRules(): array
    {
        return [
            'username' => 'required|max:20',
            'password' => 'required|min:6',
            'dept_ids'  => 'required',
            'role_ids' => 'required'
        ];
    }

    /**
     * 新增数据验证规则
     * return array
     */
    public function updateRules(): array
    {
        return [
            'username' => 'required|max:20',
            'dept_ids'  => 'required',
            'role_ids' => 'required'
        ];
    }

    /**
     * 修改状态数据验证规则
     * return array
     */
    public function changeStatusRules(): array
    {
        return [
            'id' => 'required',
            'status' => 'required'
        ];
    }

    /**
     * 修改密码验证规则
     * return array
     */
    public function modifyPasswordRules(): array
    {
        return [
            'newPassword' => 'required|confirmed',
            'newPassword_confirmation' => 'required',
            'oldPassword' => ['required', function ($attribute, $value, $fail) {
                $service = $this->container->get(SystemUserService::class);
                /* @var SystemUser $model */
                $model = $service->mapper->getModel()::find((int) user()->getId(), ['password']);
                if (! $service->mapper->checkPass($value, $model->password)) {
                    $fail(t('system.valid_password'));
                }
            }],
        ];
    }

    /**
     * 设置用户首页数据验证规则
     */
    public function setHomePageRules(): array
    {
        return [
            'id' => 'required',
            'dashboard' => 'required'
        ];
    }

    /**
     * 登录规则
     * @return string[]
     */
    public function loginRules(): array
    {
        return [
            'username' => 'required|max:20',
            'password' => 'required|min:6',
        ];
    }

    /**
     * 字段映射名称
     * return array
     */
    public function attributes(): array
    {
        return [
            'id' => '用户ID',
            'username' => '用户名',
            'password' => '用户密码',
            'dashboard' => '用户后台首页',
            'oldPassword' => '旧密码',
            'newPassword' => '新密码',
            'newPassword_confirmation' => '确认密码',
            'status' => '用户状态',
            'dept_ids' => '部门ID',
            'role_ids' => '角色列表',
        ];
    }

}