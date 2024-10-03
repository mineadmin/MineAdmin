/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import '@/layouts/style/search-panel.scss'
import { useMagicKeys } from '@vueuse/core'
import menuGotoHandle from '@/utils/menuGotoHandle.ts'
import type { MineRoute } from '#/global'

export default defineComponent({
  name: 'searchPanel',
  setup() {
    const searchInputEl = ref<HTMLElement>()
    const { setSearchPanelEnable, getSearchPanelEnable } = useSettingStore()
    const routeKey = ref<string>('')
    const router = useRouter()
    const routes = computed(() => {
      return router.getRoutes().filter((item) => {
        return !['notFound', 'MineRootLayoutRoute', 'login'].includes(item.name as string)
          && !item.meta?.hidden
          && (item.components || item.meta?.type === 'L')
          && !(/^\/uc/.test(item.path))
          && (
            item.path?.includes(routeKey.value)
            || (item.name as string)?.indexOf(routeKey.value) > -1
            || (item.meta?.title as string)?.indexOf(routeKey.value) > -1
          )
      })
    })

    const handleClick = async (item: MineRoute.routeRecord) => {
      await menuGotoHandle(router, item)
      setSearchPanelEnable(false)
    }

    onMounted(() => {
      const { escape, alt, s } = useMagicKeys()
      watchEffect(async () => {
        if (alt.value && s.value && !getSearchPanelEnable()) {
          setSearchPanelEnable(true)
          await nextTick()
          searchInputEl.value?.focus()
        }
        if (escape.value && getSearchPanelEnable()) {
          setSearchPanelEnable(false)
        }
      })
    })

    return () => (
      <div>
        <div class="mine-search-panel-mask"></div>
        <div class="mine-search-panel-container">
          <div class="mine-search-input-container">
            <ma-svg-icon name="heroicons:magnifying-glass-solid" size={20} class="text-gray-4 dark-text-gray-4" />
            <input v-model={routeKey.value} class="mine-search-input" placeholder={useTrans('mineAdmin.search.placeholder')} ref={searchInputEl} />
            <ma-svg-icon name="material-symbols:close-rounded" size={20} class="cursor-pointer dark-text-stone-2" onClick={() => setSearchPanelEnable(false)} />
          </div>

          {routes.value.length > 0
          && (
            <ul class="mine-search-list-container">
              {routes.value.map((item) => {
                return (
                  <li class="mine-search-item-list" onClick={async () => await handleClick(item as MineRoute.routeRecord)}>
                    <ma-svg-icon
                      name={item.meta?.icon ? item.meta?.icon : 'material-symbols:brightness-empty-outline'}
                      size={20}
                    />
                    <div class="mine-search-text">
                      <span class="w-6/12">{(item.meta?.i18n ? useTrans(item.meta?.i18n) : item.meta?.title) ?? useTrans('mineAdmin.search.noNameMenu') }</span>
                      <m-tooltip text={item.meta?.type === 'L' ? item.meta?.link : item.path}>
                        <span class="mine-search-route-path">{item.meta?.type === 'L' ? item.meta?.link : item.path}</span>
                      </m-tooltip>
                    </div>
                  </li>
                )
              })}
            </ul>
          )}
          {routes.value.length === 0 && (
            <div class="mt-35 w-full flex flex-col justify-center text-center text-gray-4">
              <ma-svg-icon name="tabler:hourglass-empty" size={50} class="mx-auto" />
              <span class="mt-3 text-sm">{useTrans('mineAdmin.search.notResult')}</span>
            </div>
          )}
        </div>
      </div>
    )
  },
})
