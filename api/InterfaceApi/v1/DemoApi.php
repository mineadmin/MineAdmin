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
use Api\Request\v1\DemoApiRequest;
use Api\Request\v1\UserInfoRequest;

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
    public function getDeptList(): ResponseInterface
    {
        // 第二个参数，不进行数据权限检查，否则会拉起检测是否登录。
        return $this->response->success('请求成功', $this->dept->getTreeList([], false));
    }

    /**
     * 获取部门列表接口
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getUserInfo(UserInfoRequest $userInfoRequest): ResponseInterface
    {

        // 标准formRequest
        $data = $userInfoRequest->validated();
        $info = $this->user->get(function($query) use ($data) {
            $query->where('id', $data['id']);
        });

        // DemoApiRequest $request
//        // mineFormRequest
//        $data = $request->validated();
//        $info = $this->user->get($data['id']);
        return $this->response->success('请求成功', [
            'info' => $info
        ]);
    }
}