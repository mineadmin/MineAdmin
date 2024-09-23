/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */

import type { MaSearchItem } from '@mineadmin/search'

const items: MaSearchItem[] = [
  {
    label: '用户名',
    prop: 'username',
    render: 'input',
  },
  {
    label: '用户昵称',
    prop: 'nickname',
    render: 'input',
  },
  {
    label: '手机号',
    prop: 'phone',
    render: 'input',
  },
  {
    label: '邮箱',
    prop: 'phone',
    render: 'input',
  },
  {
    label: '状态',
    prop: 'status',
    render: () => (
      <el-radio-group>
        <el-radio value={1}>启用</el-radio>
        <el-radio value={2}>禁用</el-radio>
      </el-radio-group>
    ),
  },
]

export default items
