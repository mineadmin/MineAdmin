<?php

namespace App\Service;

use App\Exception\BusinessException;
use App\Http\Common\ResultCode;
use App\Models\Attachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttachmentService
{
    /**
     * @param  array<string, mixed>  $params
     * @return array{list: array<int, array<string, mixed>>, total: int}
     */
    public function page(array $params, int $page, int $pageSize): array
    {
        $suffixes = Arr::get($params, 'suffix');
        if (is_string($suffixes) && $suffixes !== '') {
            $suffixes = explode(',', $suffixes);
        }

        $paginator = Attachment::query()
            ->when(Arr::get($params, 'origin_name'), function ($query, string $originName): void {
                $query->where('origin_name', 'like', '%'.$originName.'%');
            })
            ->when(Arr::get($params, 'storage_mode'), function ($query, string $storageMode): void {
                $query->where('storage_mode', $storageMode);
            })
            ->when($suffixes, function ($query) use ($suffixes): void {
                $query->whereIn('suffix', Arr::wrap($suffixes));
            })
            ->when(Arr::get($params, 'current_user_id'), function ($query, mixed $currentUserId): void {
                $query->where('created_by', $currentUserId);
            })
            ->orderBy('id', 'desc')
            ->paginate(perPage: $pageSize, page: $page);

        return $this->formatPage($paginator);
    }

    public function upload(UploadedFile $file, int $userId): Attachment
    {
        $hash = md5_file($file->getRealPath());
        $existing = Attachment::query()->where('hash', $hash)->first();

        if ($existing instanceof Attachment) {
            return $existing;
        }

        $suffix = $file->getClientOriginalExtension() ?: $file->extension();
        $objectName = Str::uuid()->toString().($suffix === '' ? '' : '.'.$suffix);
        $storagePath = now()->format('Ymd');
        $path = $file->storeAs($storagePath, $objectName, 'public');
        $size = $file->getSize();

        return Attachment::query()->create([
            'created_by' => $userId,
            'updated_by' => $userId,
            'origin_name' => $file->getClientOriginalName(),
            'storage_mode' => 'local',
            'object_name' => $objectName,
            'mime_type' => $file->getMimeType(),
            'storage_path' => $storagePath,
            'hash' => $hash,
            'suffix' => $suffix,
            'size_byte' => $size,
            'size_info' => $this->sizeInfo($size),
            'url' => Storage::disk('public')->url($path),
        ]);
    }

    public function deleteById(int $id): void
    {
        $attachment = Attachment::query()->find($id);

        if ($attachment === null) {
            throw new BusinessException(ResultCode::NotFound, 'Not Found');
        }

        $attachment->delete();
    }

    private function sizeInfo(int $size): string
    {
        if ($size >= 1048576) {
            return round($size / 1048576, 2).' MB';
        }

        if ($size >= 1024) {
            return round($size / 1024, 2).' KB';
        }

        return $size.' B';
    }

    /**
     * @return array{list: array<int, array<string, mixed>>, total: int}
     */
    private function formatPage(LengthAwarePaginator $paginator): array
    {
        return [
            'list' => $paginator->items(),
            'total' => $paginator->total(),
        ];
    }
}
