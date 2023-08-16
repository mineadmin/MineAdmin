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
        if (isset($data['config_select_data']) && is_array($data['config_select_data'])) {
            $data['config_select_data'] = json_encode($data['config_select_data'], JSON_UNESCAPED_UNICODE);
        }
        return $this->model::query()->where('key', $key)->update($data) > -1;
    }

    /**
     * 按 keys 更新配置
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function updateByKey(string $key, mixed $value = null): bool
    {
        return $this->model::query()->where('key', $key)->update([
            'value' => is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value
        ]) > 0;
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
        if (!empty($params['group_id'])) {
            $query->where('group_id', $params['group_id']);
        }
        if (!empty($params['name'])) {
            $query->where('name', $params['name']);
        }
        if (!empty($params['key'])) {
            $query->where('key', 'like',  '%'.$params['key'].'%');
        }
        return $query;
    }
}