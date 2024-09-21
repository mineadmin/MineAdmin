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
import type { MaTableExpose } from '@mineadmin/table'
import useTable from '@/hooks/useTable.ts'

const { model = {} } = defineProps<{ model: Record<string, any> }>()

useTable('buttonFormTable').then((table: MaTableExpose) => {
  table.setData(model?.btnPermission ?? [{}])
  table.setColumns([
    {
      label: '#',
      cellRender: ({ $index }): any => (
        <>
          <el-button size="small">+</el-button>
          <el-button size="small">-</el-button>
        </>
      ),
    },
    {
      label: '按钮名称',
      cellRender: ({ row }): any => (
        <el-input v-model={row.title} />
      ),
    },
    {
      label: '按钮权限',
      cellRender: ({ row }): any => (
        <el-input v-model={row.code} />
      ),
    },
    {
      label: '按钮国际化',
      cellRender: ({ row }): any => (
        <el-input v-model={row.i18n} />
      ),
    },
  ])
})
</script>

<template>
  <el-card class="w-full">
    <ma-table ref="buttonFormTable">
      <template #empty>
        还没有按钮菜单
      </template>
    </ma-table>
  </el-card>
</template>

<style scoped lang="scss">

</style>
