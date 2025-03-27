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
import { deleteByIds, page } from '~/base/api/position.ts'
import type { MaProTableExpose, MaProTableOptions, MaProTableSchema } from '@mineadmin/pro-table'
import type { TransType } from '@/hooks/auto-imports/useTrans.ts'
import { useMessage } from '@/hooks/useMessage.ts'
import hasAuth from '@/utils/permission/hasAuth.ts'

const { data = null } = defineProps<{
  data?: DepartmentVo | null
}>()

const i18n = useTrans() as TransType
const t = i18n.globalTrans
const proTableRef = ref<MaProTableExpose>()

const msg = useMessage()

function showBtn(auth: string | string[]) {
  return hasAuth(auth)
}

// 参数配置
const options = ref<MaProTableOptions>({
  header: { show: false },
  // 表格参数
  tableOptions: {
    adaption: false,
    height: 400,
    rowKey: 'id',
  },
  // 搜索参数
  searchOptions: {
    fold: true,
    text: {
      searchBtn: () => t('crud.search'),
      resetBtn: () => t('crud.reset'),
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
  searchItems: [{ label: '岗位名称', prop: 'name', render: 'input' }],
  // 表格列
  tableColumns: [
    // 多选列
    { type: 'selection', showOverflowTooltip: false, label: () => t('crud.selection') },
    {
      label: '岗位名称',
      prop: 'name',
      align: 'left',
    }, {
      label: '创建时间',
      prop: 'created_at',
      width: 180,
    },
    // 操作列
    {
      type: 'operation',
      align: 'right',
      label: () => t('crud.operation'),
      width: '180px',
      operationConfigure: {
        actions: [
          {
            name: 'edit',
            icon: 'material-symbols:person-edit',
            show: () => showBtn('permission:position:update'),
            text: () => t('crud.edit'),
            onClick: ({ row }) => {
            },
          },
          {
            name: 'del',
            show: () => showBtn('permission:position:delete'),
            icon: 'mdi:delete',
            text: () => t('crud.delete'),
            onClick: ({ row }, proxy: MaProTableExpose) => {
              msg.delConfirm(t('crud.delDataMessage')).then(async () => {
                const response = await deleteByIds([row.id])
                if (response.code === ResultCode.SUCCESS) {
                  msg.success(t('crud.delSuccess'))
                  await proxy.refresh()
                }
              })
            },
          },
        ],
      },
    },
  ],
})
</script>

<template>
  <ma-pro-table ref="proTableRef" :options="options" :schema="schema">
    <template #actions>
      <el-button
        v-auth="['permission:position:save']"
        type="primary"
        @click="() => {
          // maDialog.setTitle(t('crud.add'))
          // maDialog.open({ formType: 'add' })
        }"
      >
        {{ t('crud.add') }}
      </el-button>
    </template>
  </ma-pro-table>
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
