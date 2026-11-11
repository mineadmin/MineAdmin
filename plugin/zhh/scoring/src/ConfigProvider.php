<?php

declare(strict_types=1);
/**
 * This file is part of the zhh/scoring plugin.
 *
 * 插件啟動配置：把插件 src/ 目錄納入註解掃描（路由、中間件、權限碼、Crontab、WebSocket 均依賴註解）。
 * 全局 config/autoload/annotations.php 只掃描 app 與 kernel，插件路徑必須由本類返回合併（方案 §4.4）。
 */

namespace Plugin\Zhh\Scoring;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            // 合併到 config/autoload/annotations.php 的 scan.paths
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            // 註冊插件命令行命令（bin/hyperf.php scoring:publish，方案 §4.5）
            'commands' => [
                Command\PublishCommand::class,
            ],
        ];
    }
}
