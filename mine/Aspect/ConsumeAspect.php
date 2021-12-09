<?php
/**
 * Description:消费切面
 * Created by phpStorm.
 * User: mike
 * Date: 2021/11/19
 * Time: 下午2:14
 */
declare(strict_types=1);
namespace Mine\Aspect;

use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Exception\Exception;
use Hyperf\Utils\ApplicationContext;
use Mine\Amqp\Event\AfterConsume;
use Mine\Amqp\Event\BeforeConsume;
use Mine\Amqp\Event\FailToConsume;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class ConsumeAspect
 * @package Mine\Aspect
 * @Aspect
 */
class ConsumeAspect extends AbstractAspect
{
    public $classes = [
        'Hyperf\Amqp\Message\ConsumerMessage::consumeMessage'
    ];
    
    /**
     * @param ProceedingJoinPoint $proceedingJoinPoint
     * @return mixed
     * @throws Exception
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        
        try{
            // 切面切入后，执行对应的方法会由此来负责
            // $proceedingJoinPoint 为连接点，通过该类的 process() 方法调用原方法并获得结果
            // 在调用前进行某些处理
            $data = $proceedingJoinPoint->getArguments()[0];
            $message = $proceedingJoinPoint->getArguments()[1];
            $eventDispatcher = ApplicationContext::getContainer()->get(EventDispatcherInterface::class);
            //消费之前事件
            $eventDispatcher->dispatch(new BeforeConsume($message,$data));

            $result = $proceedingJoinPoint->process();
            // 在调用后进行某些处理
            //消费之后事件
            $eventDispatcher->dispatch(new AfterConsume($message,$data,''));
            return $result;
        }catch(\Throwable $e){
            //发生错误处理
            $eventDispatcher->dispatch(new FailToConsume($message, $data, $e));
        }
        
    }
}
