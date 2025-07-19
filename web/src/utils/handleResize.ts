/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import { useMouseInElement, useResizeObserver } from '@vueuse/core'

let listenerBound = false

export default function handleResize(el: Ref<HTMLElement>) {
  const settingStore = useSettingStore()

  useResizeObserver(document.body, (entries) => {
    const { width, height } = entries[0].contentRect

    updateSearchPanelTop(height)

    const isSmallScreen = width < 1024
    isMobileDevice()
    settingStore.setMobileState(isSmallScreen)
    settingStore.setMobileSubmenuState(!isSmallScreen)

    setupSubAsideListener(el, settingStore)
  })
}

/**
 * 设置搜索面板位置
 */
function updateSearchPanelTop(height: number) {
  const searchPanel = document.querySelector('.mine-search-panel-container') as HTMLElement | null
  // eslint-disable-next-line style/max-statements-per-line
  if (!searchPanel) { return }

  let top = '0px'
  if (height >= 800) {
    top = 'calc(100% - 50% - 350px)'
  }
  else if (height >= 500) {
    top = 'calc(100% - 50% - 250px)'
  }
  searchPanel.style.top = top
}

/**
 * 更全面的移动端判断：触摸设备 + UA
 */
function isMobileDevice(): boolean {
  return (
    window.matchMedia('(pointer: coarse)').matches
    || /android|iphone|ipad|ipod|mobile|tablet/i.test(navigator.userAgent)
  )
}

/**
 * 子菜单区域监听 - 始终开启监听，避免多端逻辑错乱
 */
function setupSubAsideListener(el: Ref<HTMLElement>, settingStore: any) {
  const { isOutside } = useMouseInElement(el)

  const listener = () => {
    if (settingStore.getMobileSubmenuState() && isOutside.value) {
      settingStore.setMobileSubmenuState(false)
    }
  }

  if (!listenerBound) {
    document.addEventListener('mousedown', listener)
    document.addEventListener('touchstart', listener)
    listenerBound = true
  }
}
