import type { Ref } from 'vue'
import type { MaProTableColumns, MaProTableExpose } from '@mineadmin/pro-table'
import { ElTag } from 'element-plus'
import { UserLoginLog } from '~/base/api/log.ts'

const dictStore = useDictStore()

export default function getColumns(tableRef: Ref<MaProTableExpose>, formRef: Ref<any>, t: any): MaProTableColumns[] {
  return [
    // 多选列
    { type: 'selection', showOverflowTooltip: false, label: () => t('crud.selection') },
    // 索引序号列
    { type: 'index' },
    // 普通列
    { label: () => t('username'), prop: 'username' },
    { label: () => t('os'), prop: 'os' },
    { label: () => t('ip'), prop: 'ip' },
    { label: () => t('browser'), prop: 'browser' },
    { label: () => t('status'), prop: 'status',
      cellRender: ({ row }) => (
        <ElTag type={dictStore.t('system-status', row.status, 'color')}>
          {t(dictStore.t('system-status', row.status, 'i18n'))}
        </ElTag>
      ),
    },
    { label: () => t('remark'), prop: 'remark' },
    { label: () => t('login_time'), prop: 'login_time' },
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
              UserLoginLog.delete([row.id])
              tableRef.value.refresh()
            },
          },
        ],
      },
    },
  ]
}
