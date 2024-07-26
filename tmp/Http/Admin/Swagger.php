<?php

namespace App\Http\Admin;

use Hyperf\Swagger\Annotation\Info;
use Hyperf\Swagger\Annotation\Server;

#[Server(
    url: 'http://127.0.0.1:9501',
    description: '本地服務qweqwe',
)]
#[Info(
    version: '3.0.0-dev',
    description: 'MineAdmin 是一款基于 Hyperf 开发的开源管理系统，提供了用户管理、权限管理、系统设置、系统监控等功能。',
    title: 'MineAdmin',
)]
class Swagger
{

}