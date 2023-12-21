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

namespace App\System\Service;

use App\System\Mapper\SystemPostMapper;
use Mine\Abstracts\AbstractService;

class SystemPostService extends AbstractService
{
    /**
     * @var SystemPostMapper
     */
    public $mapper;

    public function __construct(SystemPostMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
