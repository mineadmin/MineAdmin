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

export default function getSearchItems(t: any): MaSearchItem[] {
  return [
    {
      label: () => t('username'),
      prop: 'username',
      render: 'input',
    },
    {
      label: () => t('router'),
      prop: 'router',
      render: 'input',
    },
    {
      label: () => t('service_name'),
      prop: 'service_name',
      render: 'input',
    },
    {
      label: () => t('ip'),
      prop: 'ip',
      render: 'input',
    },
    {
      label: () => t('remark'),
      prop: 'remark',
      render: 'input',
    },
  ]
}
