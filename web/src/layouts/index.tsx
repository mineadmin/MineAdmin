/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import { KeepAlive, Transition } from 'vue'
import { RouterView } from 'vue-router'
import MineHeader from './components/header'
import MineSearchPanel from './components/search-panel'
import MineMainAside from './components/main-aside'
import MineSubAside from './components/sub-aside'
import MineBars from './components/bars'
import MineFooter from './components/footer'
import MineBackTop from './components/back-top'
import MineIframe from './components/iframe'
import '@/layouts/style/index.scss'
import type { SystemSettings } from '#/global'
import handleResize from '@/utils/handleResize'

export default defineComponent({
  name: 'MineContainer',
  setup() {
    const {
      getSettings,
      showMineHeader,
      isMixedLayout,
      isClassicLayout,
      isBannerLayout,
      openGlobalWatermark,
      clearGlobalWatermark,
      getSearchPanelEnable,
      getMobileState,
    } = useSettingStore()

    const subAsideEl = ref()
    const keepAliveStore = useKeepAliveStore()
    const appSetting = getSettings('app') as SystemSettings.app
    const menuStore = useMenuStore()
    const route = useRoute()

    watch(() => appSetting.enableWatermark, (v: boolean | undefined) => {
      v && openGlobalWatermark()
      v || clearGlobalWatermark()
    }, { immediate: true })

    onMounted(() => {
      if (menuStore.subMenu.length > 0 && appSetting?.layout === 'columns') {
        menuStore.setSubAsideWidthByDefault()
      }
      else if (menuStore.subMenu.length === 0 && appSetting?.layout === 'mixed') {
        menuStore.setSubAsideWidthByZero()
      }
      else if (appSetting?.layout === 'columns') {
        menuStore.setSubAsideWidthByZero()
      }
      else {
        menuStore.setSubAsideWidthByDefault()
      }
      handleResize(subAsideEl)
    })

    return () => (
      <div class="app-container">
        <MineHeader />
        <div class={{
          'mine-wrapper': true,
          'mine-wrapper-full': !showMineHeader() || getMobileState(),
          'mine-wrapper-not-full': showMineHeader() && !getMobileState(),
        }}
        >
          <Transition name="mine-aside-animate">
            <div class={{ 'group mine-aside': true, 'w-0': getMobileState() }} v-show={!isBannerLayout()}>
              {(!isClassicLayout() && !isMixedLayout()) && <MineMainAside />}
              <MineSubAside ref={subAsideEl} />
            </div>
          </Transition>
          <div class="mine-main">
            <MineBars />
            <div class="mine-worker-area">
              <RouterView class="router-view">
                {({ Component }) => (
                  <Transition name={appSetting.pageAnimate} mode="out-in">
                    <KeepAlive include={keepAliveStore.list}>
                      {(keepAliveStore.getShowState() && route.meta.type !== 'I') && <Component key={route.fullPath} />}
                    </KeepAlive>
                  </Transition>
                )}

              </RouterView>
            </div>
            <div class="mine-iframe-area" v-show={route.meta?.type === 'I'}>
              <MineIframe />
            </div>
            <MineFooter />
            <MineBackTop />
          </div>

        </div>
        <div class="mine-max-size-exit" onClick={() => useTabStore().exitMaxSizeTab()}>
          <ma-svg-icon name="i-material-symbols:close" size={50} />
        </div>

        <MineSearchPanel v-show={getSearchPanelEnable()} />
      </div>
    )
  },
})
