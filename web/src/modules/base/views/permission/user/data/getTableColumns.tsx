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
import type { UserVo } from '~/base/api/user.ts'
import type { UseDialogExpose } from '@/hooks/useDialog.ts'

import defaultAvatar from '@/assets/images/defaultAvatar.jpg'
import { ElTag } from 'element-plus'
import { useMessage } from '@/hooks/useMessage.ts'
import { deleteByIds, resetPassword } from '~/base/api/user.ts'
import { ResultCode } from '@/utils/ResultCode.ts'
import hasAuth from '@/utils/permission/hasAuth.ts'

export default function getTableColumns(dialog: UseDialogExpose, formRef: any, t: any): MaProTableColumns[] {
  const dictStore = useDictStore()
  const msg = useMessage()

  const showBtn = (auth: string | string[], row: UserVo) => {
    return hasAuth(auth) && row.id !== 1 && row.username !== 'SuperAdmin'
  }

  return [
    // 多选列
    { type: 'selection', showOverflowTooltip: false, label: () => t('crud.selection'),
      cellRender: ({ row }): any => (row.id === 1 || row.username === 'SuperAdmin') ? '-' : undefined,
      selectable: (row: UserVo) => ![1].includes(row.id as number) || !['SuperAdmin'].includes(row.username as string),
    },
    // 索引序号列
    { type: 'index' },
    // 普通列
    { label: () => t('baseUser.avatar'), prop: 'avatar', width: '120px',
      cellRender: ({ row }) => (
        <div class="flex-center">
          <el-avatar src={(row.avatar === '' || !row.avatar) ? defaultAvatar : row.avatar} alt={row.username} />
        </div>
      ),
    },
    { label: () => t('baseUser.username'), prop: 'username' },
    { label: () => t('baseUser.nickname'), prop: 'nickname' },
    { label: () => t('baseUser.userType'), prop: 'user_type',
      cellRender: ({ row }) => (
        <ElTag type={dictStore.t('base-userType', row.user_type, 'color')}>
          {t(dictStore.t('base-userType', row.user_type, 'i18n'))}
        </ElTag>
      ),
    },
    { label: () => t('baseUser.phone'), prop: 'phone' },
    { label: () => t('baseUser.email'), prop: 'email' },
    { label: () => t('baseUser.status'), prop: 'status',
      cellRender: ({ row }) => (
        <ElTag type={dictStore.t('system-status', row.status, 'color')}>
          {t(dictStore.t('system-status', row.status, 'i18n'))}
        </ElTag>
      ),
    },
    // 操作列
    {
      type: 'operation',
      label: () => t('crud.operation'),
      operationConfigure: {
        actions: [
          {
            name: 'edit',
            icon: 'material-symbols:person-edit',
            show: ({ row }) => showBtn('permission:user:update', row),
            text: () => t('crud.edit'),
            onClick: ({ row }) => {
              dialog.setTitle(t('crud.edit'))
              dialog.open({ formType: 'edit', data: row })
            },
          },
          {
            name: 'del',
            show: ({ row }) => showBtn('permission:user:delete', row),
            icon: 'mdi:delete',
            text: () => t('crud.delete'),
            onClick: ({ row }, proxy: MaProTableExpose) => {
              msg.delConfirm(t('crud.delDataMessage')).then(async () => {
                const response = await deleteByIds([row.id])
                if (response.code === ResultCode.SUCCESS) {
                  msg.success(t('crud.delSuccess'))
                  proxy.refresh()
                }
              })
            },
          },
          {
            name: 'setRole',
            show: ({ row }) => showBtn('permission:user:role', row),
            icon: 'material-symbols:person-add-rounded',
            text: () => t('baseUser.setRole'),
            onClick: ({ row }) => {
              dialog.setTitle(t('baseUser.setRole'))
              dialog.open({ formType: 'setRole', data: row })
            },
          },
          {
            name: 'initPassword',
            show: ({ row }) => showBtn('permission:user:password', row),
            icon: 'material-symbols:passkey',
            text: () => t('baseUser.initPassword'),
            onClick: ({ row }) => {
              msg.confirm(t('baseUser.setPassword')).then(async () => {
                const response = await resetPassword(row.id)
                if (response.code === ResultCode.SUCCESS) {
                  msg.success(t('baseUser.setPasswordSuccess'))
                }
              })
            },
          },
          {
            name: 'noAllowSuperAdmin',
            show: ({ row }) => row.id === 1 && row.username === 'SuperAdmin',
            disabled: () => true,
            text: () => t('baseUser.superAdminNoEdit'),
          },
        ],
      },
    },
  ]
}
