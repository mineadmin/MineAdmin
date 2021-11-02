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

namespace Mine\Listener;

use App\System\Service\SystemOperLogService;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Mine\Event\Operation;
use Mine\MineRequest;
use Psr\Container\ContainerInterface;

/**
 * Class OperactionListener
 * @package Mine\Listener
 * @Listener
 */
class OperactionListener implements ListenerInterface
{
    protected $container;

    protected $ignoreRouter = ['/login', '/getInfo', '/system/captcha'];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return string[] returns the events that you want to listen
     */
    public function listen(): array
    {
        return [
            Operation::class
        ];
    }

    /**
     * Handle the Event when the event is triggered, all listeners will
     * complete before the event is returned to the EventDispatcher.
     * @throws \HyperfExt\Jwt\Exceptions\JwtException
     * @var Operation $event
     */
    public function process(object $event)
    {
        $requestInfo = $event->getRequestInfo();
        if (!in_array($requestInfo['router'], $this->ignoreRouter)) {
            $service = $this->container->get(SystemOperLogService::class);
            $requestInfo['request_data'] = json_encode($requestInfo['request_data'], JSON_UNESCAPED_UNICODE);
//            $requestInfo['response_data'] = $requestInfo['response_data'];
            $service->save($requestInfo);
        }
    }
}