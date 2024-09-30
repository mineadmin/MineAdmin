<i18n lang="yaml">
en:
  title: "User Operation log"
  id: "ID"
  username: "Username"
  method: "Request Method"
  router: "Request Router"
  service_name: "Service Name"
  ip: "Request IP Address"
  created_at: "Creation Time"
  updated_at: "Update Time"
  deleted_at: "Deletion Time"
  remark: "Remark"
zh_CN:
  title: "用户操作日志"
  id: "ID"
  username: "用户名"
  method: "请求方式"
  router: "请求路由"
  service_name: "业务名称"
  ip: "请求IP地址"
  created_at: "创建时间"
  updated_at: "更新时间"
  deleted_at: "删除时间"
  remark: "备注"
zh_TW:
  title: "用戶操作日誌"
  id: "ID"
  username: "使用者名稱"
  method: "請求方式"
  router: "請求路由"
  service_name: "業務名稱"
  ip: "請求IP地址"
  created_at: "建立時間"
  updated_at: "更新時間"
  deleted_at: "刪除時間"
  remark: "備註"
</i18n>

<script setup lang="ts">
import type { MaProTableExpose, MaProTableOptions, MaProTableSchema } from '@mineadmin/pro-table'
import type { Ref } from 'vue'
import getSearchItems from '~/base/views/log/UserOperationLogSearch.tsx'
import getColumns from '~/base/views/log/UserOperationLogColumn.tsx'
import type { RequestLogInfoVo } from '~/base/api/log.ts'
import { UserOperatorLog } from '~/base/api/log.ts'

const t = useTrans()
const proTableRef = ref<MaProTableExpose>() as Ref<MaProTableExpose>
const formRef = ref()
const selections: Ref<RequestLogInfoVo[]> = ref([])

async function clickDelete() {
  const ids = selections.value.map((value: RequestLogInfoVo) => {
    return value.id
  })
  const res = await UserOperatorLog.delete(ids)
  if (res.code === 200) {
    proTableRef.value.refresh()
  }
}

const options = ref<MaProTableOptions>({
  // 表格距离底部的像素偏移适配
  adaptionOffsetBottom: 161,
  header: {
    mainTitle: t('title'),
  },
  // 表格参数
  tableOptions: {
    on: {
      // 表格选择事件
      onSelectionChange: (selection: any[]) => selections.value = selection,
    },
  },
  // 搜索参数
  searchOptions: {
    fold: true,
    defaultValue: { status: 1 },
    text: {
      searchBtn: () => t('crud.search'),
      resetBtn: () => t('crud.reset'),
      isFoldBtn: () => t('crud.searchFold'),
      notFoldBtn: () => t('crud.searchUnFold'),
    },
  },
  // 搜索表单参数
  searchFormOptions: { labelWidth: '90px' },
  // 请求配置
  requestOptions: {
    api: UserOperatorLog.page,
  },
})
// 架构配置
const schema = ref<MaProTableSchema>({
  // 搜索项
  searchItems: getSearchItems(t),
  // 表格列
  tableColumns: getColumns(proTableRef, formRef, t),
})
</script>

<template>
  <div class="mine-layout pt-3">
    <MaProTable ref="proTableRef" :options="options" :schema="schema">
      <template #toolbarLeft>
        <el-button type="danger" plain :disabled="selections.length < 1" @click="clickDelete">
          {{ t('crud.delete') }}
        </el-button>
      </template>
    </MaProTable>
  </div>
</template>

<style scoped lang="scss">

</style>
