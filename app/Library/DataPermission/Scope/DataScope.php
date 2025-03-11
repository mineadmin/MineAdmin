<?php

namespace App\Library\DataPermission\Scope;

use App\Http\CurrentUser;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Hyperf\Database\Model\Scope;

final class DataScope implements Scope
{
    public function __construct(
        private readonly CurrentUser $currentUser
    ){}

    public function apply(Builder $builder, Model $model): void
    {
        if ($this->currentUser->user() === null){
            return;
        }
        $user = $this->currentUser->user();
        if ($user->isSuperAdmin()){
            return;
        }
    }
}