<?php

namespace HyperfTests\Feature\Admin;

use App\Http\Common\ResultCode;
use App\Model\Permission\User;
use Hyperf\Collection\Arr;
use Hyperf\Stringable\Str;
use HyperfTests\HttpTestCase;

class PassportControllerTest extends HttpTestCase
{
    public function testLoginUsernameNotFound()
    {
        User::where('username','admin')->forceDelete();
        $result = $this->post('/admin/passport/login', [
            'username' => 'admin',
            'password' => 'admin'
        ]);
        $this->assertSame(Arr::get($result,'code'),ResultCode::UNPROCESSABLE_ENTITY->value);
    }

    public function testLoginUserPasswordFail()
    {
        $user = User::create([
            'username'  =>  Str::random(10),
            'password'  =>  password_hash('123456',PASSWORD_DEFAULT),
        ]);

        $result = $this->post('/admin/passport/login', [
            'username' => $user->username,
            'password' => 'admin'
        ]);
        $this->assertSame(Arr::get($result,'code'),ResultCode::UNPROCESSABLE_ENTITY->value);
        $user->forceDelete();
    }

    public function testLoginSuccess()
    {
        $user = User::create([
            'username'  =>  Str::random(10),
            'password'  =>  123456,
        ]);

        $result = $this->post('/admin/passport/login', [
            'username' => $user->username,
            'password' => '123456'
        ]);
        $this->assertSame(Arr::get($result,'code'),ResultCode::SUCCESS->value);
        $this->assertArrayHasKey('token',$result['data']);
        $this->assertArrayHasKey('expire_at',$result['data']);
        $this->assertIsInt($result['data']['expire_at']);
        $user->forceDelete();
    }

    public function testLogoutFail()
    {
        $result = $this->post('/admin/passport/logout');
        $this->assertSame(Arr::get($result,'code'),ResultCode::UNAUTHORIZED->value);
    }

    public function testLogoutSuccess()
    {
        $user = User::create([
            'username'  =>  Str::random(10),
            'password'  =>  123456,
        ]);
        $result = $this->post('/admin/passport/login', [
            'username' => $user->username,
            'password' => '123456'
        ]);
        $this->assertSame(Arr::get($result,'code'),ResultCode::SUCCESS->value);
        $this->assertArrayHasKey('token',$result['data']);
        $this->assertArrayHasKey('expire_at',$result['data']);
        $this->assertIsInt($result['data']['expire_at']);
        $token = $result['data']['token'];
        $result = $this->post('/admin/passport/logout',[],[
            'Authorization' => 'Bearer '.$token
        ]);
        $this->assertSame(Arr::get($result,'code'),ResultCode::SUCCESS->value);
        $result = $this->post('/admin/passport/logout',[],[
            'Authorization' => 'Bearer '.$token
        ]);
        $this->assertSame(Arr::get($result,'code'),ResultCode::UNAUTHORIZED->value);
        $user->forceDelete();
    }

    public function testGetInfo(): void
    {
        $user = User::create([
            'username'  =>  Str::random(10),
            'password'  =>  123456,
        ]);
        $result = $this->post('/admin/passport/login', [
            'username' => $user->username,
            'password' => '123456'
        ]);
        $this->assertSame(Arr::get($result,'code'),ResultCode::SUCCESS->value);
        $this->assertArrayHasKey('token',$result['data']);
        $this->assertArrayHasKey('expire_at',$result['data']);
        $this->assertIsInt($result['data']['expire_at']);

        $info = $this->get('/admin/passport/getInfo');

        $this->assertSame(Arr::get($info,'code'),ResultCode::UNAUTHORIZED->value);

        $info = $this->get('/admin/passport/getInfo',[],[
            'Authorization' => 'Bearer '.$result['data']['token']
        ]);

        $this->assertSame(Arr::get($info,'code'),ResultCode::SUCCESS->value);
        $attributes = $user->toArray();
        foreach (Arr::except($attributes,['password']) as $key => $value){
            $this->assertSame($value,Arr::get($info,'data.'.$key));
        }
        $user->forceDelete();
    }
}