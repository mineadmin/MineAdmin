<?php

declare(strict_types=1);

namespace Plugin\MineAdmin\AppStore\Request;

use App\Http\Common\Request\Traits\NoAuthorizeTrait;
use Hyperf\Validation\Request\FormRequest;
use Plugin\MineAdmin\AppStore\Enum\TerminalAction;

class TerminalTaskRequest extends FormRequest
{
    use NoAuthorizeTrait;

    public function rules(): array
    {
        return [
            'action' => 'required|string|in:' . implode(',', TerminalAction::values()),
            'identifier' => 'sometimes|nullable|string|max:120',
            'version' => 'sometimes|nullable|string|max:80',
        ];
    }
}
