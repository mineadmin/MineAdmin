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
     * 容器
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * Redis
     * @var Redis
     */
    protected Redis $redis;

    #[Value("cache.default.prefix")]
    protected ?string $prefix = null;


    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
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
     * @throws \RedisException
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
     * @param bool $isScope
     * @return array
     * @throws \RedisException
     */
    public function getList(?array $params = null, bool $isScope = false): array
    {
        if (! isset($params['code'])) {
            return [];
        }

        $key = $this->prefix . 'Dict:' . $params['code'];

        if ($data = $this->redis->get($key)) {
            return unserialize($data);
        }

        $args = [
            'select' => ['id', 'label as title', 'value as key'],
            'status' => \Mine\MineModel::ENABLE,
            'orderBy' => 'sort',
            'orderType' => 'desc'
        ];
        $data = $this->mapper->getList(array_merge($args, $params), $isScope);

        $this->redis->set($key, serialize($data));

        return $data;
    }

    /**
     * 清除缓存
     * @return bool
     * @throws \RedisException
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
