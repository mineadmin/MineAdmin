/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { MaProTableColumns } from '@mineadmin/pro-table'
import { ElMessage as msg } from 'element-plus'
import defaultAvatar from '@/assets/images/defaultAvatar.jpg'

const cols: MaProTableColumns[] = [
  // 多选列
  { type: 'selection', showOverflowTooltip: false },
  // 索引序号列
  { type: 'index' },
  // 普通列
  { label: '头像', prop: 'avatar', width: '120px',
    cellRender: ({ row }) => (
      <div class="flex-center">
        <el-avatar src={(row.avatar === '' || !row.avatar) ? defaultAvatar : row.avatar} alt={row.username} />
      </div>
    ),
  },
  { label: '用户名', prop: 'username' },
  { label: '昵称', prop: 'nickname' },
  { label: '手机', prop: 'phone' },
  { label: '邮箱', prop: 'email' },
  { label: '状态', prop: 'status',
    cellRender: ({ row }) => (
      <el-tag type={row.status === 1 ? 'primary' : 'danger'}>
        { row.status === 1 ? '正常' : '禁用' }
      </el-tag>
    ),
  },
  // 操作列
  {
    type: 'operation',
    operationConfigure: {
      actions: [
        {
          name: 'edit',
          icon: 'material-symbols:person-edit',
          text: '编辑',
          onClick: ({ row }) => {
            msg.success(`打开编辑${row.id}`)
          },
        },
        {
          name: 'del',
          icon: 'mdi:delete',
          text: '删除',
        },
        {
          name: 'setRole',
          icon: 'material-symbols:person-add-rounded',
          text: '赋予角色',
        },
        {
          name: 'initPassword',
          icon: 'material-symbols:passkey',
          text: '初始化密码',
        },
      ],
    },
  },
]

export default cols
