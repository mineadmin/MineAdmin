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

namespace App\Kernel\Casbin\Rule;

use Hyperf\DbConnection\Model\Model;

class Rule extends Model
{
    /**
     * Fillable.
     */
    protected array $fillable = ['ptype', 'v0', 'v1', 'v2', 'v3', 'v4', 'v5'];

    /**
     * Create a new Eloquent model instance.
     */
    public function __construct(array $attributes = [])
    {
        $this->setTable(config('permission.database.rules_table'));
        $this->setConnection(config('permission.database.connection'));

        parent::__construct($attributes);
    }
}
