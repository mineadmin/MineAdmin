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
 */
#[Controller(prefix: "system")]
class UploadController extends MineController
{
    #[Inject]
    protected SystemUploadFileService $service;

    /**
     * 上传文件
     * @param UploadFileRequest $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \League\Flysystem\FileExistsException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping("uploadFile"), Auth]
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
     * @param UploadImageRequest $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \League\Flysystem\FileExistsException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping("uploadImage"), Auth]
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
     * @param NetworkImageRequest $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Exception
     */
    #[PostMapping("saveNetworkImage"), Auth]
    public function saveNetworkImage(NetworkImageRequest $request): \Psr\Http\Message\ResponseInterface
    {
        return $this->success($this->service->saveNetworkImage($request->validated()));
    }

    /**
     * 获取当前目录所有文件和目录
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("getAllFiles"), Auth]
    public function getAllFile(): \Psr\Http\Message\ResponseInterface
    {
        return $this->success(
            $this->service->getAllFile($this->request->all())
        );
    }

    /**
     * 获取文件信息
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("getFileInfo")]
    public function getFileInfo(): \Psr\Http\Message\ResponseInterface
    {
        return $this->success($this->service->read($this->request->input('id', null)));
    }

    /**
     * 下载文件
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("download")]
    public function download(): \Psr\Http\Message\ResponseInterface
    {
        $id = $this->request->input('id');
        if (empty($id)) {
            return $this->error("附件ID必填");
        }
        $model = $this->service->read($id);
        return $this->_download(BASE_PATH . '/public' . $model->url, $model->origin_name);
    }
}