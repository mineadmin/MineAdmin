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

namespace App\Service\DataCenter;

use App\Exception\BusinessException;
use App\Http\Common\ResultCode;
use App\Kernel\Upload\UploadManager;
use App\Repository\DataCenter\AttachmentRepository;
use App\Repository\Setting\ConfigRepository;
use App\Service\IService;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @extends IService<AttachmentRepository>
 */
final class AttachmentService extends IService
{
    public function __construct(
        protected readonly AttachmentRepository $repository,
        protected readonly ConfigRepository $configRepository,
        protected readonly UploadManager $uploadManager
    ) {}

    public function upload(SplFileInfo $fileInfo, int $userId): array
    {
        $openUpload = (int) $this->configRepository->findFiledByKey('upload.open', 0);
        if (! $openUpload) {
            throw new BusinessException(code: ResultCode::UNPROCESSABLE_ENTITY, message: trans('attachment.upload_not_open'));
        }
        $fileHash = md5_file($fileInfo->getRealPath());
        if ($attachment = $this->repository->findByHash($fileHash)) {
            return $attachment->toArray();
        }
        return $this->repository->create(
            [
                ...$this->uploadManager->upload(
                    $fileInfo,
                    $this->configRepository->findFiledByKey('storage_mode', 'local')
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
