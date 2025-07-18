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

namespace App\Schema;

use App\Model\Enums\User\Status;
use App\Model\Enums\User\Type;
use App\Model\Permission\User;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Schema;

#[Schema]
final class UserSchema implements \JsonSerializable
{
    #[Property(property: 'id', title: '用户ID，主键', type: 'int')]
    public ?int $id;

    #[Property(property: 'username', title: '用户名', type: 'string')]
    public ?string $username;

    #[Property(property: 'user_type', title: '用户类型：(100系统用户)', type: 'string')]
    public ?Type $userType;

    #[Property(property: 'nickname', title: '用户昵称', type: 'string')]
    public ?string $nickname;

    #[Property(property: 'phone', title: '手机', type: 'string')]
    public ?string $phone;

    #[Property(property: 'email', title: '用户邮箱', type: 'string')]
    public ?string $email;

    #[Property(property: 'avatar', title: '用户头像', type: 'string')]
    public ?string $avatar;

    #[Property(property: 'signed', title: '个人签名', type: 'string')]
    public ?string $signed;

    #[Property(property: 'status', title: '状态 (1正常 2停用)', type: 'int')]
    public ?Status $status;

    #[Property(property: 'login_ip', title: '最后登陆IP', type: 'string')]
    public ?string $loginIp;

    #[Property(property: 'login_time', title: '最后登陆时间', type: 'string')]
    public mixed $loginTime;

    #[Property(property: 'backend_setting', title: '后台设置数据', type: 'array')]
    public ?array $backendSetting;

    #[Property(property: 'created_by', title: '创建者', type: 'int')]
    public ?int $createdBy;

    #[Property(property: 'updated_by', title: '更新者', type: 'int')]
    public ?int $updatedBy;

    #[Property(property: 'created_at', title: '创建时间', type: 'string')]
    public mixed $createdAt;

    #[Property(property: 'updated_at', title: '更新时间', type: 'string')]
    public mixed $updatedAt;

    #[Property(property: 'remark', title: '备注', type: 'string')]
    public ?string $remark;

    #[Property(property: 'policy', ref: '#/components/schemas/PolicySchema', title: '权限')]
    public ?PolicySchema $policy;

    public function __construct(User $model)
    {
        $this->id = $model->id;
        $this->username = $model->username;
        $this->userType = $model->user_type;
        $this->nickname = $model->nickname;
        $this->phone = $model->phone;
        $this->email = $model->email;
        $this->avatar = $model->avatar;
        $this->signed = $model->signed;
        $this->status = $model->status;
        $this->loginIp = $model->login_ip;
        $this->loginTime = $model->login_time;
        $this->backendSetting = $model->backend_setting;
        $this->createdBy = $model->created_by;
        $this->updatedBy = $model->updated_by;
        $this->createdAt = $model->created_at;
        $this->updatedAt = $model->updated_at;
        $this->remark = $model->remark;
        $this->policy = isset($model->policy) ? new PolicySchema($model->policy) : null;
    }

    public function jsonSerialize(): mixed
    {
        return ['id' => $this->id, 'username' => $this->username, 'user_type' => $this->userType, 'nickname' => $this->nickname, 'phone' => $this->phone, 'email' => $this->email, 'avatar' => $this->avatar, 'signed' => $this->signed, 'status' => $this->status, 'login_ip' => $this->loginIp, 'login_time' => $this->loginTime, 'backend_setting' => $this->backendSetting, 'created_by' => $this->createdBy, 'updated_by' => $this->updatedBy, 'created_at' => $this->createdAt, 'updated_at' => $this->updatedAt, 'remark' => $this->remark];
    }
}
