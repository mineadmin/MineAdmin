<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link https://github.com/mineadmin
-->

<script setup lang="tsx">
import type { DepartmentUserVo, DepartmentVo } from '~/base/api/department.ts'
import type { MaTableColumns, MaTableExpose } from '@mineadmin/table'
import type { TransType } from '@/hooks/auto-imports/useTrans.ts'
import useTable from '@/hooks/useTable.ts'

const { data = null } = defineProps<{
  data?: DepartmentVo | null
}>()

const i18n = useTrans() as TransType
const t = i18n.globalTrans
const tableRef = ref<MaTableExpose>()

const tableData = computed<DepartmentUserVo[]>(() => {
  return Array.isArray(data?.department_users) ? data.department_users : []
})

const tableColumns: MaTableColumns[] = [
  {
    label: () => t('baseUserManage.username'),
    prop: 'username',
    align: 'left',
  },
  {
    label: () => t('baseUserManage.nickname'),
    prop: 'nickname',
  },
  {
    label: () => t('baseUserManage.phone'),
    prop: 'phone',
  },
  {
    label: () => t('baseUserManage.email'),
    prop: 'email',
  },
]

function setTableData(rows: DepartmentUserVo[]) {
  tableRef.value?.setData(rows)
}

watch(tableData, (rows) => {
  setTableData(rows)
}, { immediate: true })

useTable('tableRef')
  .then((table: MaTableExpose) => {
    tableRef.value = table
    table.setOptions({
      adaption: false,
      height: 400,
      rowKey: 'id',
    })
    table.setColumns(tableColumns)
    setTableData(tableData.value)
  })
  .catch((err) => {
    console.error('init user table failed:', err)
  })
</script>

<template>
  <ma-table ref="tableRef" />
</template>

<style scoped lang="scss">
:deep(.mineadmin-pro-table-search) {
  margin: 0;
  padding: 0;
  @apply pt-3;
}

:deep(.mine-card) {
  margin: 0;
}
</style>
