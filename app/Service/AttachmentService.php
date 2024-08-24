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

namespace App\Service;

use App\Kernel\Upload\UploadManager;
use App\Repository\AttachmentRepository;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @extends IService<AttachmentRepository>
 */
final class AttachmentService extends IService
{
    public function __construct(
        protected readonly AttachmentRepository $repository,
        protected readonly UploadManager $uploadManager
    ) {}

    public function upload(SplFileInfo $fileInfo, int $userId): array
    {
        $fileHash = md5_file($fileInfo->getRealPath());
        if ($attachment = $this->repository->findByHash($fileHash)) {
            return $attachment->toArray();
        }
        return $this->repository->create(
            [
                ...$this->uploadManager->upload(
                    $fileInfo,
                ),
                ...[
                    'created_by' => $userId,
                ]]
        )->toArray();
    }

    public function getRepository(): AttachmentRepository
    {
        return $this->repository;
    }
}
