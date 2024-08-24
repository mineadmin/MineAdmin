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

use App\Http\Admin\CurrentUser;
use App\Http\Common\Controller\AbstractController;
use App\Http\Common\Middleware\AuthMiddleware;
use App\Http\Common\Result;
use App\Kernel\Swagger\Attributes\PageResponse;
use App\Schema\MenuSchema;
use App\Schema\RoleSchema;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HyperfServer;

#[HyperfServer(name: 'http')]
#[Middleware(AuthMiddleware::class)]
final class PermissionController extends AbstractController
{
    public function __construct(
        private readonly CurrentUser $user
    ) {}

    #[Get(
        path: '/admin/permission/menus',
        operationId: 'PermissionMenus',
        summary: '获取当前用户菜单',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['权限']
    )]
    #[PageResponse(
        instance: MenuSchema::class,
        example: '{"code":200,"message":"成功","data":[{"id":290,"parent_id":0,"name":"LAme6dFrlf","code":"eNiYagCtJp","icon":"test","route":"test","component":"test","redirect":"test","is_hidden":1,"type":"M","status":1,"sort":1,"created_by":1,"updated_by":1,"created_at":"2024-08-02 00:32:26","updated_at":"2024-08-02 00:32:26","deleted_at":null,"remark":"test","children":[{"id":291,"parent_id":290,"name":"zFFsqwN3rB","code":"isz4eTJANV","icon":"test","route":"test","component":"test","redirect":"test","is_hidden":1,"type":"M","status":1,"sort":1,"created_by":1,"updated_by":1,"created_at":"2024-08-02 00:32:26","updated_at":"2024-08-02 00:32:26","deleted_at":null,"remark":"test","children":[]}]},{"id":291,"parent_id":290,"name":"zFFsqwN3rB","code":"isz4eTJANV","icon":"test","route":"test","component":"test","redirect":"test","is_hidden":1,"type":"M","status":1,"sort":1,"created_by":1,"updated_by":1,"created_at":"2024-08-02 00:32:26","updated_at":"2024-08-02 00:32:26","deleted_at":null,"remark":"test","children":[]},{"id":292,"parent_id":0,"name":"mMMSlHc8cv","code":"xzobstyEmP","icon":"test","route":"test","component":"test","redirect":"test","is_hidden":1,"type":"M","status":1,"sort":1,"created_by":1,"updated_by":1,"created_at":"2024-08-02 00:32:26","updated_at":"2024-08-02 00:32:26","deleted_at":null,"remark":"test","children":[{"id":293,"parent_id":292,"name":"8Sr5vtPSqw","code":"9SelwHGooE","icon":"test","route":"test","component":"test","redirect":"test","is_hidden":1,"type":"M","status":1,"sort":1,"created_by":1,"updated_by":1,"created_at":"2024-08-02 00:32:26","updated_at":"2024-08-02 00:32:26","deleted_at":null,"remark":"test","children":[]}]},{"id":293,"parent_id":292,"name":"8Sr5vtPSqw","code":"9SelwHGooE","icon":"test","route":"test","component":"test","redirect":"test","is_hidden":1,"type":"M","status":1,"sort":1,"created_by":1,"updated_by":1,"created_at":"2024-08-02 00:32:26","updated_at":"2024-08-02 00:32:26","deleted_at":null,"remark":"test","children":[]},{"id":294,"parent_id":0,"name":"ot8fL3u7QZ","code":"kCbrLhgYDj","icon":"test","route":"test","component":"test","redirect":"test","is_hidden":1,"type":"M","status":1,"sort":1,"created_by":1,"updated_by":1,"created_at":"2024-08-02 00:32:26","updated_at":"2024-08-02 00:32:26","deleted_at":null,"remark":"test","children":[{"id":295,"parent_id":294,"name":"6uQFNiMzJa","code":"GVvC2iPU92","icon":"test","route":"test","component":"test","redirect":"test","is_hidden":1,"type":"M","status":1,"sort":1,"created_by":1,"updated_by":1,"created_at":"2024-08-02 00:32:26","updated_at":"2024-08-02 00:32:26","deleted_at":null,"remark":"test","children":[]}]},{"id":295,"parent_id":294,"name":"6uQFNiMzJa","code":"GVvC2iPU92","icon":"test","route":"test","component":"test","redirect":"test","is_hidden":1,"type":"M","status":1,"sort":1,"created_by":1,"updated_by":1,"created_at":"2024-08-02 00:32:26","updated_at":"2024-08-02 00:32:26","deleted_at":null,"remark":"test","children":[]}]}'
    )]
    public function menus(): Result
    {
        return $this->success(
            data: $this->user->menus()
        );
    }

    #[Get(
        path: '/admin/permission/roles',
        operationId: 'PermissionRoles',
        summary: '获取当前用户角色',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['权限']
    )]
    #[PageResponse(
        instance: RoleSchema::class,
        example: '{"code":200,"message":"成功","data":[{"id":290,"parent_id":0,"name":"LAme6dFrlf","code":"eNiYagCtJp","icon":"test","route":"test","component":"test","redirect":"test","is_hidden":1,"type":"M","status":1,"sort":1,"created_by":1,"updated_by":1,"created_at":"2024-08-02 00:32:26","updated_at":"2024-08-02 00:32:26","deleted_at":null,"remark":"test","children":[{"id":291,"parent_id":290,"name":"zFFsqwN3rB","code":"isz4eTJANV","icon":"test","route":"test","component":"test","redirect":"test","is_hidden":1,"type":"M","status":1,"sort":1,"created_by":1,"updated_by":1,"created_at":"2024-08-02 00:32:26","updated_at":"2024-08-02 00:32:26","deleted_at":null,"remark":"test","children":[]}]},{"id":291,"parent_id":290,"name":"zFFsqwN3rB","code":"isz4eTJANV","icon":"test","route":"test","component":"test","redirect":"test","is_hidden":1,"type":"M","status":1,"sort":1,"created_by":1,"updated_by":1,"created_at":"2024-08-02 00:32:26","updated_at":"2024-08-02 00:32:26","deleted_at":null,"remark":"test","children":[]},{"id":292,"parent_id":0,"name":"mMMSlHc8cv","code":"xzobstyEmP","icon":"test","route":"test","component":"test","redirect":"test","is_hidden":1,"type":"M","status":1,"sort":1,"created_by":1,"updated_by":1,"created_at":"2024-08-02 00:32:26","updated_at":"2024-08-02 00:32:26","deleted_at":null,"remark":"test","children":[{"id":293,"parent_id":292,"name":"8Sr5vtPSqw","code":"9SelwHGooE","icon":"test","route":"test","component":"test","redirect":"test","is_hidden":1,"type":"M","status":1,"sort":1,"created_by":1,"updated_by":1,"created_at":"2024-08-02 00:32:26","updated_at":"2024-08-02 00:32:26","deleted_at":null,"remark":"test","children":[]}]},{"id":293,"parent_id":292,"name":"8Sr5vtPSqw","code":"9SelwHGooE","icon":"test","route":"test","component":"test","redirect":"test","is_hidden":1,"type":"M","status":1,"sort":1,"created_by":1,"updated_by":1,"created_at":"2024-08-02 00:32:26","updated_at":"2024-08-02 00:32:26","deleted_at":null,"remark":"test","children":[]},{"id":294,"parent_id":0,"name":"ot8fL3u7QZ","code":"kCbrLhgYDj","icon":"test","route":"test","component":"test","redirect":"test","is_hidden":1,"type":"M","status":1,"sort":1,"created_by":1,"updated_by":1,"created_at":"2024-08-02 00:32:26","updated_at":"2024-08-02 00:32:26","deleted_at":null,"remark":"test","children":[{"id":295,"parent_id":294,"name":"6uQFNiMzJa","code":"GVvC2iPU92","icon":"test","route":"test","component":"test","redirect":"test","is_hidden":1,"type":"M","status":1,"sort":1,"created_by":1,"updated_by":1,"created_at":"2024-08-02 00:32:26","updated_at":"2024-08-02 00:32:26","deleted_at":null,"remark":"test","children":[]}]},{"id":295,"parent_id":294,"name":"6uQFNiMzJa","code":"GVvC2iPU92","icon":"test","route":"test","component":"test","redirect":"test","is_hidden":1,"type":"M","status":1,"sort":1,"created_by":1,"updated_by":1,"created_at":"2024-08-02 00:32:26","updated_at":"2024-08-02 00:32:26","deleted_at":null,"remark":"test","children":[]}]}'
    )]
    public function roles(): Result
    {
        return $this->success(
            data: $this->user->roles()
        );
    }
}
