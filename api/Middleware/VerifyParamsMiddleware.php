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
namespace Api\Middleware;

use App\System\Model\SystemApi;
use Mine\Annotation\Api\MApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class VerifyParamsMiddleware implements MiddlewareInterface
{
    /**
     * 验证接口参数
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler):ResponseInterface
    {
        $apiData = $request->getParsedBody()['apiData'];
        $requestData = $this->getRequestData($request, $apiData);

        $columns = container()->get(\App\System\Service\SystemApiService::class)
            ->getColumnListByApiId((string) $apiData['id'])['api_column'];


        // todo...

        return $handler->handle($request);
    }

    protected function getRequestData(ServerRequestInterface $request, &$apiData): array
    {
        $bodyData = $request->getParsedBody(); unset($bodyData['apiData']);

        if ($apiData['request_mode'] === MApi::METHOD_GET) {
            $params = $request->getQueryParams();
        } else if ($apiData['request_mode'] === MApi::METHOD_ALL) {
            $params = array_merge($request->getQueryParams(), $bodyData);
        } else {
            $params = &$bodyData;
        }

        return $params;
    }
}