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

namespace HyperfTests\Feature\Admin;

use App\Model\Permission\User;
use Hyperf\Collection\Arr;
use Hyperf\Stringable\Str;
use Hyperf\Testing\Assert;
use HyperfTests\HttpTestCase;

/**
 * @mixin   HttpTestCase
 */
trait GetTokenTrait
{
    public function generatorUser(): User
    {
        return User::create([
            'username' => Str::random(10),
            'password' => 123456,
        ]);
    }

    public function getToken(User $user): string
    {
        $result = $this->post('/admin/passport/login', [
            'username' => $user->username,
            'password' => $this->getPassword(),
        ]);
        if (! \is_array($result)) {
            Assert::fail('Get token failed.');
        }
        if (! Arr::has($result, 'data.access_token')) {
            Assert::fail('Get token failed.');
        }
        return Arr::get($result, 'data.access_token');
    }

    protected function getPassword(): string
    {
        if (property_exists($this, 'password')) {
            return $this->password;
        }
        return '123456';
    }
}
