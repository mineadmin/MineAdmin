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

namespace App\Repository;

use App\Model\Attachment;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;

/**
 * @extends IRepository<Attachment>
 */
final class AttachmentRepository extends IRepository
{
    public function __construct(
        protected readonly Attachment $model
    ) {}

    public function findByHash(string $hash): ?Attachment
    {
        return $this->model->newQuery()->where('hash', $hash)->first();
    }

    public function handleSearch(Builder $query, array $params): Builder
    {
        return $query
            ->when(Arr::get($params, 'suffix'), static function (Builder $query, $suffix) {
                $query->whereIn('suffix', Arr::wrap($suffix));
            })
            ->when(Arr::get($params, 'mime_type'), static function (Builder $query, $mime_type) {
                $query->whereIn('mime_type', Arr::wrap($mime_type));
            })
            ->when(Arr::get($params, 'storage_mode'), static function (Builder $query, $storage_mode) {
                $query->whereIn('storage_mode', Arr::wrap($storage_mode));
            })
            ->when(Arr::get($params, 'created_by'), static function (Builder $query, $created_by) {
                $query->where('created_by', $created_by);
            })
            ->when(Arr::get($params, 'updated_by'), static function (Builder $query, $updated_by) {
                $query->where('updated_by', $updated_by);
            })
            ->when(Arr::get($params, 'created_at'), static function (Builder $query, $created_at) {
                $query->whereBetween('created_at', $created_at);
            })
            ->when(Arr::get($params, 'updated_at'), static function (Builder $query, $updated_at) {
                $query->whereBetween('updated_at', $updated_at);
            })
            ->when(Arr::get($params, 'url'), static function (Builder $query, $url) {
                $query->where('url', $url);
            })
            ->when(Arr::get($params, 'hash'), static function (Builder $query, $hash) {
                $query->where('hash', $hash);
            })
            ->when(Arr::get($params, 'object_name'), static function (Builder $query, $object_name) {
                $query->where('object_name', $object_name);
            })
            ->when(Arr::get($params, 'origin_name'), static function (Builder $query, $origin_name) {
                $query->where('origin_name', $origin_name);
            })
            ->when(Arr::get($params, 'storage_path'), static function (Builder $query, $storage_path) {
                $query->where('storage_path', $storage_path);
            });
    }
}
