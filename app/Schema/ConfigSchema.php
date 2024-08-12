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

use App\Model\Setting\Config;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Schema;

#[Schema(title: 'ConfigSchema')]
class ConfigSchema implements \JsonSerializable
{
    #[Property(property: 'group_id', title: '组id', type: 'int')]
    public ?int $groupId;

    #[Property(property: 'key', title: '配置键名', type: 'string')]
    public ?string $key;

    #[Property(property: 'value', title: '配置值', type: 'string')]
    public ?string $value;

    #[Property(property: 'name', title: '配置名称', type: 'string')]
    public ?string $name;

    #[Property(property: 'input_type', title: '数据输入类型', type: 'string')]
    public ?string $inputType;

    #[Property(property: 'config_select_data', title: '配置选项数据', type: 'mixed')]
    public mixed $configSelectData;

    #[Property(property: 'sort', title: '排序', type: 'int')]
    public ?int $sort;

    #[Property(property: 'remark', title: '备注', type: 'string')]
    public ?string $remark;

    public function __construct(Config $model)
    {
        $this->groupId = $model->group_id;
        $this->key = $model->key;
        $this->value = $model->value;
        $this->name = $model->name;
        $this->inputType = $model->input_type;
        $this->configSelectData = $model->config_select_data;
        $this->sort = $model->sort;
        $this->remark = $model->remark;
    }

    public function jsonSerialize(): mixed
    {
        return ['group_id' => $this->groupId, 'key' => $this->key, 'value' => $this->value, 'name' => $this->name, 'input_type' => $this->inputType, 'config_select_data' => $this->configSelectData, 'sort' => $this->sort, 'remark' => $this->remark];
    }
}
