/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import { Transition } from 'vue'
import Logo from '../logo'
import '@/layouts/style/main-aside.scss'
import type { MineRoute } from '#/global'
import menuGotoHandle from '@/utils/menuGotoHandle'
import useParentNode from '@/hooks/useParentNode'

export default defineComponent ({
  name: 'MainAside',
  setup() {
    const route = useRoute()
    const router = useRouter()
    const menuStore = useMenuStore()
    const { getSettings, showMineHeader, isBannerLayout } = useSettingStore()
    const mainAsideSetting = getSettings('mainAside')

    const mainAsideRef = ref()
    const shadowTop = ref<boolean>(false)
    const shadowBottom = ref<boolean>(false)

    function onAsideScroll() {
      const scrollTop = mainAsideRef.value.scrollTop
      shadowTop.value = scrollTop > 0
      const clientHeight = mainAsideRef.value.clientHeight
      const scrollHeight = mainAsideRef.value.scrollHeight
      shadowBottom.value = Math.ceil(scrollTop + clientHeight) < scrollHeight
    }
    const asideListClass = computed(() => {
      return {
        'mine-main-aside-list': true,
        'shadow-top': shadowTop.value,
        'shadow-bottom': shadowBottom.value,
        'pt-2': !showMineHeader(),
        'flex gap-x-2 px-2 items-center': showMineHeader(),
      }
    })
    const goToAppoint = async (e: any, route: MineRoute.routeRecord) => {
      await menuGotoHandle(router, route)
      menuStore.activeTopMenu = route
      if (getSettings('mainAside').enableOpenFirstRoute || route?.children) {
        const aNode = useParentNode(e, 'a')
        document.querySelector('a.active')?.classList.remove('active')
        aNode.classList.add('active')
      }
    }
    return () => {
      return (
        <Transition name={isBannerLayout() ? 'mine-main-header' : 'mine-main-aside'}>
          <div class={{
            'mine-main-aside-content': true,
            '!border-r-1': useMenuStore().subMenu.length > 0,
            'flex-col': !showMineHeader(),
            '!w-full px-3': showMineHeader(),
            '!hidden !lg:flex': true,
          }}
          >
            { showMineHeader() || (<Logo showTitle={false} />) }
            <div
              ref={mainAsideRef}
              class={asideListClass.value}
              onScroll={onAsideScroll}
              style={`${showMineHeader() ? 'width: calc(100% - 70px) !important;' : ''}`}
            >
              {menuStore.topMenu.map((menu: MineRoute.routeRecord, _: number) => (
                <a
                  v-show={!menu?.meta?.hidden}
                  class={{
                    'active': (menu.name === route?.meta?.activeName || menu.name === route.name || menuStore.activeTopMenu?.name === menu.name),
                    'w-[70%] w-max-[70%]': mainAsideSetting.showTitle,
                    'w-[40%] max-w-[40%]': !mainAsideSetting.showTitle,
                    'w-[50%] max-w-[50%]': !mainAsideSetting.showIcon,
                    'h-[35px]': !mainAsideSetting.showIcon,
                    'mx-auto mb-1.5 gap-y-0.5 px-1 py-1 block': !showMineHeader(),
                    '!w-auto flex items-center px-2 h-11 gap-x-1': showMineHeader(),
                  }}
                  title={menu?.meta?.i18n ? useTrans(menu.meta?.i18n) : menu?.meta?.title}
                  onClick={async (e: any) => await goToAppoint(e, menu)}
                >
                  { mainAsideSetting.showIcon && menu?.meta?.icon && (
                    <div>
                      <ma-svg-icon
                        name={menu?.meta?.icon}
                        class={{
                          'mine-main-aside-icon': true,
                          'w-[30px] text-[18px]': showMineHeader(),
                        }}
                      />
                    </div>
                  )}
                  { mainAsideSetting.showTitle && (
                    <span class="route-link" to={menu.path}>
                      { menu?.meta?.i18n ? useTrans(menu?.meta?.i18n) : menu?.meta?.title }
                    </span>
                  )}
                </a>
              ))}
            </div>
            {
              router.hasRoute('MineAppStoreRoute')
              && (
                <m-tooltip text={useTrans('menu.appstore')} placement="right">
                  <a
                    class="h-14 flex cursor-pointer items-center justify-center"
                    onClick={() => router.push({ path: '/appstore' })}
                    title={useTrans('menu.appstore')}
                  >
                    <ma-svg-icon name="vscode-icons:file-type-azure" size={30} />
                  </a>
                </m-tooltip>
              )
            }
          </div>
        </Transition>
      )
    }
  },
})
