import type { Ref } from 'vue'
import type { MaProTableColumns, MaProTableExpose } from '@mineadmin/pro-table'
import { UserOperatorLog } from '~/base/api/log.ts'

export default function getColumns(tableRef: Ref<MaProTableExpose>, formRef: Ref<any>, t: any): MaProTableColumns[] {
  return [
    // 多选列
    { type: 'selection', showOverflowTooltip: false, label: () => t('crud.selection') },
    // 索引序号列
    { type: 'index' },
    // 普通列
    { label: () => t('baseOperationLog.username'), prop: 'username' },
    { label: () => t('baseOperationLog.method'), prop: 'method' },
    { label: () => t('baseOperationLog.router'), prop: 'router' },
    { label: () => t('baseOperationLog.service_name'), prop: 'service_name' },
    { label: () => t('baseOperationLog.ip'), prop: 'ip' },
    { label: () => t('baseOperationLog.created_at'), prop: 'created_at' },
    // 操作列
    {
      type: 'operation',
      label: () => t('crud.operation'),
      operationConfigure: {
        actions: [
          {
            name: 'del',
            icon: 'mdi:delete',
            text: '删除',
            onClick: async ({ row }) => {
              await UserOperatorLog.delete([row.id])
              tableRef.value.refresh()
            },
          },
        ],
      },
    },
  ]
}
