<?php

declare(strict_types=1);
namespace App\System\Service;

use App\System\Mapper\SystemDictDataMapper;
use Hyperf\Config\Annotation\Value;
use Hyperf\Redis\Redis;
use Mine\Abstracts\AbstractService;
use Psr\Container\ContainerInterface;

/**
 * 字典类型业务
 * Class SystemLoginLogService
 * @package App\System\Service
 */
class SystemDictDataService extends AbstractService
{
    /**
     * @var SystemDictDataMapper
     */
    public $mapper;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Redis
     */
    protected $redis;

    /**
     * @Value("cache.default.prefix")
     * @var string
     */
    protected $prefix;


    public function __construct(SystemDictDataMapper $mapper, ContainerInterface $container)
    {
        $this->mapper = $mapper;
        $this->container = $container;
        $this->redis = $this->container->get(Redis::class);
    }

    /**
     * 查询多个字典
     * @param array|null $params
     * @return array
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
     * 查询一个字典
     * @param array|null $params
     * @return array
     */
    public function getList(?array $params = null): array
    {
        if (! isset($params['code'])) {
            return [];
        }

        $key = $this->prefix . 'Dict:' . $params['code'];

        if ($data = $this->redis->get($key)) {
            return unserialize($data);
        }

        $args = [
            'select' => ['id', 'label', 'value'],
            'status' => '0',
            'orderBy' => 'sort',
            'orderType' => 'desc'
        ];
        $data = $this->mapper->getList(array_merge($args, $params), false);

        $this->redis->set($key, serialize($data));

        return $data;
    }

    /**
     * 清除缓存
     * @return bool
     */
    public function clearCache(): bool
    {
        $key = $this->prefix . 'Dict:*';
        foreach ($this->redis->keys($key) as $item) {
            $this->redis->del($item);
        }
        return true;
    }
}
