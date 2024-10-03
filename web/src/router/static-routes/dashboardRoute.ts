/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { RouteRecordRaw } from 'vue-router'

const dashboardRoute: RouteRecordRaw = {
  name: 'dashboard',
  path: '/dashboard',
  meta: {
    title: '仪表盘',
    i18n: 'menu.dashboard',
    icon: 'mingcute:dashboard-line',
  },
  redirect: '/dashboard/workbench',
  children: [
    {
      name: 'dashboard:workbench',
      path: '/dashboard/workbench',
      meta: {
        title: '工作台',
        i18n: 'menu.dashboard:workbench',
        icon: 'ic:round-computer',
        type: 'M',
        breadcrumbEnable: true,
        copyright: true,
        cache: true,
      },
      component: () => import(('~/base/views/dashboard/workbench.vue')),
    },
    {
      name: 'dashboard:analysis',
      path: '/dashboard/analysis',
      meta: {
        title: '分析页',
        i18n: 'menu.dashboard:analysis',
        icon: 'hugeicons:analysis-text-link',
        type: 'M',
        breadcrumbEnable: true,
        copyright: true,
        cache: true,
      },
      component: () => import(('~/base/views/dashboard/analysis.vue')),
    },
    {
      name: 'dashboard:report',
      path: '/dashboard/report',
      meta: {
        title: '统计报表',
        i18n: 'menu.dashboard:report',
        icon: 'iconoir:stats-report',
        type: 'M',
        breadcrumbEnable: true,
        copyright: true,
        cache: true,
      },
      component: () => import(('~/base/views/dashboard/report.vue')),
    },
  ],
}

export default dashboardRoute
