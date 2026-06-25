/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { Dictionary } from '#/global'

export default [
  { label: '全部数据权限', value: 'ALL', i18n: 'dictionary.dataScope.all', color: 'primary' },
  { label: '本部门数据权限', value: 'DEPT_SELF', i18n: 'dictionary.dataScope.deptSelf', color: 'primary' },
  { label: '本部门及所有子部门数据权限', value: 'DEPT_TREE', i18n: 'dictionary.dataScope.deptTree', color: 'primary' },
  { label: '本人数据权限', value: 'SELF', i18n: 'dictionary.dataScope.self', color: 'primary' },
  { label: '自选部门数据权限', value: 'CUSTOM_DEPT', i18n: 'dictionary.dataScope.customDept', color: 'primary' },
  { label: '自定义函数数据权限', value: 'CUSTOM_FUNC', i18n: 'dictionary.dataScope.customFunc', color: 'primary' },
] as Dictionary[]
