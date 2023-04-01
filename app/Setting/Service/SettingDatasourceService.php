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

namespace App\Setting\Service;

use App\Setting\Mapper\SettingDatasourceMapper;
use Mine\Abstracts\AbstractService;

/**
 * 数据源管理服务类
 */
class SettingDatasourceService extends AbstractService
{
    /**
     * @var SettingDatasourceMapper
     */
    public $mapper;

    public function __construct(SettingDatasourceMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}