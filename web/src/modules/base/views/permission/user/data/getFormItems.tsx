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
import type { UserVo } from '~/base/api/user.ts'
import MaUploadImage from '@/components/ma-upload-image/index.vue'
import MaDictRadio from '@/components/ma-dict-picker/ma-dict-radio.vue'

export default function getFormItems(formType: 'add' | 'edit' = 'add', t: any, model: UserVo, deptData: Ref<any[]>): MaFormItem[] {
  if (formType === 'add') {
    model.password = '123456'
    model.status = 1
    model.user_type = 100
    model.policy = []
  }

  const departmentList = deptData.value.filter((_, index) => index > 0)

  model.backend_setting = []

  return [
    {
      label: () => t('baseUserManage.avatar'),
      prop: 'avatar',
      render: () => MaUploadImage,
    },
    {
      label: () => t('baseUserManage.username'),
      prop: 'username',
      render: 'input',
      cols: { md: 12, xs: 24 },
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: t('baseUserManage.username') }),
      },
      itemProps: {
        rules: [{ required: true, message: t('form.requiredInput', { msg: t('baseUserManage.username') }) }],
      },
    },
    {
      label: () => t('baseUserManage.nickname'),
      prop: 'nickname',
      render: 'input',
      cols: { md: 12, xs: 24 },
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: t('baseUserManage.nickname') }),
      },
      itemProps: {
        rules: [{ required: true, message: t('form.requiredInput', { msg: t('baseUserManage.nickname') }) }],
      },
    },
    {
      label: () => t('baseUserManage.password'),
      prop: 'password',
      render: 'input',
      cols: { md: 12, xs: 24 },
      renderProps: {
        disabled: formType === 'edit',
        placeholder: t('form.pleaseInput', { msg: t('baseUserManage.password') }),
      },
      itemProps: {
        rules: formType === 'add' ? [{ required: true, message: t('form.requiredInput', { msg: t('baseUserManage.password') }) }] : [],
      },
    },
    {
      label: () => t('baseUserManage.phone'),
      prop: 'phone',
      render: 'input',
      cols: { md: 12, xs: 24 },
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: t('baseUserManage.phone') }),
      },
    },
    {
      label: () => t('baseUserManage.dept'),
      prop: 'department.id',
      render: () => <el-tree-select />,
      renderProps: {
        data: departmentList,
        multiple: true,
        filterable: true,
        clearable: true,
        props: { label: 'name' },
        checkStrictly: true,
        nodeKey: 'id',
        placeholder: t('form.pleaseInput', { msg: t('baseUserManage.dept') }),
        onChange: (value: any) => {
          console.log(value)
        },
      },
    },
    {
      label: () => t('baseUserManage.post'),
      prop: 'position.id',
      render: () => <el-tree-select />,
      cols: { md: 12, xs: 24 },
      renderProps: {
        data: departmentList,
        multiple: true,
        filterable: true,
        clearable: true,
        props: { label: 'name' },
        checkStrictly: true,
        nodeKey: 'id',
        placeholder: t('form.pleaseInput', { msg: t('baseUserManage.dept') }),
      },
    },
    {
      label: () => t('baseUserManage.dataScope'),
      render: () => <el-button />,
      cols: { md: 12, xs: 24 },
      renderProps: {
        type: 'primary',
        plain: true,
      },
      renderSlots: {
        default: () => t('baseUserManage.setDataScope'),
      },
    },
    {
      label: () => t('baseUserManage.email'),
      prop: 'email',
      render: 'input',
      cols: { md: 12, xs: 24 },
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: t('baseUserManage.email') }),
      },
    },
    {
      label: () => t('baseUserManage.userType'),
      prop: 'user_type',
      cols: { md: 12, xs: 24 },
      render: () => MaDictRadio,
      renderProps: {
        renderMode: 'button',
        placeholder: t('form.pleaseInput', { msg: t('baseUserManage.userType') }),
        dictName: 'base-userType',
      },
    },
    {
      label: () => t('crud.remark'),
      prop: 'remark',
      render: 'input',
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: t('crud.remark') }),
        type: 'textarea',
      },
    },
    {
      label: () => t('crud.status'),
      prop: 'status',
      render: () => MaDictRadio,
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: t('crud.status') }),
        dictName: 'system-status',
      },
    },
  ]
}
