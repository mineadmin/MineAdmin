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
import MaDictSelect from '@/components/ma-dict-picker/ma-dict-select.vue'
import MaRemoteSelect from '@/components/ma-remote-select/index.vue'

export default function getSearchItems(t: any): MaSearchItem[] {
  return [
    {
      label: () => t('baseUser.username'),
      prop: 'username',
      render: () => MaRemoteSelect,
      renderProps: {
        placeholder: '请选择用户',
        url: '/admin/role/list',
        props: {
          label: 'name', value: 'id',
        },
        dataHandle: (response: any) => {
          return response.data.list
        },
      },
    },
    {
      label: () => t('baseUser.nickname'),
      prop: 'nickname',
      render: 'input',
    },
    {
      label: () => t('baseUser.phone'),
      prop: 'phone',
      render: 'input',
    },
    {
      label: () => t('baseUser.email'),
      prop: 'email',
      render: 'input',
    },
    {
      label: () => t('baseUser.status'),
      prop: 'status',
      render: () => MaDictSelect,
      renderProps: {
        dictName: 'system-status',
      },
    },
  ]
}
