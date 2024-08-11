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

use App\Kernel\Swagger\Attributes\FormRequest as FormRequestAnnotation;
use App\Schema\DictTypeSchema;
use Hyperf\Validation\Request\FormRequest;

#[FormRequestAnnotation(
    schema: DictTypeSchema::class,
    only: [
        'name',
        'code',
        'status',
        'remark',
    ]
)]
class DictTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50',
            'code' => 'required|string|max:100',
            'status' => 'required|integer',
            'remark' => 'required|string|max:255',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => trans('dict_type.name'),
            'code' => trans('dict_type.code'),
            'status' => trans('dept.status'),
            'remark' => trans('dept.remark'),
        ];
    }
}
