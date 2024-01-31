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

namespace App\Setting\Mapper;

use App\Setting\Model\SettingConfig;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

class SettingConfigMapper extends AbstractMapper
{
    /**
     * @var SettingConfig
     */
    public $model;

    public function assignModel()
    {
        $this->model = SettingConfig::class;
    }

    /**
     * 按Key获取配置.
     */
    public function getConfigByKey(string $key): array
    {
        $model = $this->model::query()->find($key, [
            'group_id', 'name', 'key', 'value', 'sort', 'input_type', 'config_select_data',
        ]);
        return $model ? $model->toArray() : [];
    }

    /**
     * 按组的key获取一组配置信息.
     */
    public function getConfigByGroupKey(string $groupKey): array
    {
        $prefix = env('DB_PREFIX');
        return $this->model::query()->whereRaw(
            sprintf('group_id = ( SELECT id FROM %ssetting_config_group WHERE code = ? )', $prefix),
            [$groupKey]
        )->get()->toArray();
    }

    /**
     * 更新配置.
     */
    public function updateConfig(string $key, array $data): bool
    {
        if (isset($data['config_select_data']) && is_array($data['config_select_data'])) {
            $data['config_select_data'] = json_encode($data['config_select_data'], JSON_UNESCAPED_UNICODE);
        }
        return $this->model::query()->where('key', $key)->update($data) > -1;
    }

    /**
     * 按 keys 更新配置.
     */
    public function updateByKey(string $key, mixed $value = null): bool
    {
        return $this->model::query()->where('key', $key)->update([
            'value' => is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value,
        ]) > 0;
    }

    /**
     * 保存配置.
     */
    public function save(array $data): mixed
    {
        $this->filterExecuteAttributes($data);
        $model = $this->model::create($data);
        return ($model->{$model->getKeyName()}) ? 1 : 0;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['group_id']) && filled($params['group_id'])) {
            $query->where('group_id', $params['group_id']);
        }
        if (isset($params['name']) && filled($params['name'])) {
            $query->where('name', $params['name']);
        }
        if (isset($params['key']) && filled($params['key'])) {
            $query->where('key', 'like', '%' . $params['key'] . '%');
        }
        return $query;
    }
}
