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

namespace HyperfTests\Feature\Admin\DataCenter;

use App\Http\Common\ResultCode;
use App\Model\DataCenter\Attachment;
use App\Model\Setting\Config;
use Carbon\Carbon;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\ModelNotFoundException;
use HyperfTests\Feature\Admin\ControllerCase;

/**
 * @internal
 * @coversNothing
 */
class AttachmentControllerTest extends ControllerCase
{
    public function testUpload(): void
    {
        $token = $this->token;
        $faker = $this->fakerGenerator();
        $url = '/admin/attachment/upload';
        $code = 'attachment:upload';
        $file = $faker->image();
        Config::query()->where('key', 'upload.open')->forceDelete();
        /**
         * @var Config $config
         */
        $config = Config::query()->where('key', 'upload.open')->firstOrNew([
            'key' => 'upload.open',
            'value' => 0,
            'input_type' => 'select',
            'group_id' => 0,
        ]);
        $config->value = 0;
        $config->save();
        $result = $this->file($url, [
            'name' => 'file',
            'file' => $file,
        ]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::UNAUTHORIZED->value);

        $enforce = $this->getEnforce();
        $this->assertFalse($enforce->hasPermissionForUser($this->user->username, $code));
        $this->assertTrue($enforce->addPermissionForUser($this->user->username, $code));
        $result = $this->file($url, [
            'name' => 'file',
            'file' => $file,
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::UNPROCESSABLE_ENTITY->value);

        $config->value = 1;
        $config->save();
        $result = $this->file($url, [
            'name' => 'file',
            'file' => $file,
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $this->assertIsArray(Arr::get($result, 'data'));
        $this->assertArrayHasKey('storage_mode', Arr::get($result, 'data'));
        $this->assertArrayHasKey('object_name', Arr::get($result, 'data'));
        $this->assertArrayHasKey('hash', Arr::get($result, 'data'));
        $this->assertArrayHasKey('mime_type', Arr::get($result, 'data'));
        $this->assertArrayHasKey('storage_path', Arr::get($result, 'data'));
        $this->assertArrayHasKey('suffix', Arr::get($result, 'data'));
        $this->assertArrayHasKey('size_byte', Arr::get($result, 'data'));
        $this->assertArrayHasKey('size_info', Arr::get($result, 'data'));
        $this->assertArrayHasKey('url', Arr::get($result, 'data'));
    }

    public function testPageList(): void
    {
        $token = $this->token;
        $url = '/admin/attachment/list';
        $code = 'attachment:list';
        $result = $this->get($url);
        $enforce = $this->getEnforce();
        $this->assertSame(Arr::get($result, 'code'), ResultCode::UNAUTHORIZED->value);
        $result = $this->get($url, [], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
        $this->assertFalse($enforce->hasPermissionForUser($this->user->username, $code));
        $this->assertTrue($enforce->addPermissionForUser($this->user->username, $code));
        $result = $this->get($url, [], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $this->assertIsArray(Arr::get($result, 'data'));
        $this->assertArrayHasKey('list', Arr::get($result, 'data'));
    }

    public function testDelete(): void
    {
        $token = $this->token;
        $faker = $this->fakerGenerator();
        $entity = Attachment::create([
            'storage_mode' => rand(1, 4),
            'origin_name' => $faker->name,
            'object_name' => $faker->name,
            'hash' => $faker->md5,
            'mime_type' => $faker->mimeType(),
            'storage_path' => $faker->word,
            'suffix' => $faker->fileExtension(),
            'size_byte' => $faker->randomNumber(),
            'size_info' => $faker->randomNumber(),
            'url' => $faker->url,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'remark' => $faker->sentence,
        ]);
        $url = '/admin/attachment';
        $code = 'attachment:delete';
        $enforce = $this->getEnforce();
        $result = $this->delete($url . '/' . $entity->id);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::UNAUTHORIZED->value);
        $result = $this->delete($url . '/' . $entity->id, [], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
        $this->assertFalse($enforce->hasPermissionForUser($this->user->username, $code));
        $this->assertTrue($enforce->addPermissionForUser($this->user->username, $code));
        $result = $this->delete($url . '/' . $entity->id, [], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $this->expectException(ModelNotFoundException::class);
        $entity->refresh();
    }
}
