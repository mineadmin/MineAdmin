<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace HyperfTests;

use Faker\Factory;
use Faker\Generator;
use Hyperf\Testing\Client;
use Hyperf\Testing\Concerns\RunTestsInCoroutine;
use PHPUnit\Framework\TestCase;

/**
 * Class HttpTestCase.
 * @method get($uri, $data = [], $headers = [])
 * @method post($uri, $data = [], $headers = [])
 * @method put($uri, $data = [], $headers = [])
 * @method delete($uri, $data = [], $headers = [])
 * @method json($uri, $data = [], $headers = [])
 * @method file($uri, $data = [], $headers = [])
 * @method request($method, $path, $options = [])
 */
abstract class HttpTestCase extends TestCase
{
    use RunTestsInCoroutine;

    /**
     * @var Client
     */
    protected $client;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name);
        $this->client = make(Client::class);
    }

    public function __call($name, $arguments)
    {
        return $this->client->{$name}(...$arguments);
    }

    protected function fakerGenerator(): Generator
    {
        return Factory::create();
    }
}
