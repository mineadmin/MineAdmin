<?php

declare(strict_types=1);
namespace App\System\Controller;

use App\System\Request\Upload\CreateUploadDirRequest;
use App\System\Request\Upload\NetworkImageRequest;
use App\System\Request\Upload\UploadFileRequest;
use App\System\Request\Upload\UploadImageRequest;
use App\System\Service\SystemUploadFileService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\MineController;

/**
 * Class UploadController
 * @package App\System\Controller
 * @Controller(prefix="system")
 */
class UploadController extends MineController
{
    /**
     * @Inject
     * @var SystemUploadFileService
     */
    protected $service;

    /**
     * 上传文件
     * @PostMapping("uploadFile")
     * @param UploadFileRequest $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \League\Flysystem\FileExistsException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @Auth
     */
    public function uploadFile(UploadFileRequest $request): \Psr\Http\Message\ResponseInterface
    {
        if ($request->validated() && $request->file('file')->isValid()) {
            $data = $this->service->upload(
                $request->file('file'), ['path' => $request->input('path', null)]
            );
            return empty($data) ? $this->error() : $this->success($data);
        } else {
            return $this->error(t('system.upload_file_verification_fail'));
        }
    }

    /**
     * 上传图片
     * @PostMapping("uploadImage")
     * @param UploadImageRequest $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \League\Flysystem\FileExistsException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @Auth
     */
    public function uploadImage(UploadImageRequest $request): \Psr\Http\Message\ResponseInterface
    {
        if ($request->validated() && $request->file('image')->isValid()) {
            $data = $this->service->upload(
                $request->file('image'), ['path' => $request->input('path', null)]
            );
            return empty($data) ? $this->error() : $this->success($data);
        } else {
            return $this->error(t('system.upload_image_verification_fail'));
        }
    }

    /**
     * 保存网络图片
     * @PostMapping("saveNetworkImage")
     * @Auth
     * @throws \Exception
     */
    public function saveNetworkImage(NetworkImageRequest $request): \Psr\Http\Message\ResponseInterface
    {
        return $this->success($this->service->saveNetworkImage($request->validated()));
    }

    /**
     * 获取可上传的目录
     * @GetMapping("getDirectory")
     * @Auth
     */
    public function getDirectory(): \Psr\Http\Message\ResponseInterface
    {
        return $this->success(
            $this->service->getDirectory(
                $this->request->input('path', ''),
                (bool) $this->request->input('isChildren', false)
            )
        );
    }

    /**
     * 获取当前目录所有文件和目录
     * @GetMapping("getAllFiles")
     * @Auth
     */
    public function getAllFile(): \Psr\Http\Message\ResponseInterface
    {
        return $this->success(
            $this->service->getAllFile($this->request->all())
        );
    }

    /**
     * 创建上传目录
     * @PostMapping("createUploadDir")
     * @Auth
     */
    public function createUploadDir(CreateUploadDirRequest $request): \Psr\Http\Message\ResponseInterface
    {
        return $this->service->createUploadDir($request->all()) ? $this->success() : $this->error();
    }

    /**
     * 删除上传目录
     * @PostMapping("deleteUploadDir")
     * @Auth
     */
    public function deleteUploadDir(CreateUploadDirRequest $request): \Psr\Http\Message\ResponseInterface
    {
        return $this->service->deleteUploadDir($request->all()) ? $this->success() : $this->error();
    }

    /**
     * 获取文件信息
     * @GetMapping("getFileInfo")
     */
    public function getFileInfo(): \Psr\Http\Message\ResponseInterface
    {
        return $this->success($this->service->read($this->request->input('id', null)));
    }
}