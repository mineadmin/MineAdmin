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

namespace HyperfTests\Feature\Admin\Setting;

use App\Http\Common\ResultCode;
use App\Model\Setting\ConfigGroup;
use Hyperf\Collection\Arr;
use Hyperf\Stringable\Str;
use HyperfTests\Feature\Admin\ControllerCase;

/**
 * @internal
 * @coversNothing
 */
class ConfigGroupControllerTest extends ControllerCase
{
    public function testGetConfigGroup(): void
    {
        $token = $this->token;
        $url = '/admin/config/group';
        $result = $this->get($url);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::UNAUTHORIZED->value);
        $result = $this->get($url, [], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
        $enforce = $this->getEnforce();
        $enforce->addPermissionForUser($this->user->username, 'config:group:list');
        $result = $this->get($url, [], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $enforce->deletePermissionForUser($this->user->username, 'config:group:list');
        $result = $this->get($url, [], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
    }

    public function testAddConfigGroup(): void
    {
        $token = $this->token;
        $url = '/admin/config/group';
        $fillableData = [
            'name' => Str::random(),
            'code' => Str::random(),
            'remark' => Str::random(),
        ];
        $result = $this->post($url, $fillableData);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::UNAUTHORIZED->value);
        $result = $this->post($url, $fillableData, ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
        $enforce = $this->getEnforce();
        $enforce->addPermissionForUser($this->user->username, 'config:group:create');
        $result = $this->post($url, $fillableData, ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $enforce->deletePermissionForUser($this->user->username, 'config:group:create');
        $result = $this->post($url, $fillableData, ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
    }

    public function testDeleteConfigGroup(): void
    {
        $token = $this->token;
        $configGroup = ConfigGroup::create([
            'name' => Str::random(),
            'code' => Str::random(),
            'remark' => Str::random(),
        ]);
        $url = '/admin/config/group/' . $configGroup->id;
        $result = $this->delete($url);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::UNAUTHORIZED->value);
        $result = $this->delete($url, [], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
        $enforce = $this->getEnforce();
        $enforce->addPermissionForUser($this->user->username, 'config:group:delete');
        $result = $this->delete($url, [], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $enforce->deletePermissionForUser($this->user->username, 'config:group:delete');
        $result = $this->delete($url, [], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
    }

    public function testEditConfigGroup(): void
    {
        $token = $this->token;
        $configGroup = ConfigGroup::create([
            'name' => Str::random(),
            'code' => Str::random(),
            'remark' => Str::random(),
        ]);
        $url = '/admin/config/group/' . $configGroup->id;
        $fillableData = [
            'name' => Str::random(),
            'code' => Str::random(),
            'remark' => Str::random(),
        ];
        $result = $this->put($url, $fillableData);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::UNAUTHORIZED->value);
        $result = $this->put($url, $fillableData, ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
        $enforce = $this->getEnforce();
        $enforce->addPermissionForUser($this->user->username, 'config:group:edit');
        $result = $this->put($url, $fillableData, ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $enforce->deletePermissionForUser($this->user->username, 'config:group:edit');
        $result = $this->put($url, $fillableData, ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
        $configGroup->refresh();
        $this->assertSame($configGroup->name, $fillableData['name']);
        $this->assertSame($configGroup->code, $fillableData['code']);
        $this->assertSame($configGroup->remark, $fillableData['remark']);
        $configGroup->forceDelete();
    }
}
