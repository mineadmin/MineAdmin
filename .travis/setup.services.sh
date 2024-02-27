#!/usr/bin/env bash
 docker run --name mysql -p 3306:3306 -e MYSQL_ALLOW_EMPTY_PASSWORD=true -d mysql:${MYSQL_VERSION} --bind-address=0.0.0.0 --default-authentication-plugin=mysql_native_password &
docker run --name postgres -p 5432:5432 -e POSTGRES_PASSWORD=postgres -d postgres:${PGSQL_VERSION} &
docker run --name redis -p 6379:6379 -d redis &
docker run -d --name dev-consul -e CONSUL_BIND_INTERFACE=eth0 -p 8500:8500 consul:1.15.4 &
docker run --name nsq -p 4150:4150 -p 4151:4151 -p 4160:4160 -p 4161:4161 -p 4170:4170 -p 4171:4171 --entrypoint /bin/nsqd -d nsqio/nsq:latest &
docker run -d --restart=always --name rabbitmq -p 4369:4369 -p 5672:5672 -p 15672:15672 -p 25672:25672 rabbitmq:management-alpine &
wait
docker ps -a