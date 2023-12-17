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

namespace Api\Listener;

use App\System\Service\SystemApiLogService;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Mine\Event\ApiAfter;
use Mine\Helper\Str;
use Mine\MineRequest;
use Hyperf\Coroutine\Coroutine;

/**
 * API访问日志保存
 */
#[Listener]
class ApiLogListener implements ListenerInterface
{

    /**
     * 监听事件
     * @return string[]
     */
    public function listen(): array
    {
        return [
            ApiAfter::class
        ];
    }

    /**
     * 事件处理
     * @param object $event
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function process(object $event): void
    {

        
        /* @var $event ApiAfter */
        $data = $event->getApiData();
        $request = container()->get(MineRequest::class);
        $service = container()->get(SystemApiLogService::class);

        if (empty($data)) {
            // 只记录异常日志，但并不抛出异常，程序正常走下去
            logger('Api Access Log')->error('API数据为空，访问日志无法记录，以下为 Request 信息：'. json_encode($request));
        } else {
            // 保存日志
            $reqData = $request->getParsedBody();
            unset($reqData['apiData']);
            $response = $event->getResult();

            // 适配 注解api, 没有id 因为没写库
            $app_id = $data['id'] ?? 0;
            $app_id = is_numeric($app_id) ? $app_id : 0;

            // 放协程 尽量快速响应接口， 以及延时写库 这里没试，怕协程切换拿不到数据，先把需要的都取出来

            $queryParams = $request->getQueryParams();
            $responseCode = $response->getStatusCode();

            // 返回内容以免过大，暂不保存访问内容
            // $responseData => $response->getBody()->getContents(),
            $responseData = '';


            $ip = $request->ip();
            $ipLocation = Str::ipToRegion($ip);
            $accessTime = date('Y-m-d H:i:s');
            Coroutine::create(function() use ($service, $app_id, $data, $reqData, $queryParams, $responseCode, $responseData, $ip, $ipLocation, $accessTime) {
                // 随机延时写入
                sleep(rand(5, 30));
                $service->save([
                    'api_id' => $app_id,
                    'api_name' => $data['name'],
                    'access_name' => $data['access_name'],
                    'request_data' => [ 'data' => $reqData, 'params' => $queryParams ],
                    'response_code' => $responseCode,
                    // 'response_data' => $responseData,
                    'ip' => $ip,
                    'ip_location' => $ipLocation,
                    'access_time' => $accessTime
                ]);
            });
        }
    }
}