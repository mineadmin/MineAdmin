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

namespace App\Http\Admin\Controller;

use App\Exception\BusinessException;
use App\Http\Admin\CurrentUser;
use App\Http\Admin\Request\Passport\LoginRequest;
use App\Http\Admin\Vo\PassportLoginVo;
use App\Http\Common\Controller\AbstractController;
use App\Http\Common\Middleware\AuthMiddleware;
use App\Http\Common\Result;
use App\Kernel\Auth\Support\RequestScopedTokenTrait;
use App\Kernel\Swagger\Attributes\ResultResponse;
use App\Schema\UserSchema;
use App\Service\Permission\UserService;
use Hyperf\Codec\Json;
use Hyperf\Collection\Arr;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Swagger\Annotation as OA;
use Hyperf\Swagger\Annotation\Post;

use function App\Http\Admin\Support\user;

/**
 * Class LoginController.
 */
#[OA\HyperfServer(name: 'http')]
class PassportController extends AbstractController
{
    use RequestScopedTokenTrait;

    public function __construct(
        private readonly UserService $userService
    ) {}

    /**
     * 登录.
     */
    #[Post(
        path: '/admin/passport/login',
        operationId: 'passportLogin',
        summary: '系统登录',
        tags: ['admin:passport']
    )]
    #[ResultResponse(
        instance: new Result(data: new PassportLoginVo()),
        title: '登录成功',
        description: '登录成功返回对象',
        example: '{"code":200,"message":"成功","data":{"token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MjIwOTQwNTYsIm5iZiI6MTcyMjA5NDAiwiZXhwIjoxNzIyMDk0MzU2fQ.7EKiNHb_ZeLJ1NArDpmK6sdlP7NsDecsTKLSZn_3D7k","expire_at":300}}'
    )]
    #[OA\RequestBody(content: new OA\JsonContent(
        ref: LoginRequest::class,
        title: '登录请求参数',
        required: ['username', 'password'],
        example: '{"username":"admin","password":"123456"}'
    ))]
    public function login(LoginRequest $request): Result
    {
        $username = (string) $request->input('username');
        $password = (string) $request->input('password');
        return $this->success(
            $this->userService->login(
                $username,
                $password
            )
        );
    }

    /**
     * 退出.
     */
    #[Post(
        path: '/admin/passport/logout',
        operationId: 'passportLogout',
        summary: '退出',
        security: [['bearerAuth' => []]],
        tags: ['admin:passport']
    )]
    #[ResultResponse(instance: new Result(), example: '{"code":200,"message":"成功","data":[]}')]
    #[Middleware(AuthMiddleware::class)]
    public function logout(RequestInterface $request)
    {
        $this->userService->logout($this->getToken());
        return $this->success();
    }

    /**
     * 用户信息.
     */
    #[OA\Get(
        path: '/admin/passport/getInfo',
        operationId: 'getInfo',
        summary: '获取用户信息',
        security: [['bearerAuth' => []]],
        tags: ['admin:passport']
    )]
    #[Middleware(AuthMiddleware::class)]
    #[ResultResponse(
        instance: new Result(data: UserSchema::class),
    )]
    public function getInfo(): Result
    {
        return $this->success(Arr::except(user()?->toArray(), ['password']));
    }

    /**
     * 刷新token.
     */
    #[Post(
        path: '/admin/passport/refresh',
        operationId: 'refresh',
        summary: '刷新token',
        security: [['bearerAuth' => []]],
        tags: ['admin:passport']
    )]
    #[Middleware(AuthMiddleware::class)]
    #[ResultResponse(
        instance: new Result(data: new PassportLoginVo())
    )]
    public function refresh(CurrentUser $user): Result
    {
        return $this->success($user->refresh());
    }

    #[OA\Get(
        path: '/admin/passport/getBingBackgroundImage',
        operationId: 'getBingBackgroundImage',
        description: '获取每日的必应背景图',
    )]
    #[OA\Response(
        response: 200,
        description: '成功',
        content: new OA\JsonContent(example: '{
  "images": [
    {
      "startdate": "20240726",
      "fullstartdate": "202407261600",
      "enddate": "20240727",
      "url": "/th?id=OHR.RhinelandVineyards_ZH-CN3332101688_1920x1080.jpg&rf=LaDigue_1920x1080.jpg&pid=hp",
      "urlbase": "/th?id=OHR.RhinelandVineyards_ZH-CN3332101688",
      "copyright": "摩泽尔河谷的葡萄园，莱茵兰-法尔茨，德国 (© Jorg Greuel/Getty Images)",
      "copyrightlink": "https://www.bing.com/search?q=%E6%B3%95%E5%B0%94%E8%8C%A8%E8%91%A1%E8%90%84%E9%85%92%E4%BA%A7%E5%8C%BA&form=hpcapt&mkt=zh-cn",
      "title": "完美的葡萄酒",
      "quiz": "/search?q=Bing+homepage+quiz&filters=WQOskey:%22HPQuiz_20240726_RhinelandVineyards%22&FORM=HPQUIZ",
      "wp": true,
      "hsh": "4d0805d3edb368d9cebf56b7376cd938",
      "drk": 1,
      "top": 1,
      "bot": 1,
      "hs": [
        
      ]
    }
  ],
  "tooltips": {
    "loading": "正在加载...",
    "previous": "上一个图像",
    "next": "下一个图像",
    "walle": "此图片不能下载用作壁纸。",
    "walls": "下载今日美图。仅限用作桌面壁纸。"
  }
}')
    )]
    public function getBingBackgroundImage(): Result
    {
        try {
            $response = file_get_contents('https://cn.bing.com/HPImageArchive.aspx?format=js&idx=0&n=1');
            $content = Json::decode($response);
            if ($url = Arr::get($content, 'images.0.url')) {
                return $this->success([
                    'url' => 'https://cn.bing.com' . $url,
                ]);
            }
            throw new BusinessException();
        } catch (\Exception $e) {
            return $this->error('获取必应背景失败');
        }
    }
}
