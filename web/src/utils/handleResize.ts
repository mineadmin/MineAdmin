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
import type { Ref } from 'vue'

const listenerMap = new WeakMap<HTMLElement, (e: MouseEvent) => void>()

export default function handleResize(el: Ref<HTMLElement>) {
  const { setMobileState, setMobileSubmenuState } = useSettingStore()
  useResizeObserver(document.body, (entries) => {
    const [entry] = entries
    const { width, height } = entry.contentRect
    const searchPanelNode = document.querySelector('.mine-search-panel-container') as HTMLElement
    searchPanelNode.style.top = height < 500 ? '0px' : height < 800 ? 'calc(100% - 50% - 250px)' : 'calc(100% - 50% - 350px)'
    setMobileState(width < 1024)
    setMobileSubmenuState(!(width < 1024))
    checkMobileSubAside(el)
  })
}

function isMobileDevice() {
  return /Android|iPhone|iPad|iPod|Mobile/i.test(navigator.userAgent)
}

function checkMobileSubAside(el: Ref<HTMLElement>) {
  const { isOutside } = useMouseInElement(el)
  const settingStore = useSettingStore()
  const targetEl = el.value

  // 如果之前绑定过，先移除
  const prevListener = listenerMap.get(targetEl)
  if (prevListener) {
    document.removeEventListener('mousemove', prevListener)
    listenerMap.delete(targetEl)
  }

  if (isMobileDevice() && window.innerWidth < 1024) {
    const listenerEvent = () => {
      if (settingStore.getMobileSubmenuState() && isOutside.value) {
        settingStore.setMobileSubmenuState(false)
      }
    }

    document.addEventListener('mousemove', listenerEvent)
    listenerMap.set(targetEl, listenerEvent)
  }
}
