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

use Hyperf\Validation\Request\FormRequest;

class PostRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:60',
            'code' => 'required|string|max:60',
            'sort' => 'required|integer',
            'status' => 'required|integer',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => trans('post.name'),
            'code' => trans('post.code'),
            'sort' => trans('post.sort'),
            'status' => trans('post.status'),
        ];
    }
}
