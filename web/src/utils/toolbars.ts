/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { App } from 'vue'
import type { MineToolbar } from '#/global'
import MaSvgIcon from '@/components/ma-svg-icon/index.vue'

export default function toolbars() {
  const settingStore = useSettingStore()
  const toolbars = ref<MineToolbar[]>([])
  const state = ref(true)
  const defaultToolbars = ref<MineToolbar[]>([
    {
      name: 'search',
      title: 'mineAdmin.toolbars.search',
      icon: 'heroicons:magnifying-glass-20-solid',
      show: true,
      component: () => import(('@/layouts/components/bars/toolbar/components/search.tsx')),
    },
    {
      name: 'notification',
      title: 'mineAdmin.toolbars.notification',
      icon: 'heroicons:bell',
      show: true,
      component: () => import(('@/layouts/components/bars/toolbar/components/notification.tsx')),
    },
    {
      name: 'translate',
      icon: 'heroicons:language-20-solid',
      title: 'mineAdmin.toolbars.translate',
      show: true,
      component: () => import(('@/layouts/components/bars/toolbar/components/translate.tsx')),
    },
    {
      name: 'fullscreen',
      icon: 'mingcute:fullscreen-line',
      title: 'mineAdmin.toolbars.fullscreen',
      show: true,
      component: () => import(('@/layouts/components/bars/toolbar/components/fullscreen.tsx')),
    },
    {
      name: 'switchMode',
      icon: 'lets-icons:color-mode-light',
      title: 'mineAdmin.toolbars.switchMode',
      show: true,
      component: () => import(('@/layouts/components/bars/toolbar/components/switch-mode.tsx')),
    },
    {
      name: 'settings',
      icon: 'heroicons:cog-solid',
      title: 'mineAdmin.toolbars.settings',
      show: true,
      component: () => import(('@/layouts/components/bars/toolbar/components/settings.tsx')),
    },
  ])

  toolbars.value = defaultToolbars.value

  const getShowToolbar = () => {
    const toolbarSettings = settingStore.getSettings('toolBars') || []
    return toolbars.value.map((item) => {
      const savedItem = toolbarSettings.find((setting: MineToolbar) => setting.name === item.name)
      return {
        ...item,
        show: savedItem ? savedItem.show : item.show,
      }
    }).filter(item => item.show)
  }

  const render = () => {
    return Promise.all(getShowToolbar().map(async (item: MineToolbar) => {
      const handle = item?.handle ? item.handle : () => {}
      const className = typeof item?.className === 'function' ? item.className() : item.className
      if (item?.handle) {
        return h(
          MaSvgIcon,
          {
            class: ['tool-icon', className],
            size: 20,
            name: item.icon,
            onClick: async () => await handle(item),
          },
        )
      }
      else if (item?.component) {
        const app = getCurrentInstance()?.appContext?.app as App
        const component = await item.component()
        if (app && !app?._context?.components[component.default.name]) {
          app.component(component.default.name, component.default)
        }
        return h(component.default)
      }
    }))
  }

  const add = (toolbar: MineToolbar) => {
    if (!toolbars.value.find(item => item.name === toolbar.name)) {
      toolbars.value.push(toolbar)
    }
  }

  const remove = (name: string) => {
    toolbars.value = toolbars.value.filter(item => item.name !== name)
  }

  return {
    state,
    defaultToolbars,
    toolbars,
    getShowToolbar,
    add,
    remove,
    render,
  }
}
