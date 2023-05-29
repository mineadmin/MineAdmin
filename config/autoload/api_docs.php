<?php

declare(strict_types=1);

return [
    // enable false 将不会启动 swagger 服务
    'enable' => env('APP_ENV') !== 'prod',
    'format' => 'json',
    'output_dir' => BASE_PATH . '/runtime/swagger',
    'prefix_url' => env('API_DOCS_PREFIX_URL', '/swagger'),
    // 替换验证属性
    'validation_custom_attributes' => true,
    // 全局responses
    'responses' => [
        ['response' => 401, 'description' => 'Unauthorized'],
        ['response' => 500, 'description' => 'System error'],
    ],
    // swagger 的基础配置  会映射到OpenAPI对象
    'swagger' => [
        'info' => [
            'title' => 'MineAdmin API DOC',
            'version' => '1.1',
            'description' => 'MineAdmin后台接口列表',
        ],
        'servers' => [
            [
                'url' => 'http://127.0.0.1:9501',
                'description' => '本地服务器',
            ],
        ],
        'components' => [
            'securitySchemes' => [
                [
                    'securityScheme' => 'Authorization',
                    'type' => 'apiKey',
                    'in' => 'header',
                    'name' => 'Authorization',
                ],
            ],
        ],
        'security' => [
            ['Authorization' => []],
        ],
        'externalDocs' => [
            'description' => 'Find out more about Swagger',
            'url' => 'https://github.com/tw2066/api-docs',
        ],
    ],
];
