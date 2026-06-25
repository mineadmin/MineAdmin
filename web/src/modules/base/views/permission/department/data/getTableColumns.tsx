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
import type { UseDialogExpose } from '@/hooks/useDialog.ts'

import { useMessage } from '@/hooks/useMessage.ts'
import { deleteByIds } from '~/base/api/department.ts'
import { ResultCode } from '@/utils/ResultCode.ts'
import hasAuth from '@/utils/permission/hasAuth.ts'

export default function getTableColumns(dialog: UseDialogExpose, formRef: any, t: any): MaProTableColumns[] {
  const msg = useMessage()

  const showBtn = (auth: string | string[]) => {
    return hasAuth(auth)
  }

  return [
    // 多选列
    { type: 'selection', showOverflowTooltip: false, label: () => t('crud.selection') },
    // 普通列
    { label: () => t('baseDepartment.name'), prop: 'name', align: 'left' },
    { label: () => t('baseDepartment.leaderCount'), prop: 'leader', cellRender: ({ row }) => row.leader?.length ?? 0 },
    { label: () => t('baseDepartment.positionsCount'), prop: 'positions', cellRender: ({ row }) => row.positions?.length ?? 0 },
    { label: () => t('baseDepartment.usersCount'), prop: 'users', cellRender: ({ row }) => row.department_users?.length ?? 0 },
    { label: () => t('baseDepartment.created_at'), prop: 'created_at', width: 200 },
    { label: () => t('baseDepartment.updated_at'), prop: 'updated_at', width: 200 },

    // 操作列
    {
      type: 'operation',
      label: () => t('crud.operation'),
      width: '180px',
      operationConfigure: {
        actions: [
          {
            name: 'setLeader',
            show: () => showBtn('permission:leader:index'),
            icon: 'material-symbols:checklist-rounded',
            text: () => t('baseDepartment.page.setLeader'),
            onClick: ({ row }) => {
              dialog.setTitle(t('baseDepartment.page.setLeader'))
              dialog.setAttr({ width: '55%' })
              dialog.open({ formType: 'setLeader', data: row })
            },
          },
          {
            name: 'managePost',
            icon: 'material-symbols:position-bottom-right-outline-rounded',
            show: () => showBtn('permission:position:index'),
            text: () => t('baseDepartment.page.managePost'),
            onClick: ({ row }) => {
              dialog.setTitle(t('baseDepartment.page.managePost'))
              dialog.setAttr({ width: '55%' })
              dialog.open({ formType: 'position', data: row })
            },
          },
          {
            name: 'viewUser',
            icon: 'uil:users-alt',
            show: () => showBtn('permission:department:update'),
            text: () => t('baseDepartment.page.previewUser'),
            onClick: ({ row }) => {
              dialog.setTitle(t('baseDepartment.page.previewUser'))
              dialog.setAttr({ width: '55%' })
              dialog.open({ formType: 'viewUser', data: row })
            },
          },
          {
            name: 'edit',
            icon: 'material-symbols:person-edit',
            show: () => showBtn('permission:department:update'),
            text: () => t('crud.edit'),
            onClick: ({ row }) => {
              dialog.setTitle(t('crud.edit'))
              dialog.setAttr({ width: '550px' })
              dialog.open({ formType: 'edit', data: row })
            },
          },
          {
            name: 'del',
            show: () => showBtn('permission:department:delete'),
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
  ]
}
