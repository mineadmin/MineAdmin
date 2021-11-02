<p>1.创建生产者:</p>
php bin/hyperf.php gen:amqp-producer DemoProducer
<p>2.创建消费者:</p>
php bin/hyperf.php gen:amqp-consumer DemoConsumer
<p>3.修改生产者的注解,例如:</p>
<p>两个注解最好都要修改</p>
Producer(exchange: 'hyperf.exchange', routingKey: 'role.routing')
<p>4.修改消费者的注解,例如:</p>
<p>两个注解最好都要修改</p>
Consumer(exchange: 'hyperf.exchange', routingKey: 'role.routing', queue: 'role.queue', name: "role.queue", nums: 1)]
<p>5.依赖注入延迟队列类</p>
use Hyperf\Di\Annotation\Inject;
<br>
use Mine\Amqp\DelayProducer;
<br>
/**
<br>
 * @Inject
<br>
 * @var DelayProducer
<br>
 */
<br>
protected $producer;
<p>6.进行调用</p>
$message = new RoleProducer('message');
<br>
参数1:生产者对象 2:队列的是否确认机制 3:超时时间重试 (秒) 4:延迟时间 (秒) 如果为0或者不传,默认立即发送
<br>
$this->producer->produce($message,false,5,5);
<br>
其他的问题请见hyperf官方的AMQP模块文档
