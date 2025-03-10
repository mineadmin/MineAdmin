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

export default function getFormItems(formType: 'add' | 'edit' = 'add', t: any): MaFormItem[] {
  const treeSelectRef = ref()
  const deptList = ref([])

  useHttp().get('/admin/department/list?level=1').then((res: any) => {
    deptList.value = res.data.list
  })

  return [
    {
      label: () => t('baseDepartment.parentDepartment'), prop: 'parent_id', render: () => (
        <el-tree-select
          ref={treeSelectRef}
          data={deptList.value}
          props={{ value: 'id', label: 'name' }}
          check-strictly={true}
          default-expand-all={true}
          clearable={true}
        >
        </el-tree-select>
      ),
      renderProps: {
        class: 'w-full',
        placeholder: t('baseDepartment.placeholder.parentDepartment'),
      },
    },
    {
      label: () => t('baseDepartment.name'),
      prop: 'name',
      render: 'input',
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: t('baseDepartment.name') }),
      },
      itemProps: {
        rules: [{ required: true, message: t('form.requiredInput', { msg: t('baseDepartment.placeholder.name') }) }],
      },
    },
  ]
}
