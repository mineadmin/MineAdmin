import type { MaProTableColumns, MaProTableExpose } from '@mineadmin/pro-table'
import { UserOperatorLog } from '~/base/api/log.ts'
import { ResultCode } from '@/utils/ResultCode.ts'
import { useMessage } from '@/hooks/useMessage.ts'

export default function getColumns(t: any): MaProTableColumns[] {
  const msg = useMessage()
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
        type: 'tile',
        actions: [
          {
            name: 'del',
            icon: 'mdi:delete',
            text: () => t('crud.delete'),
            onClick: ({ row }, proxy: MaProTableExpose) => {
              msg.delConfirm(t('crud.delDataMessage')).then(async () => {
                const response = await UserOperatorLog.delete([row.id])
                if (response.code === ResultCode.SUCCESS) {
                  msg.success(t('crud.delSuccess'))
                  proxy.refresh()
                }
              })
            },
          },
        ],
      },
    },
  ]
}
