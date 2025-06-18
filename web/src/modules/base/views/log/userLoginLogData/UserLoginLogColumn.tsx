/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { MaProTableColumns, MaProTableExpose } from '@mineadmin/pro-table'
import { ElTag } from 'element-plus'
import { UserLoginLog } from '~/base/api/log.ts'
import { ResultCode } from '@/utils/ResultCode.ts'
import { useMessage } from '@/hooks/useMessage.ts'

export default function getColumns(t: any): MaProTableColumns[] {
  const dictStore = useDictStore()
  const msg = useMessage()

  return [
    // 多选列
    { type: 'selection', showOverflowTooltip: false, label: () => t('crud.selection') },
    // 索引序号列
    { type: 'index' },
    // 普通列
    { label: () => t('baseLoginLog.username'), prop: 'username' },
    // { label: () => t('baseLoginLog.os'), prop: 'os' },
    { label: () => t('baseLoginLog.ip'), prop: 'ip' },
    { label: () => t('baseLoginLog.browser'), prop: 'browser' },
    { label: () => t('baseLoginLog.status'), prop: 'status',
      cellRender: ({ row }) => (
        <ElTag type={dictStore.t('system-state', row.status, 'color')}>
          {t(dictStore.t('system-state', row.status, 'i18n'))}
        </ElTag>
      ),
    },
    { label: () => t('baseLoginLog.login_time'), prop: 'login_time' },
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
            linkProps: { underline: 'never' },
            text: () => t('crud.delete'),
            onClick: ({ row }, proxy: MaProTableExpose) => {
              msg.delConfirm(t('crud.delDataMessage')).then(async () => {
                const response = await UserLoginLog.delete([row.id])
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
  ]
}
