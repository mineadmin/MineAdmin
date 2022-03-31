<?php

declare(strict_types=1);
return [
    // enable false 将不会启动 swagger 服务
    'enable' => true,
    'output_dir' => BASE_PATH . '/runtime/swagger',
    'prefix_url' => env('API_DOCS_PREFIX_URL', '/swagger'),
    //认证api key
    'security_api_key' => ['Authorization'],
    //替换验证属性
    'validation_custom_attributes' => false,
    //全局responses
    'responses' => [
        401 => ['description' => 'Unauthorized'],
    ],
    // swagger 的基础配置
    'swagger' => [
        'swagger' => '2.0',
        'info' => [
            'description' => 'MineAdmin所有后端接口列表',
            'version' => '1.0.0',
            'title' => 'MineAdmin接口文档',
        ],
        'host' => '',
        'schemes' => [],
    ],
];
