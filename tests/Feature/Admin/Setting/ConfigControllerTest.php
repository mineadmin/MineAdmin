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
use App\Model\Setting\Config;
use App\Model\Setting\ConfigGroup;
use Hyperf\Collection\Arr;
use Hyperf\Stringable\Str;
use HyperfTests\Feature\Admin\ControllerCase;

/**
 * @internal
 * @coversNothing
 */
class ConfigControllerTest extends ControllerCase
{
    public function testList(): void
    {
        $token = $this->token;
        $url = '/admin/config';
        $result = $this->get($url);
        $configGroup = ConfigGroup::create([
            'name' => Str::random(),
            'code' => Str::random(),
            'remark' => Str::random(),
        ]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::UNAUTHORIZED->value);
        $result = $this->get($url, ['group_id' => $configGroup->id], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
        $enforce = $this->getEnforce();
        $enforce->addPermissionForUser($this->user->username, 'config:list');
        $result = $this->get($url, ['group_id' => $configGroup->id], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $enforce->deletePermissionForUser($this->user->username, 'config:list');
        $result = $this->get($url, ['group_id' => $configGroup->id], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
        $configGroup->forceDelete();
    }

    public function testCreate(): void
    {
        $token = $this->token;
        $url = '/admin/config';
        $configGroup = ConfigGroup::create([
            'name' => Str::random(),
            'code' => Str::random(),
            'remark' => Str::random(),
        ]);
        $fillableData = [
            'group_id' => $configGroup->id,
            'name' => Str::random(),
            'code' => Str::random(),
            'remark' => Str::random(),
            'key' => Str::random(),
            'value' => Str::random(),
            'input_type' => Str::random(),
            'config_select_data' => [
                'key' => Str::random(),
                'value' => Str::random(),
            ],
        ];
        $result = $this->post($url, $fillableData);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::UNAUTHORIZED->value);
        $result = $this->post($url, $fillableData, ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
        $enforce = $this->getEnforce();
        $enforce->addPermissionForUser($this->user->username, 'config:create');
        $result = $this->post($url, $fillableData, ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $enforce->deletePermissionForUser($this->user->username, 'config:create');
        $result = $this->post($url, $fillableData, ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
        $configGroup->forceDelete();
    }

    public function testEdit(): void
    {
        $token = $this->token;
        $configGroup = ConfigGroup::create([
            'name' => Str::random(),
            'code' => Str::random(),
            'remark' => Str::random(),
        ]);
        /**
         * @var Config $config
         */
        $config = $configGroup->configs()->create([
            'group_id' => $configGroup->id,
            'name' => Str::random(),
            'code' => Str::random(),
            'remark' => Str::random(),
            'key' => Str::random(),
            'value' => Str::random(),
            'input_type' => Str::random(),
            'config_select_data' => [
                'key' => Str::random(),
                'value' => Str::random(),
            ],
        ]);
        $url = '/admin/config/' . $config->key;
        $fillableData = [
            'group_id' => $configGroup->id,
            'name' => Str::random(),
            'remark' => Str::random(),
            'key' => Str::random(),
            'value' => Str::random(),
            'input_type' => Str::random(),
            'config_select_data' => [
                'key' => Str::random(),
                'value' => Str::random(),
            ],
        ];
        $result = $this->put($url, $fillableData);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::UNAUTHORIZED->value);
        $result = $this->put($url, $fillableData, ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
        $enforce = $this->getEnforce();
        $enforce->addPermissionForUser($this->user->username, 'config:edit');
        $result = $this->put($url, $fillableData, ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $enforce->deletePermissionForUser($this->user->username, 'config:edit');
        $result = $this->put($url, $fillableData, ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
        $config->setAttribute('key', $fillableData['key']);
        $config->refresh();
        $this->assertSame($config->name, $fillableData['name']);
        $this->assertSame($config->value, $fillableData['value']);
        $this->assertSame($config->remark, $fillableData['remark']);
        $this->assertSame($config->input_type, $fillableData['input_type']);
        $this->assertSame($config->config_select_data, $fillableData['config_select_data']);
        $config->forceDelete();
        $configGroup->forceDelete();
    }

    public function testDelete(): void
    {
        $token = $this->token;
        $configGroup = ConfigGroup::create([
            'name' => Str::random(),
            'code' => Str::random(),
            'remark' => Str::random(),
        ]);
        $config = $configGroup->configs()->create([
            'group_id' => $configGroup->id,
            'name' => Str::random(),
            'code' => Str::random(),
            'remark' => Str::random(),
            'key' => Str::random(),
            'value' => Str::random(),
            'input_type' => Str::random(),
            'config_select_data' => [
                'key' => Str::random(),
                'value' => Str::random(),
            ],
        ]);
        $url = '/admin/config/' . $config->key;
        $result = $this->delete($url);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::UNAUTHORIZED->value);
        $result = $this->delete($url, [], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
        $enforce = $this->getEnforce();
        $enforce->addPermissionForUser($this->user->username, 'config:delete');
        $result = $this->delete($url, [], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $enforce->deletePermissionForUser($this->user->username, 'config:delete');
        $result = $this->delete($url, [], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
        $config->forceDelete();
        $configGroup->forceDelete();
    }
}
