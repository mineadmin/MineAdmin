/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import { useI18n } from 'vue-i18n'

export default defineComponent({
  name: 'translate',
  setup() {
    const route = useRoute()
    const userStore = useUserStore()
    const settingStore = useSettingStore()
    const locales = userStore.getLocales()
    const { locale, t } = useI18n()
    async function changeLanguage(item: { label: string, value: string }) {
      userStore.setLanguage(item.value)
      const appSettings = settingStore.getSettings('app')
      if (appSettings) {
        appSettings.useLocale = item.value
      }
      locale.value = item.value
      settingStore.setTitle(route.meta?.i18n ? t(route.meta?.i18n as string) : route.meta?.title as string)
      await userStore.saveSettingToSever()
    }
    return () => (

      <m-dropdown
        class="min-w-[5rem]"
        triggers={['click']}
        style="position: relative; top: 2px"
        v-slots={{
          default: () => (
            <div>
              <ma-svg-icon
                className="tool-icon"
                name="heroicons:language-20-solid"
                size={20}
              />
            </div>
          ),
          popper: () => (
            <div>
              {locales.map((item: any) => (
                <m-dropdown-item
                  type="default"
                  disabled={item.value === locale.value}
                  handle={() => changeLanguage(item)}
                >
                  {item.label}
                </m-dropdown-item>
              ))}
            </div>
          ),
        }}
      />
    )
  },
})
