<?php

use App\Http\Admin\Controller\AttachmentController;
use App\Http\Admin\Controller\PassportController;
use App\Http\Admin\Controller\Permission\DepartmentController;
use App\Http\Admin\Controller\Permission\LeaderController;
use App\Http\Admin\Controller\Permission\MenuController;
use App\Http\Admin\Controller\Permission\PermissionController;
use App\Http\Admin\Controller\Permission\PositionController;
use App\Http\Admin\Controller\Permission\RoleController;
use App\Http\Admin\Controller\Permission\UserController;
use App\Http\Admin\Controller\Permission\UserLoginLogController;
use App\Http\Admin\Controller\Permission\UserOperationLogController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/passport')->group(function (): void {
    Route::post('login', [PassportController::class, 'login']);
    Route::post('refresh', [PassportController::class, 'refresh']);

    Route::middleware(['auth:api', 'access.token', 'operation.log'])->group(function (): void {
        Route::post('logout', [PassportController::class, 'logout']);
        Route::get('getInfo', [PassportController::class, 'getInfo']);
    });
});

Route::prefix('admin/permission')->middleware(['auth:api', 'access.token', 'operation.log'])->group(function (): void {
    Route::get('menus', [PermissionController::class, 'menus']);
    Route::get('roles', [PermissionController::class, 'roles']);
    Route::post('update', [PermissionController::class, 'update']);
});

Route::prefix('admin/user')->middleware(['auth:api', 'access.token', 'operation.log'])->group(function (): void {
    Route::get('list', [UserController::class, 'pageList'])->middleware('permission:permission:user:index');
    Route::put('/', [UserController::class, 'updateInfo'])->middleware('permission:permission:user:update');
    Route::put('password', [UserController::class, 'resetPassword'])->middleware('permission:permission:user:password');
    Route::post('/', [UserController::class, 'create'])->middleware('permission:permission:user:save');
    Route::delete('/', [UserController::class, 'delete'])->middleware('permission:permission:user:delete');
    Route::put('{userId}', [UserController::class, 'save'])->middleware('permission:permission:user:update');
    Route::get('{userId}/roles', [UserController::class, 'getUserRole'])->middleware('permission:permission:user:getRole');
    Route::put('{userId}/roles', [UserController::class, 'batchGrantRolesForUser'])->middleware('permission:permission:user:setRole');
});

Route::prefix('admin/role')->middleware(['auth:api', 'access.token', 'operation.log'])->group(function (): void {
    Route::get('list', [RoleController::class, 'pageList'])->middleware('permission:permission:role:index');
    Route::post('/', [RoleController::class, 'create'])->middleware('permission:permission:role:save');
    Route::put('{id}', [RoleController::class, 'save'])->middleware('permission:permission:role:update');
    Route::delete('/', [RoleController::class, 'delete'])->middleware('permission:permission:role:delete');
    Route::get('{id}/permissions', [RoleController::class, 'getRolePermissionForRole'])->middleware('permission:permission:role:getMenu');
    Route::put('{id}/permissions', [RoleController::class, 'batchGrantPermissionsForRole'])->middleware('permission:permission:role:setMenu');
});

Route::prefix('admin/menu')->middleware(['auth:api', 'access.token', 'operation.log'])->group(function (): void {
    Route::get('list', [MenuController::class, 'pageList'])->middleware('permission:permission:menu:index');
    Route::post('/', [MenuController::class, 'create'])->middleware('permission:permission:menu:create');
    Route::put('{id}', [MenuController::class, 'save'])->middleware('permission:permission:menu:save');
    Route::delete('/', [MenuController::class, 'delete'])->middleware('permission:permission:menu:delete');
});

Route::prefix('admin/department')->middleware(['auth:api', 'access.token', 'operation.log'])->group(function (): void {
    Route::get('list', [DepartmentController::class, 'pageList'])->middleware('permission:permission:department:index');
    Route::post('/', [DepartmentController::class, 'create'])->middleware('permission:permission:department:save');
    Route::put('{id}', [DepartmentController::class, 'save'])->middleware('permission:permission:department:update');
    Route::delete('/', [DepartmentController::class, 'delete'])->middleware('permission:permission:department:delete');
});

Route::prefix('admin/position')->middleware(['auth:api', 'access.token', 'operation.log'])->group(function (): void {
    Route::get('list', [PositionController::class, 'pageList'])->middleware('permission:permission:position:index');
    Route::put('{id}/data_permission', [PositionController::class, 'batchDataPermission'])->middleware('permission:permission:position:data_permission');
    Route::post('/', [PositionController::class, 'create'])->middleware('permission:permission:position:save');
    Route::put('{id}', [PositionController::class, 'save'])->middleware('permission:permission:position:update');
    Route::delete('/', [PositionController::class, 'delete'])->middleware('permission:permission:position:delete');
});

Route::prefix('admin/leader')->middleware(['auth:api', 'access.token', 'operation.log'])->group(function (): void {
    Route::get('list', [LeaderController::class, 'pageList'])->middleware('permission:permission:leader:index');
    Route::post('/', [LeaderController::class, 'create'])->middleware('permission:permission:leader:save');
    Route::delete('/', [LeaderController::class, 'delete'])->middleware('permission:permission:leader:delete');
});

Route::prefix('admin/attachment')->middleware(['auth:api', 'access.token', 'operation.log'])->group(function (): void {
    Route::get('list', [AttachmentController::class, 'list'])->middleware('permission:dataCenter:attachment:list');
    Route::post('upload', [AttachmentController::class, 'upload'])->middleware('permission:dataCenter:attachment:upload');
    Route::delete('{id}', [AttachmentController::class, 'delete'])->middleware('permission:dataCenter:attachment:delete');
});

Route::prefix('admin/user-login-log')->middleware(['auth:api', 'access.token', 'operation.log'])->group(function (): void {
    Route::get('list', [UserLoginLogController::class, 'page'])->middleware('permission:log:userLogin:list');
    Route::delete('/', [UserLoginLogController::class, 'delete'])->middleware('permission:log:userLogin:delete');
});

Route::prefix('admin/user-operation-log')->middleware(['auth:api', 'access.token', 'operation.log'])->group(function (): void {
    Route::get('list', [UserOperationLogController::class, 'page'])->middleware('permission:log:userOperation:list');
    Route::delete('/', [UserOperationLogController::class, 'delete'])->middleware('permission:log:userOperation:delete');
});
