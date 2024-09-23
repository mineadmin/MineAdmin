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
import type { MaProTableOptions, MaProTableSchema } from '@mineadmin/pro-table'
import searchItems from './data/searchItems.tsx'
import tableColumns from './data/tableColumns.tsx'
import { page } from '~/base/api/user'

defineOptions({ name: 'permission:user' })

// 参数配置
const options = ref<MaProTableOptions>({
  // 表格距离底部的像素偏移适配
  adaptionOffsetBottom: 161,
  // 头部配置
  header: {
    mainTitle: '用户管理',
    secondaryTitle: '提供用户添加、编辑、删除功能，超管不可修改。',
  },
  // 搜索参数
  searchOptions: {
    fold: true,
    defaultValue: { status: 1 },
  },
  // 搜索表单参数
  searchFormOptions: { labelWidth: '70px' },
  requestOptions: {
    api: page,
  },
})

// 架构配置
const schema = ref<MaProTableSchema>({
  // 搜索项
  searchItems,
  // 表格列
  tableColumns,
})
</script>

<template>
  <div class="mine-layout pt-3">
    <ma-pro-table ref="proTableRef" :options="options" :schema="schema">
      <template #actions>
        <el-button type="primary">
          新增
        </el-button>
      </template>

      <template #toolbarLeft>
        <el-button type="danger" plain disabled>
          删除
        </el-button>
      </template>
    </ma-pro-table>
  </div>
</template>

<style scoped lang="scss">

</style>
