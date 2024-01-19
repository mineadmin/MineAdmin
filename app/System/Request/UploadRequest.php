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

namespace App\System\Request;

use App\Setting\Service\SettingConfigService;
use Mine\MineFormRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class UploadRequest extends MineFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [];
    }

    /**
     * 上传文件验证规则.
     * @return string[]
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \RedisException
     */
    public function uploadFileRules(): array
    {
        return [
            'file' => 'required|mimes:' . $this->getMimes('upload_allow_file'),
            'path' => 'max:30',
        ];
    }

    /**
     * 上传图片验证规则.
     * @return string[]
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \RedisException
     */
    public function uploadImageRules(): array
    {
        return [
            'image' => 'required|mimes:' . $this->getMimes('upload_allow_image'),
            'path' => 'max:30',
        ];
    }

    /**
     * 分块上传验证规则.
     * @return string[]
     */
    public function chunkUploadRules(): array
    {
        return [
            'package' => 'required',
            'total' => 'required',
            'index' => 'required',
            'hash' => 'required',
            'ext' => 'required',
            'type' => 'required',
            'name' => 'required',
            'size' => 'required',
        ];
    }

    /**
     * 分块上传验证规则.
     * @return string[]
     */
    public function saveNetworkImageRules(): array
    {
        return [
            'url' => 'required',
            'path' => 'max:30',
        ];
    }

    /**
     * 字段映射名称
     * return array.
     */
    public function attributes(): array
    {
        return [
            'url' => '网络图片地址',
            'path' => '保存目录',
            'image' => '上传图片',
            'file' => '上传文件',
            'package' => '文件数据包',
            'total' => '总分块数',
            'index' => '分块索引',
            'hash' => '文件hash',
            'ext' => '文件扩展名',
            'type' => '文件类型',
            'name' => '文件名称',
            'size' => '文件大小',
        ];
    }

    /**
     * 获取Mimes.
     * @param mixed $key
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \RedisException
     */
    protected function getMimes($key): string
    {
        return container()->get(SettingConfigService::class)->getConfigByKey($key)['value'] ?? '';
    }
}
