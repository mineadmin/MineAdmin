<?php

declare(strict_types=1);
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
     * 按Key获取配置
     * @param string $key
     * @return array
     */
    public function getConfigByKey(string $key): array
    {
        $model = $this->model::query()->find($key, [
            'group_id', 'name', 'key', 'value', 'sort', 'input_type', 'config_select_data'
        ]);
        return $model ? $model->toArray() : [];
    }

    /**
     * 更新配置
     * @param string $key
     * @param array $data
     * @return bool
     */
    public function updateConfig(string $key, array $data): bool
    {
        return $this->model::query()->where('key', $key)->update($data) > 0;
    }

    /**
     * 按 keys 更新配置
     * @param string $key
     * @param string|int|bool $value
     * @return bool
     */
    public function updateByKey(string $key, string|int|bool|null $value = null): bool
    {
        return $this->model::query()->where('key', $key)->update(['value' => $value]) > 0;
    }

    /**
     * 保存配置
     * @param array $data
     * @return int
     */
    public function save(array $data): int
    {
        $this->filterExecuteAttributes($data);
        $model = $this->model::create($data);
        return ($model->{$model->getKeyName()}) ? 1 : 0;
    }

    /**
     * 搜索处理器
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['group_id']) && !empty($params['group_id'])) {
            $query->where('group_id', $params['group_id']);
        }
        if (isset($params['name']) && !empty($params['name'])) {
            $query->where('name', $params['name']);
        }
        if (isset($params['key']) && !empty($params['key'])) {
            $query->where('key', 'like',  '%'.$params['key'].'%');
        }
        return $query;
    }
}