<?php
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

namespace Api\InterfaceApi\v1;

use App\System\Mapper\SystemDeptMapper;
use App\System\Mapper\SystemUserMapper;

/**
 * 演示，测试专用
 */
class DemoApi
{
    /**
     * @var SystemUserMapper
     */
    protected SystemUserMapper $user;

    /**
     * @var SystemDeptMapper
     */
    protected SystemDeptMapper $dept;

    /**
     * DemoApi constructor.
     * @param SystemUserMapper $user
     * @param SystemDeptMapper $dept
     */
    public function __construct(SystemUserMapper $user, SystemDeptMapper $dept)
    {
        $this->user = $user;
        $this->dept = $dept;
    }

    /**
     * 获取用户列表接口
     * @return array
     */
    public function getUserList(): array
    {
        // 第二个参数，不进行数据权限检查，否则会拉起检测是否登录。
        return $this->user->getPageList([], false);
    }

    /**
     * 获取部门列表接口
     * @return array
     */
    public function getDeptList(): array
    {
        // 第二个参数，不进行数据权限检查，否则会拉起检测是否登录。
        return $this->dept->getTreeList([], false);
    }
}