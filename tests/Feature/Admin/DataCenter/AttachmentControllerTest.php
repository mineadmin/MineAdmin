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
use App\Model\Attachment;
use Carbon\Carbon;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\ModelNotFoundException;
use HyperfTests\Feature\Admin\ControllerCase;

/**
 * @internal
 * @coversNothing
 */
final class AttachmentControllerTest extends ControllerCase
{
    public function testPageList(): void
    {
        $token = $this->token;
        $url = '/admin/attachment/list';
        $code = 'dataCenter:attachment:list';
        $result = $this->get($url);
        self::assertSame(Arr::get($result, 'code'), ResultCode::UNAUTHORIZED->value);
        $result = $this->get($url, [], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
        $this->forAddPermission($code);
        $result = $this->get($url, [], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        self::assertIsArray(Arr::get($result, 'data'));
        self::assertArrayHasKey('list', Arr::get($result, 'data'));
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
        $code = 'dataCenter:attachment:delete';
        $result = $this->delete($url . '/' . $entity->id);
        self::assertSame(Arr::get($result, 'code'), ResultCode::UNAUTHORIZED->value);
        $result = $this->delete($url . '/' . $entity->id, [], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::FORBIDDEN->value);
        $this->forAddPermission($code);
        $result = $this->delete($url . '/' . $entity->id, [], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        self::assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
        $this->expectException(ModelNotFoundException::class);
        $entity->refresh();
    }
}
