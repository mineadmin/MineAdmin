<i18n lang="yaml">
en:
  title: User Login log
  id: "ID"
  username: "Username"
  ip: "Login IP Address"
  os: "Operating System"
  browser: "Browser"
  status: "Login Status"
  message: "Message"
  login_time: "Login Time"
  remark: "Remark"
zh_CN:
  title: 用户登录日志
  id: "ID"
  username: "用户名"
  ip: "登录IP地址"
  os: "操作系统"
  browser: "浏览器"
  status: "登录状态"
  message: "提示消息"
  login_time: "登录时间"
  remark: "备注"
zh_TW:
  id: "ID"
  username: "使用者名稱"
  ip: "登入IP位址"
  os: "作業系統"
  browser: "瀏覽器"
  status: "登入狀態（1為成功，2為失敗）"
  message: "提示訊息"
  login_time: "登入時間"
  remark: "備註"
</i18n>

<script setup lang="ts">
import type { UserLoginVo } from '~/base/api/log.ts'
import { UserLoginLog } from '~/base/api/log.ts'
import type { MaProTableExpose, MaProTableOptions, MaProTableSchema } from '@mineadmin/pro-table'
import type { Ref } from 'vue'
import getColumns from '~/base/views/log/UserLoginLogColumn.tsx'
import getSearchItems from '~/base/views/log/UserLoginLogSearch.tsx'

const t = useTrans()
const proTableRef = ref<MaProTableExpose>() as Ref<MaProTableExpose>
const formRef = ref()
const selections: Ref<UserLoginVo[]> = ref([])

async function clickDelete() {
  const ids = selections.value.map((value: UserLoginVo) => {
    return value.id
  })
  const res = await UserLoginLog.delete(ids)
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
    api: UserLoginLog.page,
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
