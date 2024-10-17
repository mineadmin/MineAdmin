/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */

export default [
  {
    name: 'uc:index',
    path: '/uc/index',
    component: () => import(('~/base/views/uc/index.vue')),
    meta: {
      title: '首页',
      icon: 'heroicons:user-circle',
      i18n: 'menu.uc:index',
    },
  },
]
