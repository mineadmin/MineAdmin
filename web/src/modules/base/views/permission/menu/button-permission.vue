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

function addItme() {
  model!.btnPermission.push({ title: '', code: '', i18n: '' })
}

useTable('buttonFormTable').then((table: MaTableExpose) => {
  table.setData(model?.btnPermission)
  table.setColumns([
    {
      label: '#',
      showOverflowTooltip: false,
      width: '80px',
      headerRender: () => (
        <el-button
          size="small"
          circle
          type="primary"
          onClick={addItme}
        >
          <ma-svg-icon name="ic:round-plus" size={20} />
        </el-button>
      ),
      cellRender: ({ $index }): any => (
        <el-button
          size="small"
          circle
          type="danger"
          onClick={() => {
            if (model!.btnPermission?.length > 0) {
              model!.btnPermission.splice($index, 1)
            }
          }}
        >
          <ma-svg-icon name="ic:round-minus" size={20} />
        </el-button>
      ),
    },
    {
      label: '按钮名称',
      cellRender: ({ row }): any => (
        <el-input v-model={row.title} placeholder="请输入按钮名称" />
      ),
    },
    {
      label: '按钮标识',
      cellRender: ({ row }): any => (
        <el-input v-model={row.code} placeholder="请输入按钮标识" />
      ),
    },
    {
      label: '按钮国际化',
      cellRender: ({ row }): any => (
        <el-input v-model={row.i18n} placeholder="请输入按钮国际化" />
      ),
    },
  ])
})
</script>

<template>
  <el-card class="w-full" shadow="never">
    {{ model }}
    <ma-table ref="buttonFormTable">
      <template #empty>
        <div>
          没有按钮菜单？
          <el-button type="primary" plain @click="addItme">
            新增一个
          </el-button>
        </div>
      </template>
    </ma-table>
  </el-card>
</template>

<style scoped lang="scss">

</style>
