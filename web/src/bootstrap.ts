/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import './utils/copyright.ts'
import type { App, Directive } from 'vue'
import ElementPlus from 'element-plus'
import { createI18n } from 'vue-i18n'
import messages from '@intlify/unplugin-vue-i18n/messages'
import VConsole from 'vconsole'
import pinia from './store'
import router from './router'
import * as directives from '@/directives'
import type { ProviderService } from '#/global'
import useThemeColor from '@/hooks/useThemeColor'
import useTabStore from '@/store/modules/useTabStore.ts'
import toolbars from '@/utils/toolbars.ts'

// 加载 Element-plus 样式
import 'element-plus/dist/index.css'
import 'element-plus/theme-chalk/dark/css-vars.css'

import 'overlayscrollbars/overlayscrollbars.css'

// 加载 svg 图标
import 'virtual:uno.css'
import 'virtual:svg-icons-register'

// 全局样式
import '@/assets/styles/globals.scss'
// vue-m-message样式
import 'vue-m-message/dist/style.css'

async function createI18nService(app: App) {
  const locales: any[] = Object.entries(import.meta.glob('./locales/*.y(a)?ml')).map(([key]: any) => {
    const [, value, label] = key.match(/^.\/locales\/(\w+)\[([^[\]]+)\]\.yaml$/)
    return { label, value }
  })
  // // 获得插件目录的所有语言,通过value合并
  // const pluginLocales: any[] = Object.entries(import.meta.glob('./plugins/*/**/locales/*.y(a)?ml'))?.map(([key]: any) => {
  //   const [, value, label] = key.match(/\/locales\/(\w+)\[([^[\]]+)\]\.yaml$/)
  //   return { label, value }
  // })
  //
  // // 将 pluginLocales 通过value合并到locales 相同的value覆盖
  // pluginLocales.forEach((item: any) => {
  //   const index = locales.findIndex((value: any) => value.value === item.value)
  //   if (index !== -1) {
  //     locales[index] = item
  //   } else {
  //     locales.push(item)
  //   }
  // })
  useUserStore().setLocales(locales)
  Object.keys(messages as any).map((name: string) => {
    const matchValue = name.match(/(\w+)/) as RegExpMatchArray | null
    if (messages && matchValue) {
      messages[matchValue[1]] = messages[name]
      delete messages[name]
    }
  })

  app.use(createI18n({
    legacy: false,
    globalInjection: true,
    fallbackLocale: 'zh_CN',
    locale: useUserStore().getLanguage(),
    messages,
  }))
}

async function initProvider(app: App) {
  const pvs = import.meta.glob('./provider/**/index.ts')
  let path: string
  for (path in pvs) {
    const module: any = await pvs[path]()
    const provider: ProviderService.Provider = module.default
    provider?.init && await provider.init()
    provider.setProvider(app)
  }
}

function otherWorker(app: App) {
  if (navigator && navigator.userAgent && navigator.userAgent.match(/Win[a-z0-9]*;/)) {
    document.documentElement.classList.add('mine-ui-scrollbars')
  }
  useThemeColor().initThemeColor()
  useTabStore().initTab()
  app.config.globalProperties.$toolbars = toolbars()

  if (import.meta.env?.VITE_OPEN_vCONSOLE === 'true') {
    new VConsole()
  }
}

function registerDirectives(app: App) {
  Object.keys(directives).map((key) => {
    app.directive(key, (directives as { [key: string]: Directive })[key])
  })
}

async function bootstrap(app: App): Promise<void> {
  await initProvider(app)
  app.use(pinia)
  app.use(router)
  app.use(ElementPlus, {})
  registerDirectives(app)
  otherWorker(app)
  await createI18nService(app)
  await usePluginStore().registerPlugin(app)
  await router.isReady()
}

export default bootstrap
