import type { App } from 'vue'
import messages from '@intlify/unplugin-vue-i18n/messages'
import { createI18n } from 'vue-i18n'

type LocaleMessages = Record<string, any>

function normalizeLocaleName(name: string): string {
  const filename = name.split('/').pop()?.split('\\').pop() ?? name
  return filename.match(/^(\w+)(?:\[[^[\]]+\])?(?:\.ya?ml)?$/)?.[1] ?? filename.match(/(\w+)/)?.[1] ?? name
}

function normalizeMessages(source: LocaleMessages): LocaleMessages {
  return Object.entries(source).reduce((normalized, [name, message]) => {
    normalized[normalizeLocaleName(name)] = message
    return normalized
  }, {} as LocaleMessages)
}

function resolveLocales() {
  return Object.entries(import.meta.glob('../locales/*.y(a)?ml')).map(([key]: any) => {
    const match = key.match(/^\.\.\/locales\/(\w+)\[([^[\]]+)\]\.ya?ml$/)
    if (match) {
      const [, value, label] = match
      return { label, value }
    }

    const value = normalizeLocaleName(key)
    return { label: value, value }
  })
}

export const i18n = createI18n({
  legacy: false,
  globalInjection: true,
  fallbackLocale: 'zh_CN',
  locale: 'zh_CN',
  missingWarn: false,
  fallbackWarn: false,
  messages: normalizeMessages(messages as LocaleMessages),
})

export const globalComposer = i18n.global

export function setupI18n(app: App) {
  const userStore = useUserStore()

  globalComposer.locale.value = userStore.getLanguage() as any
  userStore.setLocales(resolveLocales())
  app.use(i18n)
}
