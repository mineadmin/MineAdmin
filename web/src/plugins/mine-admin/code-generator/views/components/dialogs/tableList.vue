<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<script setup lang="ts">
import { getTableList } from '$/mine-admin/code-generator/api'
import type { MaProTableExpose } from '@mineadmin/pro-table'

const emit = defineEmits(['selection'])

const dbRef = ref<MaProTableExpose>()
</script>

<template>
  <ma-pro-table
    ref="dbRef"
    :schema="{
      tableColumns: [
        { label: '选择', prop: 'checked', width: '80px' },
        { label: '数据表', prop: 'name' },
        { label: '表描述', prop: 'comment' },
        { label: '表引擎', prop: 'engine' },
        { label: '字符集', prop: 'collation' },
      ],
      searchItems: [
        { label: '数据表', prop: 'name', render: 'input', renderProps: { placeholder: '按名称搜索数据表' } },
      ],
    }"
    :options="{
      header: {
        show: false,
      },
      tableOptions: {
        highlightCurrentRow: true,
        adaption: false,
        on: {
          onCurrentChange: (newRow, oldRow) => {
            emit('selection', newRow)
            newRow.checked = true
            if (oldRow) {
              oldRow.checked = false
            }
          },
        },
      },
      requestOptions: {
        api: getTableList,
      },
    }"
  >
    <template #toolbarLeft>
      <el-tag>单击行即可选择，注意：没有表描述的不会显示在待选择列表</el-tag>
    </template>
    <template #column-checked="{ row }">
      <el-radio-group v-model="row.checked">
        <el-radio :value="true" />
      </el-radio-group>
    </template>
  </ma-pro-table>
</template>

<style scoped lang="scss">

</style>
