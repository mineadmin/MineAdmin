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

use App\System\Mapper\SystemBossMapper;
use Mine\Abstracts\AbstractService;

/**
 * 老板管理服务类
 */
class SystemBossService extends AbstractService
{
    /**
     * @var SystemBossMapper
     */
    public $mapper;

    public function __construct(SystemBossMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}