<script setup lang="ts">
import type { MaProTableExpose, MaProTableOptions, MaProTableSchema } from '@mineadmin/pro-table'
import type { Ref } from 'vue'
import getSearchItems from './userOperationLogData/UserOperationLogSearch.tsx'
import getColumns from './userOperationLogData/UserOperationLogColumn.tsx'
import type { RequestLogInfoVo } from '~/base/api/log.ts'
import { UserOperatorLog } from '~/base/api/log.ts'

defineOptions({ name: 'log:userOperation' })

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
    mainTitle: () => t('baseOperationLog.title'),
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
