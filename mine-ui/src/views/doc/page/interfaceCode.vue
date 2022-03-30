<template>
  <el-table :data="tableData" stripe style="width: 100%" size="large">
    <el-table-column type="index" width="100" label="#" />
    <el-table-column prop="code" label="代码标识"  />
    <el-table-column prop="label" label="英文标识" />
    <el-table-column prop="remark" label="中文说明" />
  </el-table>
</template>

<script>
export default {
  data () {
    return {
      tableData: [
        {
          code: 2000,
          label: 'METHOD_NOT_ALLOW',
          remark: '使用了不允许的访问方法',
        },
        {
          code: 2100,
          label: 'NOT_FOUND',
          remark: '资源不存在',
        },
        {
          code: 2150,
          label: 'INTERFACE_EXCEPTION',
          remark: '接口异常',
        },
        {
          code: 2200,
          label: 'RESOURCE_STOP',
          remark: '资源被停用',
        },
        {
          code: 2300,
          label: 'APP_BAN',
          remark: 'APP被停用',
        },
        {
          code: 10000,
          label: 'API_AUTH_EXCEPTION',
          remark: '接口鉴权异常',
        },
        {
          code: 10010,
          label: 'API_AUTH_FAIL',
          remark: '接口鉴权失败',
        },
        {
          code: 10011,
          label: 'API_APP_ID_MISSING',
          remark: '缺少APP ID',
        },
        {
          code: 10102,
          label: 'API_APP_SECRET_MISSING',
          remark: '缺少APP SECRET',
        },
        {
          code: 10103,
          label: 'API_ACCESS_TOKEN_MISSING',
          remark: '缺少 ACCESS TOKEN',
        },
        {
          code: 10104,
          label: 'API_PARAMS_ERROR',
          remark: 'API参数错误',
        },
        {
          code: 10105,
          label: 'API_SIGN_MISSING',
          remark: '缺少签名',
        },
        {
          code: 10106,
          label: 'API_SIGN_ERROR',
          remark: '签名错误',
        },
                {
          code: 10107,
          label: 'API_IDENTITY_MISSING',
          remark: '缺少身份认证信息',
        },
        {
          code: 10108,
          label: 'API_IDENTITY_ERROR',
          remark: '身份认证信息错误',
        },
        {
          code: 10160,
          label: 'API_VERIFY_PASS',
          remark: 'API验证通过',
        }
      ]
    }
  }
}
</script>