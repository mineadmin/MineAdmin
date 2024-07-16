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

namespace App\Service\System;

use App\Mapper\System\DictDataMapper;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Cache\Annotation\CacheEvict;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\DependProxy;
use Mine\Interfaces\ServiceInterface\DictDataServiceInterface;
use Mine\MineModel;

/**
 * 字典类型业务
 * Class LoginLogService.
 */
#[DependProxy(values: [DictDataServiceInterface::class])]
class DictDataService extends AbstractService implements DictDataServiceInterface
{
    /**
     * @var DictDataMapper
     */
    public $mapper;

    public function __construct(DictDataMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 查询多个字典.
     */
    public function getLists(?array $params = null): array
    {
        if (! isset($params['codes'])) {
            return [];
        }

        $codes = explode(',', $params['codes']);
        $data = [];

        foreach ($codes as $code) {
            $data[$code] = $this->getList(['code' => $code]);
        }

        return $data;
    }

    /**
     * 查询一个字典.
     */
    #[Cacheable(prefix: 'system:dict:data', value: '_#{params.code}', ttl: 600, listener: 'system-dict-update')]
    public function getList(?array $params = null, bool $isScope = false): array
    {
        $args = [
            'select' => ['id', 'label as title', 'value as key'],
            'status' => MineModel::ENABLE,
            'orderBy' => 'sort',
            'orderType' => 'desc',
        ];
        return $this->mapper->getList(array_merge($args, $params), $isScope);
    }

    /**
     * 清除缓存.
     */
    #[CacheEvict(prefix: 'system:dict:data', all: true)]
    public function clearCache(): bool
    {
        return true;
    }
}
