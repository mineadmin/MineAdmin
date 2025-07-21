/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import { useResizeObserver } from '@vueuse/core'
import useSettingStore from '@/store/modules/useSettingStore.ts'

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
function setupSubAsideListener(el: Ref<any>, settingStore: any) {
  const listener = (e: MouseEvent | TouchEvent) => {
    const target = e.target as HTMLElement
    const dom = el.value?.$el ?? el.value
    // eslint-disable-next-line style/max-statements-per-line
    if (!dom || !(target instanceof HTMLElement)) { return }

    // 点击的是菜单内部
    if (dom.contains(target)) {
      const tag = target.tagName.toLowerCase()
      if (['a', 'span'].includes(tag)) {
        setTimeout(() => {
          settingStore.setMobileSubmenuState(false)
        }, 100)
      }
      return
    }

    // 点击菜单外部，立即关闭
    settingStore.setMobileSubmenuState(false)
  }

  if (!listenerBound) {
    document.addEventListener('mousedown', listener, true) // 捕获阶段，防止冒泡后错过
    document.addEventListener('touchstart', listener, true)
    listenerBound = true
  }
}
