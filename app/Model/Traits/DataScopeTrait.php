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

namespace App\Model\Traits;

trait DataScopeTrait
{
    public $dataScope = false;

    protected function boot(): void
    {
        parent::boot();

        static::addGlobalScope(make(\App\Model\Scopes\DataScopeScope::class));
    }

    public function getDataScope()
    {
        return $this->dataScope;
    }
    public function setDataScope($dataScope)
    {
        $this->dataScope = $dataScope;
        return $this;
    }

}