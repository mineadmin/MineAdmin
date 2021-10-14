<?php
declare(strict_types=1);
namespace App;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Mine\Helper\LoginUser;
use Mine\MineCollection;
use Mine\MineController;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class TestController
 * @Controller(prefix="test")
 */
class TestController extends MineController
{
    /**
     * @Inject
     * @var LoggerFactory
     */
    protected $log;

    /**
     * @Inject
     * @var StdoutLoggerInterface
     */
    protected $console;

    /**
     * @GetMapping("index")
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        // 写日志
        $this->log->get('test')->debug('debug....');
        // 打印控制台
        $this->console->info('info...');

        $data = ['test' => 'abc'];

        return $this->success($data);
    }

    /**
     * @GetMapping("notice")
     * @return ResponseInterface
     */
    public function notice(): ResponseInterface
    {
        // 写日志
        $this->log->get('test')->debug('debug....');
        // 打印控制台
        $this->console->error('error...');

        return $this->success('error');
    }
}