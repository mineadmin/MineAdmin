<?php

declare(strict_types=1);
namespace App\System\Controller;

use App\System\Request\UploadRequest;
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
     * @param UploadRequest $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \League\Flysystem\FileExistsException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping("uploadFile"), Auth]
    public function uploadFile(UploadRequest $request): \Psr\Http\Message\ResponseInterface
    {
        if ($request->validated() && $request->file('file')->isValid()) {
            $data = $this->service->upload(
                $request->file('file'), $request->all()
            );
            return empty($data) ? $this->error() : $this->success($data);
        } else {
            return $this->error(t('system.upload_file_verification_fail'));
        }
    }

    /**
     * 上传图片
     * @param UploadRequest $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \League\Flysystem\FileExistsException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping("uploadImage"), Auth]
    public function uploadImage(UploadRequest $request): \Psr\Http\Message\ResponseInterface
    {
        if ($request->validated() && $request->file('image')->isValid()) {
            $data = $this->service->upload(
                $request->file('image'), $request->all()
            );
            return empty($data) ? $this->error() : $this->success($data);
        } else {
            return $this->error(t('system.upload_image_verification_fail'));
        }
    }

    /**
     * 分块上传
     * @param UploadRequest $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping("chunkUpload"), Auth]
    public function chunkUpload(UploadRequest $request): \Psr\Http\Message\ResponseInterface
    {
        return ($data = $this->service->chunkUpload($request->validated())) ? $this->success($data) : $this->error();
    }

    /**
     * 保存网络图片
     * @param UploadRequest $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Exception
     */
    #[PostMapping("saveNetworkImage"), Auth]
    public function saveNetworkImage(UploadRequest $request): \Psr\Http\Message\ResponseInterface
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
     * 通过ID获取文件信息
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("getFileInfoById")]
    public function getFileInfoByid(): \Psr\Http\Message\ResponseInterface
    {
        return $this->success($this->service->read((int) $this->request->input('id', null)) ?? []);
    }

    /**
     * 通过HASH获取文件信息
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("getFileInfoByHash")]
    public function getFileInfoByHash(): \Psr\Http\Message\ResponseInterface
    {
        return $this->success($this->service->readByHash($this->request->input('hash', null)) ?? []);
    }

    /**
     * 根据id下载文件
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("downloadById")]
    public function downloadById(): \Psr\Http\Message\ResponseInterface
    {
        $id = $this->request->input('id');
        if (empty($id)) {
            return $this->error("附件ID必填");
        }
        $model = $this->service->read((int) $id);
        if (! $model) {
            throw new \Mine\Exception\MineException('附件不存在', 500);
        }
        return $this->_download(BASE_PATH . '/public' . $model->url, $model->origin_name);
    }

    /**
     * 根据hash下载文件
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("downloadByHash")]
    public function downloadByHash(): \Psr\Http\Message\ResponseInterface
    {
        $hash = $this->request->input('hash');
        if (empty($hash)) {
            return $this->error("附件hash必填");
        }
        $model = $this->service->readByHash($hash);
        if (! $model) {
            throw new \Mine\Exception\MineException('附件不存在', 500);
        }
        return $this->_download(BASE_PATH . '/public' . $model->url, $model->origin_name);
    }

    /**
     * 输出图片、文件
     * @param string $hash
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \League\Flysystem\FilesystemException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("showFile/{hash}")]
    public function showFile(string $hash): \Psr\Http\Message\ResponseInterface
    {
        return $this->service->responseFile($hash);
    }
}