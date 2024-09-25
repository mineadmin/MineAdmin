<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<script setup lang="tsx">
import type { MaProTableExpose, MaProTableOptions, MaProTableSchema } from '@mineadmin/pro-table'
import type { Ref } from 'vue'
import { page } from '~/base/api/user'
import getSearchItems from './data/getSearchItems.tsx'
import getUserColumns from './data/getUserColumns.tsx'
import UserForm from './form.vue'

defineOptions({ name: 'permission:user' })

const proTableRef = ref<MaProTableExpose>() as Ref<MaProTableExpose>
const formRef = ref()
const selections = ref<any[]>([])
const t = useTrans()

// 参数配置
const options = ref<MaProTableOptions>({
  // 表格距离底部的像素偏移适配
  adaptionOffsetBottom: 161,
  // 头部配置
  header: {
    mainTitle: '用户管理',
    secondaryTitle: '提供用户添加、编辑、删除功能，超管不可修改。',
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
    show: false,
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
    api: page,
  },
})
// 架构配置
const schema = ref<MaProTableSchema>({
  // 搜索项
  searchItems: getSearchItems(t),
  // 表格列
  tableColumns: getUserColumns(proTableRef, formRef, t),
})
</script>

<template>
  <div class="mine-layout pt-3">
    <MaProTable ref="proTableRef" :options="options" :schema="schema">
      <template #actions>
        <el-button type="primary" @click="() => formRef.open(null)">
          {{ t('crud.add') }}
        </el-button>
      </template>

      <template #toolbarLeft>
        <el-button type="danger" plain :disabled="selections.length < 1">
          {{ t('crud.delete') }}
        </el-button>
      </template>
    </MaProTable>

    <UserForm ref="formRef" />
  </div>
</template>

<style scoped lang="scss">

</style>
