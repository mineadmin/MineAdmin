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
import type { DepartmentVo } from '~/base/api/department.ts'
import type { MaTableColumns, MaTableOptions } from '@mineadmin/table'
import type { TransType } from '@/hooks/auto-imports/useTrans.ts'

const { data = null } = defineProps<{
  data?: DepartmentVo | null
}>()

const dictStore = useDictStore()
const i18n = useTrans() as TransType
const t = i18n.globalTrans

// 参数配置
const options = ref<MaTableOptions>({
  data: data?.department_users ?? [],
  adaption: false,
  height: 400,
  rowKey: 'id',
})

// 架构配置
const tableColumns = ref<MaTableColumns[]>([
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
])
</script>

<template>
  <ma-table :options="options" :columns="tableColumns" />
</template>

<style scoped lang="scss">
:deep(.mineadmin-pro-table-search) {
  margin: 0; padding: 0;
  @apply pt-3;
}
:deep(.mine-card) {
  margin: 0;
}
</style>
