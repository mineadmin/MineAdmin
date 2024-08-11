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
use App\Schema\DictDataSchema;
use Hyperf\Validation\Request\FormRequest;

#[FormRequestAnnotation(
    schema: DictDataSchema::class,
    only: [
        'type_id',
        'label',
        'value',
        'code',
        'sort',
        'status',
        'remark',
    ]
)]
class DictDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type_id' => 'required|integer|exists:dict_type,id',
            'label' => 'nullable|string|max:50',
            'value' => 'nullable|string|max:100',
            'code' => 'nullable|string|max:100',
            'sort' => 'required|integer|min:0',
            'status' => 'required|integer|in:0,1',
            'remark' => 'required|string|max:255',
        ];
    }

    public function attributes(): array
    {
        return [
            'type_id' => trans('dict_data.type_id'),
            'label' => trans('dict_data.label'),
            'value' => trans('dict_data.value'),
            'code' => trans('dict_data.code'),
            'sort' => trans('dict_data.sort'),
            'status' => trans('dict_data.status'),
            'remark' => trans('dept.remark'),
        ];
    }
}
