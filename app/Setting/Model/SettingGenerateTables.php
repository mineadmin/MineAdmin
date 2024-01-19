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

namespace App\Setting\Model;

use App\System\Model\SystemMenu;
use Carbon\Carbon;
use Hyperf\Collection\Collection;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\DbConnection\Model\Model;
use Mine\Generator\Contracts\GeneratorTablesContract;
use Mine\Generator\Enums\ComponentTypeEnum;
use Mine\Generator\Enums\GenerateTypeEnum;
use Mine\Generator\Enums\GeneratorTypeEnum;
use Mine\MineModel;

/**
 * @property int $id 主键
 * @property string $table_name 表名称
 * @property string $table_comment 表注释
 * @property string $module_name 所属模块
 * @property string $namespace 命名空间
 * @property string $menu_name 生成菜单名
 * @property int $belong_menu_id 所属菜单
 * @property string $package_name 控制器包名
 * @property string $type 生成类型，single 单表CRUD，tree 树表CRUD，parent_sub父子表CRUD
 * @property int $generate_type 1 压缩包下载 2 生成到模块
 * @property string $generate_menus 生成菜单列表
 * @property int $build_menu 是否构建菜单
 * @property int $component_type 组件显示方式
 * @property array $options 其他业务选项
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $remark 备注
 */
class SettingGenerateTables extends MineModel implements GeneratorTablesContract
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'setting_generate_tables';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'table_name', 'table_comment', 'module_name', 'namespace', 'menu_name', 'belong_menu_id', 'package_name', 'type', 'generate_type', 'generate_menus', 'build_menu', 'component_type', 'options', 'created_by', 'updated_by', 'created_at', 'updated_at', 'remark'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'belong_menu_id' => 'integer', 'generate_type' => 'integer', 'build_menu' => 'integer', 'component_type' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'options' => 'array'];

    /**
     * 关联生成业务字段信息表.
     */
    public function columns(): HasMany
    {
        return $this->hasMany(SettingGenerateColumns::class, 'table_id', 'id');
    }

    public function getModuleName(): string
    {
        return $this->module_name;
    }

    public function getTableName(): string
    {
        return $this->table_name;
    }

    public function getGenerateMenus(): ?string
    {
        return $this->generate_menus;
    }

    public function getType(): GeneratorTypeEnum
    {
        return GeneratorTypeEnum::from($this->type);
    }

    public function getMenuName(): string
    {
        return $this->menu_name;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getPackageName(): ?string
    {
        return $this->package_name;
    }

    public function getGenerateType(): GenerateTypeEnum
    {
        return GenerateTypeEnum::from($this->generate_type);
    }

    public function getComponentType(): ComponentTypeEnum
    {
        return match ($this->component_type) {
            2 => ComponentTypeEnum::DRAWER,
            3 => ComponentTypeEnum::TAG,
            default => ComponentTypeEnum::MODAL
        };
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getPkName(): string
    {
        return $this->getQualifiedKeyName();
    }

    public function getColumns(): Collection
    {
        return $this->columns()->get();
    }

    public function handleQuery(\Closure $closure): mixed
    {
        return $closure(SettingGenerateColumns::query());
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getBelongMenuId(): int
    {
        return $this->belong_menu_id;
    }

    public function getSystemMenuFind(\Closure $closure): Model
    {
        return $closure(SystemMenu::query());
    }
}
