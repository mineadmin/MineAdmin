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

use App\Http\Common\ResultCode;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\DbConnection\Model\Model;

class CrudControllerCase extends ControllerCase
{
    public function casePageList(string $uri, string $roleCode): void
    {
        $token = $this->token;
        $result = $this->get($uri);
        $this->assertSame($result['code'], ResultCode::UNAUTHORIZED->value);
        $result = $this->get($uri, ['token' => $token]);
        $this->assertSame($result['code'], ResultCode::FORBIDDEN->value);
        $this->assertFalse($this->hasPermissions($roleCode));
        $this->assertTrue($this->addPermissions($roleCode));
        $this->assertTrue($this->hasPermissions($roleCode));
        $result = $this->get($uri, ['token' => $token]);
        $this->assertSame($result['code'], ResultCode::SUCCESS->value);
        $this->deletePermissions($roleCode);
        $result = $this->get($uri, ['token' => $token]);
        $this->assertSame($result['code'], ResultCode::FORBIDDEN->value);
    }

    /**
     * @param class-string<Model> $model
     */
    public function caseCreate(string $uri, string $roleCode, array $fillable, string $model, array $required = []): void
    {
        $token = $this->token;
        $result = $this->post($uri);
        $this->assertSame($result['code'], ResultCode::UNPROCESSABLE_ENTITY->value);
        $result = $this->post($uri, [], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame($result['code'], ResultCode::UNPROCESSABLE_ENTITY->value);
        $result = $this->post($uri, $fillable, ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame($result['code'], ResultCode::FORBIDDEN->value);
        $this->assertFalse($this->hasPermissions($roleCode));
        $this->assertTrue($this->addPermissions($roleCode));
        $this->assertTrue($this->hasPermissions($roleCode));
        $result = $this->post($uri, $fillable, ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame($result['code'], ResultCode::SUCCESS->value);
        $this->deletePermissions($roleCode);
        $result = $this->post($uri, $fillable, ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame($result['code'], ResultCode::FORBIDDEN->value);
        try {
            $entity = $model::query()->where($fillable)->first();
        } catch (\Exception $e) {
            $entity = $model::query()->where(Arr::only($fillable, $required))->first();
        }
        if (empty($entity)) {
            $entity = $model::query()->where(Arr::only($fillable, $required))->first();
        }
        if (empty($entity)) {
            $this->fail('Create failed');
        }
        foreach (array_keys($fillable) as $key) {
            if (\is_string($entity->{$key})) {
                $this->assertSame(rtrim((string) $entity->{$key}), $fillable[$key]);
            } elseif (\is_object($entity->{$key})) {
                $v = $entity->{$key};
                if ($v instanceof Model) {
                    foreach ($v->getFillable() as $vKey) {
                        $this->assertSame($v->{$vKey}, $fillable[$key][$vKey]);
                    }
                }
            } else {
                $this->assertSame($entity->{$key}, $fillable[$key]);
            }
        }
        $entity->forceDelete();
    }

    public function caseSave(string $uri, Model $entity, string $roleCode, array $fillable, array $required = []): void
    {
        $token = $this->token;
        $result = $this->put($uri . $entity->getKey());
        $this->assertSame($result['code'], ResultCode::UNPROCESSABLE_ENTITY->value);
        $result = $this->put($uri . $entity->id, [], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame($result['code'], ResultCode::UNPROCESSABLE_ENTITY->value);
        $result = $this->put($uri . $entity->getKey(), $fillable, ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame($result['code'], ResultCode::FORBIDDEN->value);
        $this->assertFalse($this->hasPermissions($roleCode));
        $this->assertTrue($this->addPermissions($roleCode));
        $this->assertTrue($this->hasPermissions($roleCode));
        $result = $this->put($uri . $entity->getKey(), $fillable, ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame($result['code'], ResultCode::SUCCESS->value);
        $this->deletePermissions($roleCode);
        $result = $this->put($uri . $entity->getKey(), $fillable, ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame($result['code'], ResultCode::FORBIDDEN->value);
        $entity->refresh();
        foreach (array_keys($fillable) as $key) {
            if (\is_string($entity->{$key})) {
                $this->assertSame(rtrim((string) $entity->{$key}), $fillable[$key]);
            } elseif (\is_object($entity->{$key})) {
                $v = $entity->{$key};
                if ($v instanceof Model) {
                    foreach ($v->getFillable() as $vKey) {
                        $this->assertSame($v->{$vKey}, $fillable[$key][$vKey]);
                    }
                }
            } else {
                $this->assertSame($entity->{$key}, $fillable[$key]);
            }
        }
        $entity->forceDelete();
    }

    public function caseDelete(string $uri, Model $entity, string $roleCode, bool $isForceDelete = false): void
    {
        $requestPath = $uri;
        $token = $this->token;
        $result = $this->delete($requestPath);
        $this->assertSame($result['code'], ResultCode::UNAUTHORIZED->value);
        $result = $this->delete($requestPath, [], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame($result['code'], ResultCode::FORBIDDEN->value);
        $this->assertFalse($this->hasPermissions($roleCode));
        $this->assertTrue($this->addPermissions($roleCode));
        $this->assertTrue($this->hasPermissions($roleCode));
        $result = $this->delete($requestPath, [
            $entity->getKey(),
        ], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame($result['code'], ResultCode::SUCCESS->value);
        $this->deletePermissions($roleCode);
        $result = $this->delete($requestPath, [
            $entity->getKey(),
        ], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame($result['code'], ResultCode::FORBIDDEN->value);
        if (! $isForceDelete) {
            $entity->refresh();
            $this->assertTrue($entity->trashed());
            $entity->forceDelete();
        } else {
            $this->expectException(ModelNotFoundException::class);
            $entity->refresh();
        }
    }
}
