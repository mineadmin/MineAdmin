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
import '@/layouts/style/sub-aside.scss'
import MineMenu from '@/layouts/components/menu'

export default defineComponent ({
  name: 'SubAside',
  setup() {
    const shadowTop = ref<boolean>(false)
    const shadowBottom = ref<boolean>(false)
    const subAsideRef = ref<HTMLElement | null>()
    const menuStore = useMenuStore()
    const route = useRoute()
    const {
      getSettings,
      toggleCollapseButton,
      toggleFixedSubAsideButton,
      getMenuCollapseState,
      getFixedAsideState,
      isColumnsLayout,
      isMixedLayout,
      showMineHeader,
      showMineSubAside,
      getMobileState,
      setMobileSubmenuState,
      getMobileSubmenuState,
    } = useSettingStore()

    function onSubAsideScroll() {
      const scrollTop = subAsideRef.value?.scrollTop ?? 0
      shadowTop.value = scrollTop > 0
      const clientHeight = subAsideRef.value?.clientHeight ?? 0
      const scrollHeight = subAsideRef.value?.scrollHeight ?? 0
      shadowBottom.value = Math.ceil(scrollTop + clientHeight) < scrollHeight
    }
    const asideListClass = computed(() => {
      return {
        'mine-sub-aside-list': true,
        'shadow-top': shadowTop.value,
        'shadow-bottom': shadowBottom.value,
      }
    })
    return () => {
      return (
        <Transition name="mine-sub-aside-container">
          <div
            class={{
              'mine-sub-aside': true,
              'w-0': isColumnsLayout() || (isMixedLayout() && (!menuStore.activeTopMenu || menuStore.activeTopMenu?.children?.length === 0)),
              'w-[var(--mine-g-sub-aside-width)]': !isColumnsLayout(),
              '!absolute left-[var(--mine-g-main-aside-width)] !w-0': getFixedAsideState() && isColumnsLayout(),
              '!group-hover-w-[var(--mine-g-sub-aside-width)] group-hover-shadow-lg': getFixedAsideState() && isColumnsLayout() && menuStore.subMenu.length > 0,
              '!absolute shadow-md': getMobileState(),
              '!w-0': getMobileState() && !getMobileSubmenuState(),
              '!w-[var(--mine-g-sub-aside-width)]': getMobileState() && getMobileSubmenuState(),
            }}
          >
            {
              ((!showMineHeader() && showMineSubAside()) || (!getMenuCollapseState() && isColumnsLayout()) || getMobileState())
              && (
                <Logo
                  showLogo={showMineSubAside() || getMenuCollapseState()}
                  showTitle={!getMenuCollapseState()}
                />
              )
            }
            <div ref={subAsideRef} class={asideListClass.value} onScroll={onSubAsideScroll}>
              <MineMenu
                menu={menuStore.allMenu}
                value={route.meta.activeName || route.path}
                default-opens={['/']}
                collapse={getMenuCollapseState()}
              />
            </div>
            <div
              class={{
                'flex items-center h-13': true,
                'justify-center': getMenuCollapseState(),
                'justify-end px-3': showMineSubAside() || getMobileState(),
                'justify-between px-3': !getMenuCollapseState() && isColumnsLayout() && !getMobileState(),
              }}
            >
              <div
                v-show={!getMenuCollapseState() && isColumnsLayout() && !getMobileState()}
                class={{
                  'mine-sub-aside-fixed-button': true,
                }}
                onClick={toggleFixedSubAsideButton}
              >
                <ma-svg-icon name={
                  getFixedAsideState()
                    ? 'material-symbols:filter-alt-off-outline-sharp'
                    : 'material-symbols:filter-alt-outline-sharp'
                }
                />
              </div>
              <div
                v-show={getSettings('subAside').showCollapseButton && !getFixedAsideState() && !getMobileState()}
                class={{
                  'mine-sub-aside-collapse-button relative px-4': true,
                  '-rotate-z-180': !getMenuCollapseState(),
                }}
                onClick={toggleCollapseButton}
              >
                <ma-svg-icon name="system-uicons:window-collapse-right" />
              </div>
              <div
                v-show={getMobileState()}
                class={{
                  'mine-sub-aside-close-button relative px-4': true,
                }}
                onClick={() => setMobileSubmenuState(false)}
              >
                <ma-svg-icon name="material-symbols:close-rounded" />
              </div>
            </div>
          </div>
        </Transition>
      )
    }
  },
})
