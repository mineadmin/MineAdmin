<?php
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

declare(strict_types=1);
namespace Mine\Crontab;

use App\Setting\Model\SettingCrontab;
use Hyperf\Crontab\Parser;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\Redis\Redis;
use Psr\Container\ContainerInterface;

/**
 * 定时任务管理器
 * Class MineCrontabManage
 * @package Mine\Crontab
 */
class MineCrontabManage
{
    /**
     * ContainerInterface
     */
    #[Inject]
    protected ContainerInterface $container;

    /**
     * Parser
     */
    #[Inject]
    protected Parser $parser;

    /**
     * ClientFactory
     */
    #[Inject]
    protected ClientFactory $clientFactory;

    /**
     * Redis
     */
    protected Redis $redis;


    /**
     * MineCrontabManage constructor.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __construct()
    {
        $this->redis = redis();
    }

    /**
     * 获取定时任务列表
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getCrontabList(): array
    {
        $prefix = config('cache.default.prefix');
        $data = $this->redis->get($prefix . 'crontab');

        if ($data === false) {
            $data = SettingCrontab::query()
                ->where('status', '0')
                ->get(explode(',', 'id,name,type,target,rule,parameter'))->toArray();
            $this->redis->set($prefix . 'crontab', serialize($data));
        } else {
            $data = unserialize($data);
        }

        if (is_null($data)) {
            return [];
        }

        $last = time();
        $list = [];

        foreach ($data as $item) {

            $crontab = new MineCrontab();
            $crontab->setCallback($item['target']);
            $crontab->setType($item['type']);
            $crontab->setEnable(true);
            $crontab->setCrontabId($item['id']);
            $crontab->setName($item['name']);
            $crontab->setParameter($item['parameter'] ?: '');
            $crontab->setRule($item['rule']);

            if (!$this->parser->isValid($crontab->getRule())) {
                console()->info('Crontab task ['.$item['name'].'] rule error, skipping execution');
                continue;
            }

            $time = $this->parser->parse($crontab->getRule(), $last);
            if ($time) {
                foreach ($time as $t) {
                    $list[] = clone $crontab->setExecuteTime($t);
                }
            }
        }
        return $list;
    }
}