<?php

namespace App\Models\Casts;

use App\Models\Permission\Meta;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/** @implements CastsAttributes<Meta, Meta> */
class MetaCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): Meta
    {
        return new Meta(empty($value) ? [] : json_decode($value, true, flags: JSON_THROW_ON_ERROR));
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if ($value instanceof Meta) {
            $value = $value->attributesToArray();
        }

        return json_encode($value, JSON_THROW_ON_ERROR);
    }
}
