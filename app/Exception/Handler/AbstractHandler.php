<?php

namespace App\Exception\Handler;

use App\Http\Common\Result;
use App\Kernel\Log\UuidRequestIdProcessor;
use Hyperf\Codec\Json;
use Hyperf\Contract\ApplicationInterface;
use Hyperf\Contract\ConfigInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerInterface;
use Swow\Psr7\Message\ResponsePlusInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Throwable;

abstract class AbstractHandler extends ExceptionHandler
{
    public function __construct(
        private readonly ConfigInterface $config,
        private readonly ContainerInterface $container,
        private readonly LoggerFactory $loggerFactory
    ){}

    abstract function handleResponse(\Throwable $throwable): Result;

    public function handle(Throwable $throwable, ResponsePlusInterface $response)
    {
        $this->report($throwable);
        return value(function (ResponsePlusInterface $responsePlus){
            // 如果是 debug 模式，自动处理跨域
            if ($this->isDebug()){
                $responsePlus
                    ->setHeader('Access-Control-Allow-Origin', '*')
                    ->setHeader('Access-Control-Allow-Credentials', 'true')
                    ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, DELETE, OPTIONS')
                    ->setHeader('Access-Control-Allow-Headers', 'DNT,Keep-Alive,User-Agent,Cache-Control,Content-Type,Authorization');
            }
            return $responsePlus;
        },$this->handlerRequestId(
            $this->handlerResult(
                $response,
                $this->handleResponse($throwable)
            )
        ));
    }

    /**
     * 处理result 打包到 response body 中
     */
    protected function handlerResult(ResponsePlusInterface $responsePlus,Result $result): ResponsePlusInterface
    {
        return $responsePlus
            ->setHeader('Content-Type', 'application/json; charset=utf-8')
            ->setBody(new SwooleStream(Json::encode($result)));
    }

    /**
     * 处理 response 加上 request-id 信息
     */
    private function handlerRequestId(ResponsePlusInterface $responsePlus): ResponsePlusInterface
    {
        return $responsePlus->setHeader('Request-Id',UuidRequestIdProcessor::getUuid());
    }

    protected function isDebug(): bool
    {
        return (boolean)$this->config->get('debug');
    }

    /**
     * 上报日志+打印错误
     */
    public function report(Throwable $throwable)
    {
        // 如果是debug模式，打印错误到控制台
        if ($this->isDebug()){
            $this->container->get(ApplicationInterface::class)->renderThrowable($throwable,new ConsoleOutput());
        }
        $this->loggerFactory
            ->get('error')
            ->error($throwable->getMessage(),['exception' => $throwable]);
    }


}