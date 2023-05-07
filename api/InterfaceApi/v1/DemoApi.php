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
use Mine\MineResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Mine\Annotation\Api\MApi;
use Mine\Annotation\Api\MApiRequestParam;
use Mine\Annotation\Api\MApiResponseParam;

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

    protected MineResponse $response;

    /**
     * DemoApi constructor.
     * @param SystemUserMapper $user
     * @param SystemDeptMapper $dept
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(SystemUserMapper $user, SystemDeptMapper $dept)
    {
        $this->response = container()->get(MineResponse::class);
        $this->user = $user;
        $this->dept = $dept;
    }

    /**
     * 获取用户列表接口
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[MApi(accessName: "getUserList", name: "获取用户列表接口", description: "获取用户列表接口", appId: "a7ccdef6d7")]

    # 请求入参 以下注解暂时仅仅用于生成文档 仅仅作为示例，可以没有
    #[MApiRequestParam(name: 'p1', description: "请求参数1", dataType: 'Integer', defaultValue: '1')]
    #[MApiRequestParam(name: 'p2', description: "请求参数2", defaultValue: '2')]

    # 响应出参 以下注解暂时仅仅用于生成文档
    #[MApiResponseParam(name: 'data.items', description: "用户信息", dataType: 'Array')]
    #[MApiResponseParam(name: 'data.pageInfo', description: "分页信息", dataType: 'Array')]
    public function getUserList(): ResponseInterface
    {
        // 第二个参数，不进行数据权限检查，否则会拉起检测是否登录。
        return $this->response->success('请求成功', $this->user->getPageList([], false));
    }

    /**
     * 获取部门列表接口
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[MApi(accessName: "getDeptList", name: "获取部门列表接口", description: "获取部门列表接口", appId: "a7ccdef6d7")]
     # 响应出参 以下注解暂时仅仅用于生成文档
    #[MApiResponseParam(name: 'data', description: "部门信息", dataType: 'Array')]
    public function getDeptList(): ResponseInterface
    {
        // 第二个参数，不进行数据权限检查，否则会拉起检测是否登录。
        return $this->response->success('请求成功', $this->dept->getTreeList([], false));
    }
}