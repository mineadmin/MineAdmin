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

use App\Kernel\Swagger\Processor\FormRequestProcessor;
use App\Kernel\Swagger\Processor\MappingProcessor;
use Symfony\Component\Finder\Finder;

return [
    'enable' => env('APP_DEBUG'),
    'port' => 9503,
    'json_dir' => BASE_PATH . '/storage/swagger',
    'html' => null,
    'url' => '/swagger',
    'auto_generate' => true,
    'scan' => [
        'paths' => Finder::create()->in(BASE_PATH.'/app/Http')->name('*.php')->getIterator(),
    ],
    'processors' => [
        new FormRequestProcessor,
    ],
];
