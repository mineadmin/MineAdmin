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
import type { MineRoute } from '#/global'

const useMenuStore = defineStore(
  'useMenuStore',
  () => {
    const { t } = useI18n()
    const route = useRoute()
    const router = useRouter()
    const routeStore = useRouteStore()
    const settingStore = useSettingStore()
    const activeTopMenu = ref<MineRoute.routeRecord>()
    const topMenu = ref<MineRoute.routeRecord[]>([])
    const subMenu = ref<MineRoute.routeRecord[]>([])
    const watchRoute = ref<MineRoute.routeRecord>()
    const allMenu = ref(computed((): MineRoute.routeRecord[] => {
      return (['classic', 'banner'].includes(settingStore.getSettings('app')?.layout as string) || settingStore.getMobileState())
        ? topMenu.value
        : subMenu.value
    }))

    function setSubAsideWidthByZero() {
      if (settingStore.isColumnsLayout() || settingStore.isMixedLayout()) {
        settingStore.setSubAsideWidth('0px')
      }
    }

    function setSubAsideWidthByDefault() {
      settingStore.setSubAsideWidth(
        settingStore.getMenuCollapseState()
          ? 'var(--mine-g-sub-aside-collapse-width)'
          : 'var(--mine-g-sub-aside-width)',
      )
    }

    function setTopMenu() {
      watchRoute.value = route as MineRoute.routeRecord
      if (route.matched[1] && route.matched[1].meta && route.matched[1].meta.breadcrumb) {
        const breadcrumb = route.matched[1].meta.breadcrumb as MineRoute.routeRecord[]
        activeTopMenu.value = breadcrumb.find((item, index) => index === 0)
      }
      else {
        activeTopMenu.value = undefined
      }
    }

    function init() {
      setTopMenu()

      watch((): any => routeStore.routesRaw, (routesRaw: MineRoute.routeRecord[]) => {
        topMenu.value = routesRaw.find((route: any): boolean => route.path === '/')?.children as MineRoute.routeRecord[] ?? []
      }, { immediate: true, deep: true })

      watch((): any => route.name, async (newName: string, oldName: string) => {
        setTopMenu()
        const newRoute = router.getRoutes().find((route: any): boolean => route.name === newName)
        const oldRoute = router.getRoutes().find((route: any): boolean => route.name === oldName)

        settingStore.setTitle(route?.meta?.i18n ? t(route.meta.i18n as string) : route.meta.title as string)

        await usePluginStore().callHooks('routerRedirect', { oldRoute, newRoute }, router)
      }, { deep: true })

      watch((): any => activeTopMenu.value, (newActiveTopMenu: MineRoute.routeRecord) => {
        if (newActiveTopMenu && newActiveTopMenu.children && newActiveTopMenu.children?.length > 0) {
          subMenu.value = newActiveTopMenu.children
          setSubAsideWidthByDefault()
        }
        else {
          setSubAsideWidthByZero()
          subMenu.value = []
        }
      }, { immediate: true, deep: true })

      watch((): string => settingStore.getSettings('app')?.layout as string, () => {
        if (settingStore.isClassicLayout() || settingStore.isMixedLayout()) {
          settingStore.setMenuCollapseState(false)
          settingStore.setFixedAsideState(false)
          if (settingStore.isMixedLayout()) {
            subMenu.value.length > 0 ? setSubAsideWidthByDefault() : setSubAsideWidthByZero()
          }
          else {
            settingStore.setSubAsideWidth('var(--mine-g-sub-aside-width)')
          }
        }
        else if (settingStore.isBannerLayout()) {
          setSubAsideWidthByZero()
          settingStore.setMainAsideWidth('0px')
        }
        else if (settingStore.isColumnsLayout()) {
          settingStore.setMainAsideWidth('var(--mine-g-main-aside-width)')
          subMenu.value.length > 0 ? setSubAsideWidthByDefault() : setSubAsideWidthByZero()
          settingStore.setHeaderHeight('0px')
        }
        else {
          setSubAsideWidthByZero()
        }
      })
    }

    return {
      topMenu,
      subMenu,
      allMenu,
      watchRoute,
      activeTopMenu,
      init,
      setSubAsideWidthByZero,
      setSubAsideWidthByDefault,
    }
  },
)

export default useMenuStore
