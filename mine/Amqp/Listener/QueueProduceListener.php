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
use Mine\Helper\Str;
use Mine\Amqp\Event\AfterProduce;
use Mine\Amqp\Event\BeforeProduce;
use Mine\Amqp\Event\FailToProduce;
use Mine\Amqp\Event\ProduceEvent;
use Mine\Amqp\Event\WaitTimeout;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Event\Annotation\Listener;

/**
 * @Listener
 */
class QueueProduceListener implements ListenerInterface
{
    private $service;
    private $exchangeName;
    private $routingKeyName;
    private $queueName;
    private $throwable;
    private $uuid;
    public function listen(): array
    {
        // 返回一个该监听器要监听的事件数组，可以同时监听多个事件
        return [
            AfterProduce::class,
            BeforeProduce::class,
            ProduceEvent::class,
            FailToProduce::class,
            WaitTimeout::class,
        ];
    }
    
    public function process(object $event)
    {
        $this->service = new SystemQueueService(new SystemQueueMapper());
        $producer = $event->producer;
        $this->throwable = $event->throwable ?? '';
        $delayTime = $event->delayTime ?? 0;
        $class = get_class($event);
        $func = lcfirst(trim(strrchr($class, '\\'),'\\'));
        // 事件触发后该监听器要执行的代码写在这里，比如该示例下的发送用户注册成功短信等
        $this->$func($producer,$delayTime);

    }

    /**
     * Description:生产前
     * User:mike
     * @param $producer
     */
    public function beforeProduce($producer,$delayTime){
        $queueName = strchr($producer->getRoutingKey(),'.',true).'.queue';
        $this->exchangeName = $producer->getExchange();
        $this->routingKeyName = $producer->getRoutingKey();
        $this->queueName = $queueName;
        
        $uuid = Str::getUUID();
        $this->uuid = $uuid;
        $content = ['uuid'=>$uuid,'data'=>json_decode($producer->payload())];
        $producer->setPayload($content);
        $data = [
            'uuid'=>$uuid,
            'exchange_name'=>$this->exchangeName,
            'routing_key_name'=>$this->routingKeyName,
            'queue_name'=>$this->queueName,
            'queue_content'=>$producer->payload(),
            'delay_time'=>$delayTime,
            'produce_status'=>SystemQueue::PRODUCE_STATUS_SUCCESS
        ];
        $this->service->save($data);
    }

    /**
     * Description:生产中
     * User:mike
     * @param $producer
     */
    public function produceEvent($producer,$delayTime){
//        $condition = ['uuid'=>$this->uuid];
//        $data = ['produce_status'=>SystemRabbitmq::PRODUCE_STATUS_DOING];
//        $this->service->update($condition,$data);
    }

    /**
     * Description:生产后
     * User:mike
     * @param $producer
     */
    public function afterProduce($producer,$delayTime){
//        $condition = ['uuid'=>$this->uuid];
//        $data = ['produce_status'=>SystemRabbitmq::PRODUCE_STATUS_SUCCESS];
//        $this->service->update($condition,$data);
    }
    /**
     * Description:生产失败
     * User:mike
     * @param $producer
     */
    public function failToProduce($producer,$delayTime){
        $condition = ['uuid'=>$this->uuid];
        $data = ['produce_status'=>SystemQueue::PRODUCE_STATUS_FAIL];
        if($this->throwable){
            $data['log_content'] = $this->throwable->getMessage();
        }
        $this->service->update($condition,$data);

    }
}
