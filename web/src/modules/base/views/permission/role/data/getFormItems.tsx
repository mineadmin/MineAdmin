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
import type { RoleVo } from '~/base/api/role.ts'
import MaDictRadio from '@/components/ma-dict-picker/ma-dict-radio.vue'

export default function getFormItems(formType: 'add' | 'edit' = 'add', t: any, model: RoleVo): MaFormItem[] {
  if (formType === 'add') {
    model.status = 1
    model.sort = 0
  }

  return [
    {
      label: () => t('baseRoleManage.name'),
      prop: 'name',
      render: 'input',
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: t('baseRoleManage.name') }),
      },
      itemProps: {
        rules: [{ required: true, message: t('form.requiredInput', { msg: t('baseRoleManage.name') }) }],
      },
    },
    {
      label: () => t('baseRoleManage.code'),
      prop: 'code',
      render: 'input',
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: t('baseRoleManage.code') }),
      },
      itemProps: {
        rules: [{ required: true, message: t('form.requiredInput', { msg: t('baseRoleManage.code') }) }],
      },
    },
    {
      label: () => t('crud.sort'),
      prop: 'sort',
      render: 'inputNumber',
      cols: { md: 12, xs: 24 },
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: t('crud.sort') }),
        class: 'w-full',
      },
    },
    {
      label: () => t('crud.status'),
      prop: 'status',
      render: () => MaDictRadio,
      cols: { md: 12, xs: 24 },
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: t('crud.status') }),
        dictName: 'system-status',
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
  ]
}
