<?php

$messageConsumerFile = dirname(__DIR__).'/app/System/Queue/Consumer/MessageConsumer.php';
$messageProducerFile = dirname(__DIR__).'/app/System/Queue/Producer/MessageProducer.php';
$str = file_get_contents($messageConsumerFile);

$replaceStr = str_replace('// #[Consumer(exchange: "mineadmin", routingKey: "message.routing", queue: "message.queue", name: "message.queue", nums: 1)]','#[Consumer(exchange: "mineadmin", routingKey: "message.routing", queue: "message.queue", name: "message.queue", nums: 1)]',$str);
file_put_contents($messageConsumerFile,$replaceStr);

$str = file_get_contents($messageProducerFile);

$replaceStr = str_replace('// #[Producer(exchange: "mineadmin", routingKey: "message.routing")]','#[Producer(exchange: "mineadmin", routingKey: "message.routing")]',$str);

file_put_contents($messageProducerFile,$replaceStr);

