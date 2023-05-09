<?php

namespace Mine\Annotation\Api;

use App\System\Model\SystemApi;
use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

#[Attribute(Attribute::TARGET_METHOD)]
class MApi extends AbstractAnnotation
{
    public function __construct(
        // 访问名
        public string $accessName,
        // 接口名
        public string $name,
        // 描述信息
        public string $description,
        // appId
        public string $appId,
        // 验证模式 1 简单  2 复杂;
        public int $authMode = SystemApi::AUTH_MODE_EASY,
        // 请求方式 A, P, G, U, D
        public string $requestMode = SystemApi::METHOD_ALL,
        // 所属分组 可以不用
        public int $groupId = 0,
        // 备注
        public string $remark = '',
        // 响应示例
        public string $response = "{\n  code: 200,\n  success: true,\n  message: '请求成功',\n  data: []\n}"
    ) {
    }
}