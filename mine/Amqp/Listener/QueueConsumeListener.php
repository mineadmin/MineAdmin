<?php
/**
 * Description:队列消费监听器
 * Created by phpStorm.
 * User: mike
 * Date: 2021/9/30
 * Time: 3:13 下午
 */
declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Mine\Amqp\Listener;

use App\System\Mapper\SystemQueueMapper;
use App\System\Model\SystemQueue;
use App\System\Service\SystemQueueService;
use Mine\Amqp\Event\AfterConsume;
use Mine\Amqp\Event\BeforeConsume;
use Mine\Amqp\Event\ConsumeEvent;
use Mine\Amqp\Event\FailToConsume;
use Mine\Amqp\Event\WaitTimeout;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Event\Annotation\Listener;
/**
 * @Listener
 */
class QueueConsumeListener implements ListenerInterface
{
    private $service;
    private $uuid;
    private $throwable;
    public function listen(): array
    {
        // 返回一个该监听器要监听的事件数组，可以同时监听多个事件
        return [
            AfterConsume::class,
            BeforeConsume::class,
            ConsumeEvent::class,
            FailToConsume::class,
            WaitTimeout::class,
        ];
    }

    public function process(object $event)
    {
        $this->service = new SystemQueueService(new SystemQueueMapper());
        $message = $event->message;
        $this->throwable = $event->throwable ?? '';
        if($message){
            $this->uuid = $event->data['uuid'];
            
            $class = get_class($event);
            $func = lcfirst(trim(strrchr($class, '\\'),'\\'));
            // 事件触发后该监听器要执行的代码写在这里，比如该示例下的发送用户注册成功短信等
            $this->$func($message);
        }
    }

    /**
     * Description:消费前
     * User:mike
     * @param $producer
     */
    public function beforeConsume($message){
        $condition = ['uuid'=>$this->uuid];
        $data = ['consume_status'=>SystemQueue::CONSUME_STATUS_DOING];
        $this->service->update($condition,$data);
    }

    /**
     * Description:消费中
     * User:mike
     * @param $producer
     */
    public function consumeEvent($message){
//        $condition = ['uuid'=>$this->uuid];
//        $data = ['produce_status'=>SystemRabbitmq::CONSUME_STATUS_DOING];
//        $this->service->update($condition,$data);
    }

    /**
     * Description:消费后
     * User:mike
     * @param $producer
     */
    public function afterConsume($message){
        $condition = ['uuid'=>$this->uuid];
        $data = ['consume_status'=>SystemQueue::CONSUME_STATUS_SUCCESS];
        $this->service->update($condition,$data);
    }
    /**
     * Description:消费失败
     * User:mike
     * @param $producer
     */
    public function failToConsume($message){
        $condition = ['uuid'=>$this->uuid];
        $data = ['consume_status'=>SystemQueue::CONSUME_STATUS_4];
        if($this->throwable){
            $data['log_content'] = $this->throwable->getMessage();
        }
        $this->service->update($condition,$data);
    }
}
