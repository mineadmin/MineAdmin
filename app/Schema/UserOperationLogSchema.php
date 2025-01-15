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

namespace App\Schema;

use App\Model\UserOperationLog;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Schema;

#[Schema(title: 'UserOperationLogSchema')]
class UserOperationLogSchema implements \JsonSerializable
{
    #[Property(property: 'id', title: '', type: 'int')]
    public ?int $id;

    #[Property(property: 'username', title: '用户名', type: 'string')]
    public ?string $username;

    #[Property(property: 'method', title: '请求方式', type: 'string')]
    public ?string $method;

    #[Property(property: 'router', title: '请求路由', type: 'string')]
    public ?string $router;

    #[Property(property: 'service_name', title: '业务名称', type: 'string')]
    public ?string $serviceName;

    #[Property(property: 'ip', title: '请求IP地址', type: 'string')]
    public ?string $ip;

    #[Property(property: 'ip_location', title: 'IP所属地', type: 'string')]
    public ?string $ipLocation;

    #[Property(property: 'request_data', title: '请求数据', type: 'mixed')]
    public mixed $requestData;

    #[Property(property: 'response_code', title: '响应状态码', type: 'string')]
    public ?string $responseCode;

    #[Property(property: 'response_data', title: '响应数据', type: 'mixed')]
    public mixed $responseData;

    #[Property(property: 'created_by', title: '创建者', type: 'int')]
    public ?int $createdBy;

    #[Property(property: 'updated_by', title: '更新者', type: 'int')]
    public ?int $updatedBy;

    #[Property(property: 'created_at', title: '创建时间', type: 'mixed')]
    public mixed $createdAt;

    #[Property(property: 'updated_at', title: '更新时间', type: 'mixed')]
    public mixed $updatedAt;

    #[Property(property: 'remark', title: '备注', type: 'string')]
    public ?string $remark;

    public function __construct(UserOperationLog $model)
    {
        $this->id = $model->id;
        $this->username = $model->username;
        $this->method = $model->method;
        $this->router = $model->router;
        $this->serviceName = $model->service_name;
        $this->ip = $model->ip;
        $this->ipLocation = $model->ip_location;
        $this->requestData = $model->request_data;
        $this->responseCode = $model->response_code;
        $this->responseData = $model->response_data;
        $this->createdBy = $model->created_by;
        $this->updatedBy = $model->updated_by;
        $this->createdAt = $model->created_at;
        $this->updatedAt = $model->updated_at;
        $this->remark = $model->remark;
    }

    public function jsonSerialize(): mixed
    {
        return ['id' => $this->id, 'username' => $this->username, 'method' => $this->method, 'router' => $this->router, 'service_name' => $this->serviceName, 'ip' => $this->ip, 'ip_location' => $this->ipLocation, 'request_data' => $this->requestData, 'response_code' => $this->responseCode, 'response_data' => $this->responseData, 'created_by' => $this->createdBy, 'updated_by' => $this->updatedBy, 'created_at' => $this->createdAt, 'updated_at' => $this->updatedAt, 'remark' => $this->remark];
    }
}
