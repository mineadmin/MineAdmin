/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import { useColorMode } from '@vueuse/core'
import type { Ref } from 'vue'
import type { SystemSettings } from '#/global'
import useWatermark from '@/hooks/useWatermark'
import useThemeColor from '@/hooks/useThemeColor.ts'

const useSettingStore = defineStore(
  'useSettingStore',
  () => {
    type settingType = SystemSettings.settingType | null
    const title = ref<string>('')
    const defaultSetting = ref<SystemSettings.all>(useDefaultSetting())
    const colorMode: Ref<string> = useColorMode()
    const searchPanelEnable = ref<boolean>(false)
    const menuCollapseState = ref<boolean>(false)
    const { setWatermark, clear } = useWatermark()
    const isMobile = ref<boolean>(false)
    const mobileMenuState = ref<boolean>(false)
    const userBarState = ref<boolean>(false)

    function showMineHeader() {
      return ['mixed', 'banner'].includes(defaultSetting.value.app?.layout as string)
    }

    function showMineSubAside() {
      return ['classic', 'mixed'].includes(defaultSetting.value.app?.layout as string)
    }

    function setUserBarState(state: boolean) {
      userBarState.value = state
    }

    function getUserBarState() {
      return userBarState.value
    }

    function setMobileState(state: boolean) {
      isMobile.value = state
    }

    function getMobileState() {
      return isMobile.value
    }

    function setMobileSubmenuState(state: boolean) {
      mobileMenuState.value = state
    }

    function getMobileSubmenuState() {
      return mobileMenuState.value
    }

    function isColumnsLayout() {
      return defaultSetting.value.app?.layout === 'columns'
    }

    function isClassicLayout() {
      return defaultSetting.value.app?.layout === 'classic'
    }

    function isMixedLayout() {
      return defaultSetting.value.app?.layout === 'mixed'
    }

    function isBannerLayout() {
      return defaultSetting.value.app?.layout === 'banner'
    }

    function getFixedAsideState() {
      return defaultSetting.value.subAside?.fixedAsideState
    }

    function setFixedAsideState(state: boolean) {
      return defaultSetting.value.subAside.fixedAsideState = state
    }

    function setToolBar(name: string, show: boolean) {
      defaultSetting.value.toolBars = defaultSetting.value.toolBars || [] // 初始化为空数组

      const existingToolBar = defaultSetting.value.toolBars.find(item => item.name === name)

      if (existingToolBar) {
        existingToolBar.show = show // 更新 show 值
      }
      else {
        defaultSetting.value.toolBars.push({ name, show }) // 添加新的工具栏项
      }
    }

    function getMenuCollapseState() {
      return menuCollapseState.value
    }
    function setMenuCollapseState(state: boolean) {
      return menuCollapseState.value = state
    }

    function toggleCollapseButton() {
      menuCollapseState.value = !menuCollapseState.value
      setSubAsideWidth(menuCollapseState.value ? 'var(--mine-g-sub-aside-collapse-width)' : 'var(--mine-g-sub-aside-width)')
    }

    function toggleFixedSubAsideButton() {
      defaultSetting.value.subAside.fixedAsideState = !defaultSetting.value.subAside.fixedAsideState
    }

    function setHeaderHeight(height: string) {
      const mineHeaderDom: HTMLElement | null = document.querySelector('.mine-header-main')
      if (mineHeaderDom) {
        mineHeaderDom.style.height = height
      }
    }

    function setMainAsideWidth(width: string) {
      const mineMainAsideDom: HTMLElement | null = document.querySelector('.mine-main-aside-content')
      if (mineMainAsideDom) {
        mineMainAsideDom.style.width = width
      }
    }

    function setSubAsideWidth(width: string) {
      const mineSubAsideDom: HTMLElement | null = document.querySelector('.mine-sub-aside')
      if (mineSubAsideDom) {
        mineSubAsideDom.style.width = width
      }
    }

    function getSettings(type: settingType = null): any {
      if (defaultSetting.value) {
        return type === null ? defaultSetting.value : defaultSetting.value[type]
      }
      else {
        return null
      }
    }

    function setSettings(setting: any, type: settingType = null) {
      if (type === null) {
        defaultSetting.value = setting
      }
      else {
        defaultSetting.value[type as string] = setting
      }
    }

    function initColorMode() {
      if (defaultSetting.value?.app?.colorMode === 'autoMode') {
        if (Number(useDayjs().format('HH')) > 8 && Number(useDayjs().format('HH')) < 18) {
          colorMode.value = 'autoMode'
        }
        else {
          colorMode.value = 'dark'
        }
      }
      else {
        colorMode.value = defaultSetting.value?.app?.colorMode ?? 'light'
      }
      // useThemeColor().initThemeColor()
    }

    async function toggleColorMode(modeText: 'light' | 'dark' | 'autoMode' | null = null) {
      if (modeText === null) {
        if (colorMode.value === 'light') {
          colorMode.value = 'dark'
        }
        else if (colorMode.value === 'dark') {
          colorMode.value = 'autoMode'
        }
        else if (colorMode.value === 'autoMode') {
          colorMode.value = 'light'
        }
      }
      else {
        colorMode.value = modeText
      }

      // await useTabStore().refreshTab()
      defaultSetting.value.app.colorMode = colorMode.value as 'light' | 'dark' | 'autoMode'
      await nextTick(() => {
        useThemeColor().initThemeColor()
        defaultSetting.value.app.enableWatermark && openGlobalWatermark()
      })
    }

    function openGlobalWatermark(str: string | string[] | null = null) {
      setWatermark((str ?? defaultSetting.value?.app?.watermarkText) as string | string[])
    }

    function clearGlobalWatermark() {
      clear()
    }

    function setSearchPanelEnable(state: boolean) {
      searchPanelEnable.value = state
    }

    function getSearchPanelEnable() {
      return searchPanelEnable.value
    }

    function getAsideDark() {
      return defaultSetting.value.app.asideDark
    }

    function setAsideDark(state: boolean): boolean {
      state ? document.body.classList.add('mine-aside-dark') : document.body.classList.remove('mine-aside-dark')
      return defaultSetting.value.app.asideDark = state
    }

    // 设置网页标题
    function setTitle(routeTitle: string) {
      title.value = `${import.meta.env.VITE_APP_TITLE} - ${routeTitle}`
      document.title = title.value
    }

    initColorMode()
    return {
      title,
      colorMode,
      initColorMode,
      setUserBarState,
      getUserBarState,
      setMobileState,
      getMobileState,
      setMobileSubmenuState,
      getMobileSubmenuState,
      setSearchPanelEnable,
      getSearchPanelEnable,
      showMineHeader,
      showMineSubAside,
      isColumnsLayout,
      isClassicLayout,
      isMixedLayout,
      isBannerLayout,
      getFixedAsideState,
      setFixedAsideState,
        setToolBar,
      getMenuCollapseState,
      setMenuCollapseState,
      toggleCollapseButton,
      toggleFixedSubAsideButton,
      setTitle,
      setSubAsideWidth,
      setMainAsideWidth,
      setHeaderHeight,
      toggleColorMode,
      getSettings,
      setSettings,
      getAsideDark,
      setAsideDark,
      openGlobalWatermark,
      clearGlobalWatermark,
    }
  },
)

export default useSettingStore
