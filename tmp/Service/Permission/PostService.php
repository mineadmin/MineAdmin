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

namespace App\Service\Permission;

use App\Repository\Permission\PostRepository;
use Mine\Abstracts\AbstractService;

class PostService extends AbstractService
{
    /**
     * @var PostRepository
     */
    public $repository;

    public function __construct(PostRepository $repository)
    {
        $this->repository = $repository;
    }
}
