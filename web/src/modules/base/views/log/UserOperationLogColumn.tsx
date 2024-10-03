import type { Ref } from 'vue'
import type { MaProTableColumns, MaProTableExpose } from '@mineadmin/pro-table'
import { UserOperatorLog } from '~/base/api/log.ts'

export default function getColumns(tableRef: Ref<MaProTableExpose>, formRef: Ref<any>, t: any): MaProTableColumns[] {
  return [
    // 多选列
    { type: 'selection', showOverflowTooltip: false, label: () => t('crud.selection') },
    // 索引序号列
    { label: () => t('id'), prop: 'id' },
    // 普通列
    { label: () => t('username'), prop: 'username' },
    { label: () => t('method'), prop: 'method' },
    { label: () => t('router'), prop: 'router' },
    { label: () => t('service_name'), prop: 'service_name' },
    { label: () => t('ip'), prop: 'ip' },
    { label: () => t('created_at'), prop: 'created_at' },
    { label: () => t('remark'), prop: 'remark' },
    // 操作列
    {
      type: 'operation',
      label: () => t('crud.operation'),
      operationConfigure: {
        actions: [
          {
            name: 'del',
            icon: 'mdi:delete',
            linkProps: { underline: false },
            text: '删除',
            onClick: ({ row }) => {
              UserOperatorLog.delete([row.id])
              tableRef.value.refresh()
            },
          },
        ],
      },
    },
  ]
}
