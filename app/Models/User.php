<?php

namespace App\Models;

use App\Models\DataPermission\Policy;
use App\Models\Enums\DataPermission\PolicyType;
use App\Models\Enums\User\Status;
use App\Models\Enums\User\Type;
use App\Models\Permission\Department;
use App\Models\Permission\Position;
use App\Models\Permission\Role;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection as SupportCollection;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

/**
 * @property int $id 用户ID,主键
 * @property string $username 用户名
 * @property string $password 密码
 * @property Type $user_type 用户类型:100=系统用户
 * @property string $nickname 用户昵称
 * @property string $phone 手机
 * @property string $email 用户邮箱
 * @property string $avatar 用户头像
 * @property string $signed 个人签名
 * @property Status $status 状态:1=正常,2=停用
 * @property string $login_ip 最后登陆IP
 * @property string $login_time 最后登陆时间
 * @property array<array-key, mixed>|null $backend_setting 后台设置数据
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property Carbon|null $created_at 创建时间
 * @property Carbon|null $updated_at 更新时间
 * @property string $remark 备注
 * @property-read Collection<int, Department> $department
 * @property-read int|null $department_count
 * @property-read Collection<int, Department> $deptLeader
 * @property-read int|null $dept_leader_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Policy|null $policy
 * @property-read Collection<int, Position> $position
 * @property-read int|null $position_count
 * @property-read Collection<int, Role> $roles
 * @property-read int|null $roles_count
 * @property-read Collection<int, PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 *
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBackendSetting($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLoginIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLoginTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSigned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUserType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 *
 * @mixin \Eloquent
 */
#[Fillable(['id', 'username', 'password', 'user_type', 'nickname', 'phone', 'email', 'avatar', 'signed', 'status', 'login_ip', 'login_time', 'backend_setting', 'created_by', 'updated_by', 'created_at', 'updated_at', 'remark'])]
#[Hidden(['password'])]
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'user';

    protected static function booted(): void
    {
        self::creating(function (self $user): void {
            if (! $user->isDirty('password')) {
                $user->resetPassword();
            }
        });

        self::deleted(function (self $user): void {
            $user->roles()->detach();
            $user->policy()->delete();
        });
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_belongs_role', 'user_id', 'role_id');
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * @return array<string, mixed>
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function setPasswordAttribute(mixed $value): void
    {
        $this->attributes['password'] = password_hash((string) $value, PASSWORD_DEFAULT);
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    public function resetPassword(): void
    {
        $this->password = '123456';
    }

    public function isSuperAdmin(): bool
    {
        return $this->roles()->where('code', 'SuperAdmin')->exists();
    }

    public function getRoles(array $fields): Collection
    {
        return $this->roles()
            ->where('status', Status::Normal)
            ->select($fields)
            ->get();
    }

    public function getPermissions(): SupportCollection
    {
        return $this->roles()->with('menus')->orderBy('sort')->get()->pluck('menus')->flatten();
    }

    public function hasPermission(string $permission): bool
    {
        return $this->roles()->whereRelation('menus', 'name', $permission)->exists();
    }

    public function policy(): HasOne
    {
        return $this->hasOne(Policy::class, 'user_id', 'id');
    }

    public function department(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'user_dept', 'user_id', 'dept_id');
    }

    public function deptLeader(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'dept_leader', 'user_id', 'dept_id');
    }

    public function position(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'user_position', 'user_id', 'position_id');
    }

    public function getPolicy(): ?Policy
    {
        $policy = $this->policy()->first();
        if ($policy !== null) {
            return $policy;
        }

        $this->load('position');
        foreach ($this->position as $position) {
            $current = $position->policy()->first();
            if ($current !== null) {
                return $current;
            }
        }

        return null;
    }

    #[Scope]
    protected function dataPermission(Builder $query, self $currentUser): void
    {
        if ($currentUser->isSuperAdmin()) {
            return;
        }

        $policy = $currentUser->getPolicy();
        if ($policy === null || $policy->policy_type->isAll()) {
            return;
        }

        $allowedUserIds = $currentUser->allowedUserIdsForDataPermission($policy);
        $query->whereIn('id', $allowedUserIds);
    }

    /**
     * @return array<int, int>
     */
    private function allowedUserIdsForDataPermission(Policy $policy): array
    {
        if ($policy->policy_type->isSelf() || $policy->policy_type->isCustomFunc()) {
            return [$this->id];
        }

        $departmentIds = $this->departmentIdsForDataPermission($policy);
        if ($departmentIds === []) {
            return [$this->id];
        }

        return User::query()
            ->whereKey($this->id)
            ->orWhereHas('department', function (Builder $query) use ($departmentIds): void {
                $query->whereIn('id', $departmentIds);
            })
            ->pluck('id')
            ->all();
    }

    /**
     * @return array<int, int>
     */
    private function departmentIdsForDataPermission(Policy $policy): array
    {
        $departments = match ($policy->policy_type) {
            PolicyType::DeptSelf => $this->baseDataPermissionDepartments(),
            PolicyType::DeptTree => $this->baseDataPermissionDepartments()->flatMap(
                fn (Department $department): SupportCollection => $department->getFlatChildren(),
            ),
            PolicyType::CustomDept => Department::query()->whereIn('id', $this->customDepartmentIds($policy))->get(),
            default => collect(),
        };

        return $departments->pluck('id')->unique()->values()->all();
    }

    /**
     * @return SupportCollection<int, Department>
     */
    private function baseDataPermissionDepartments(): SupportCollection
    {
        return $this->department()->get()
            ->merge($this->deptLeader()->get())
            ->merge($this->position()->with('department')->get()->pluck('department')->filter());
    }

    /**
     * @return array<int, int>
     */
    private function customDepartmentIds(Policy $policy): array
    {
        return collect($policy->value)->map(static fn (mixed $id): int => (int) $id)->all();
    }

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'status' => Status::class,
            'user_type' => Type::class,
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'backend_setting' => 'array',
        ];
    }
}
