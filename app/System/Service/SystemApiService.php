<?php
declare(strict_types=1);
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

namespace App\System\Service;

use App\System\Mapper\SystemApiMapper;
use Mine\Abstracts\AbstractService;

/**
 * 接口表服务类
 */
class SystemApiService extends AbstractService
{
    /**
     * @var SystemApiMapper
     */
    public $mapper;

    public function __construct(SystemApiMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}