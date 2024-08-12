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

use App\Model\Setting\ConfigGroup;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Schema;

#[Schema(title: 'ConfigGroupSchema')]
class ConfigGroupSchema implements \JsonSerializable
{
    #[Property(property: 'id', title: '主键', type: 'int')]
    public ?int $id;

    #[Property(property: 'name', title: '配置组名称', type: 'string')]
    public ?string $name;

    #[Property(property: 'code', title: '配置组标识', type: 'string')]
    public ?string $code;

    #[Property(property: 'created_by', title: '创建者', type: 'int')]
    public ?int $createdBy;

    #[Property(property: 'updated_by', title: '更新者', type: 'int')]
    public ?int $updatedBy;

    #[Property(property: 'created_at', title: '', type: 'string')]
    public mixed $createdAt;

    #[Property(property: 'updated_at', title: '', type: 'string')]
    public mixed $updatedAt;

    #[Property(property: 'remark', title: '备注', type: 'string')]
    public ?string $remark;

    public function __construct(ConfigGroup $model)
    {
        $this->id = $model->id;
        $this->name = $model->name;
        $this->code = $model->code;
        $this->createdBy = $model->created_by;
        $this->updatedBy = $model->updated_by;
        $this->createdAt = $model->created_at;
        $this->updatedAt = $model->updated_at;
        $this->remark = $model->remark;
    }

    public function jsonSerialize(): mixed
    {
        return ['id' => $this->id, 'name' => $this->name, 'code' => $this->code, 'created_by' => $this->createdBy, 'updated_by' => $this->updatedBy, 'created_at' => $this->createdAt, 'updated_at' => $this->updatedAt, 'remark' => $this->remark];
    }
}
