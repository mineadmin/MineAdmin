<?php

namespace App\Models\Permission;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meta newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meta newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meta query()
 *
 * @mixin \Eloquent
 */
#[Fillable(['title', 'i18n', 'badge', 'icon', 'affix', 'hidden', 'type', 'cache', 'copyright', 'useDefaultLayout', 'breadcrumbEnable', 'componentPath', 'componentSuffix', 'link', 'activeName', 'auth', 'role', 'user'])]
final class Meta extends Model
{
    public $incrementing = false;

    protected function casts(): array
    {
        return [
            'affix' => 'boolean',
            'hidden' => 'boolean',
            'cache' => 'boolean',
            'copyright' => 'boolean',
            'useDefaultLayout' => 'boolean',
            'breadcrumbEnable' => 'boolean',
            'title' => 'string',
            'componentPath' => 'string',
            'componentSuffix' => 'string',
            'i18n' => 'string',
            'badge' => 'string',
            'icon' => 'string',
            'type' => 'string',
            'link' => 'string',
            'activeName' => 'string',
            'auth' => 'array',
            'role' => 'array',
            'user' => 'array',
        ];
    }
}
