<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

use Symfony\Component\Finder\Finder;

return [
    'enable' => true,
    'port' => 9503,
    'json_dir' => BASE_PATH . '/storage/swagger',
    'html' => null,
    'url' => '/swagger',
    'auto_generate' => true,
    'scan' => [
        'paths' => Finder::create()
            ->in([BASE_PATH.'/app/Http',BASE_PATH.'/app/Schema'])
            ->name('*.php')
            ->getIterator(),
    ],
    'processors' => [
//        new FormRequestProcessor
    ],
];
