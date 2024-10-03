/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { MaFormItem } from '@mineadmin/form'
import MaUploadImage from '@/components/ma-upload-image/index.vue'
import type { UserVo } from '~/base/api/user.ts'

export default function getFormItems(formType: 'add' | 'edit' = 'add', t: any, model: UserVo): MaFormItem[] {
  if (formType === 'add') {
    model.password = '123456'
  }
  if (formType === 'edit') {
    model.password = ''
  }

  return [
    {
      label: () => t('baseUser.avatar'),
      prop: 'avatar',
      render: () => MaUploadImage,
    },
    {
      label: () => t('baseUser.username'),
      prop: 'username',
      render: 'input',
      cols: { span: 12 },
      renderProps: {
        placeholder: '请输入用户名',
      },
      itemProps: {
        rules: [{ required: true, message: '用户名必填' }],
      },
    },
    {
      label: () => t('baseUser.nickname'),
      prop: 'nickname',
      render: 'input',
      cols: { span: 12 },
      renderProps: {
        placeholder: '请输入昵称',
      },
      itemProps: {
        rules: [{ required: true, message: '昵称必填' }],
      },
    },
    {
      label: () => t('baseUser.password'),
      prop: 'password',
      hide: formType === 'edit',
      render: 'input',
      cols: { span: 12 },
      renderProps: {
        placeholder: '请输入密码',
      },
      itemProps: {
        rules: [{ required: true, message: '密码必填' }],
      },
    },
    {
      label: () => t('baseUser.nickname'),
      prop: 'nickname',
      render: 'input',
      cols: { span: 12 },
      renderProps: {
        placeholder: '请输入昵称',
      },
      itemProps: {
        rules: [{ required: true, message: '昵称必填' }],
      },
    },
  ]
}
