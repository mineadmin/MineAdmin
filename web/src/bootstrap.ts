/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */

import type { ProviderService } from '#/global'
import type { App, Directive } from 'vue'
import * as directives from '@/directives'
import useThemeColor from '@/hooks/useThemeColor'
import useTabStore from '@/store/modules/useTabStore.ts'
import toolbars from '@/utils/toolbars.ts'
import messages from '@intlify/unplugin-vue-i18n/messages'
import ElementPlus from 'element-plus'
import VConsole from 'vconsole'
import { createI18n } from 'vue-i18n'
import router from './router'
import pinia from './store'
import './utils/copyright.ts'
import iconCollections from '@/iconify/index.json'
import { downloadAndInstall } from '@/iconify/index.ts'

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
// 引入resources scss
import '@/assets/styles/resources/variables.scss'
import '@/assets/styles/resources/utils.scss'
import '@/assets/styles/resources/element.scss'

async function createI18nService(app: App) {
  const locales: any[] = Object.entries(import.meta.glob('./locales/*.y(a)?ml')).map(([key]: any) => {
    const [, value, label] = key.match(/^.\/locales\/(\w+)\[([^[\]]+)\]\.yaml$/)
    return { label, value }
  })
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
    silentTranslationWarn: true,
    silentFallbackWarn: true,
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

async function otherWorker(app: App) {
  if (navigator && navigator.userAgent && navigator.userAgent.match(/Win[a-z0-9]*;/)) {
    document.documentElement.classList.add('mine-ui-scrollbars')
  }
  useTabStore().initTab()
  app.config.globalProperties.$toolbars = toolbars()

  if (import.meta.env?.VITE_OPEN_vCONSOLE === 'true') {
    app.config.globalProperties.$vconsole = new VConsole()
  }

  const dictStore = useDictStore()

  Object.entries(app.config.globalProperties.$dictionary).map((item: any) => {
    const [name, data] = item
    dictStore.push(name, data)
  })

  if (iconCollections?.isOfflineUse) {
    await Promise.all(iconCollections?.collections?.map(async (name: string) => {
      try {
        await downloadAndInstall(name)
      }
      catch {
        console.error(`加载[${name}]离线图标失败，请在 public/icons 目录下检查文件是否存在，可执行 pnpm gen:icons 命令重新生成`)
      }
    }))
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
  await otherWorker(app)
  await createI18nService(app)
  await usePluginStore().registerPlugin(app)
  await router.isReady()
  useThemeColor().initThemeColor()
}

export default bootstrap
