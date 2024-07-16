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

namespace App\Service\System;

use App\Mapper\System\PostMapper;
use Mine\Abstracts\AbstractService;

class PostService extends AbstractService
{
    /**
     * @var PostMapper
     */
    public $mapper;

    public function __construct(PostMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
