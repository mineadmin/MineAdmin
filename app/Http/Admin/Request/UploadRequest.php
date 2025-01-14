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

namespace App\Http\Admin\Request;

use App\Http\Common\Request\Traits\NoAuthorizeTrait;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Schema;
use Hyperf\Validation\Request\FormRequest;

#[Schema(
    title: '上传附件',
    properties: [
        new Property(property: 'file', description: '文件', type: 'file'),
    ]
)]
class UploadRequest extends FormRequest
{
    use NoAuthorizeTrait;

    public function rules(): array
    {
        return [
            'file' => 'required|file',
        ];
    }

    public function attributes(): array
    {
        return [
            'file' => trans('attachment.file'),
        ];
    }
}
