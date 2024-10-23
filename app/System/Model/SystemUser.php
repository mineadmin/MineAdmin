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

namespace App\System\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Relations\BelongsToMany;
use Hyperf\Database\Model\SoftDeletes;
use Mine\MineModel;

/**
 * @property int $id 用户ID，主键
 * @property string $username 用户名
 * @property string $user_type 用户类型：(100系统用户)
 * @property string $nickname 用户昵称
 * @property string $phone 手机
 * @property string $email 用户邮箱
 * @property string $avatar 用户头像
 * @property string $signed 个人签名
 * @property string $dashboard 后台首页类型
 * @property int $status 状态 (1正常 2停用)
 * @property string $login_ip 最后登陆IP
 * @property string $login_time 最后登陆时间
 * @property string $backend_setting 后台设置数据
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 * @property string $remark 备注
 * @property null|Collection|SystemRole[] $roles
 * @property null|Collection|SystemPost[] $posts
 * @property null|Collection|SystemDept[] $depts
 * @property mixed $password 密码
 */
class SystemUser extends MineModel
{
    use SoftDeletes;

    public const USER_NORMAL = 1;

    public const USER_BAN = 2;

    /**
     * 系统用户.
     */
    public const TYPE_SYS_USER = '100';

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'system_user';

    /**
     * 隐藏的字段列表.
     * @var string[]
     */
    protected array $hidden = ['password', 'deleted_at'];

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'username', 'password', 'user_type', 'nickname', 'phone', 'email', 'avatar', 'signed', 'dashboard', 'status', 'login_ip', 'login_time', 'backend_setting', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at', 'remark'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'status' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'backend_setting' => 'json'];

    /**
     * 通过中间表关联角色.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(SystemRole::class, 'system_user_role', 'user_id', 'role_id');
    }

    /**
     * 通过中间表关联岗位.
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(SystemPost::class, 'system_user_post', 'user_id', 'post_id');
    }

    /**
     * 通过中间表关联部门.
     */
    public function depts(): BelongsToMany
    {
        return $this->belongsToMany(SystemDept::class, 'system_user_dept', 'user_id', 'dept_id');
    }

    /**
     * 密码加密.
     * @param mixed $value
     */
    public function setPasswordAttribute($value): void
    {
        $this->attributes['password'] = password_hash($value, PASSWORD_DEFAULT);
    }

    /**
     * 验证密码
     * @param mixed $password
     * @param mixed $hash
     */
    public static function passwordVerify($password, $hash): bool
    {
        return password_verify($password, $hash);
    }
}
