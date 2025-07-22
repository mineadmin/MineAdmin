/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { Ref } from 'vue'
import { useMouseInElement, useResizeObserver } from '@vueuse/core'
import useSettingStore from '@/store/modules/useSettingStore.ts'

// 用于记录监听控制函数
let stopMouseListener: (() => void) | null = null
let startMouseListener: (() => void) | null = null

/**
 * 自定义事件监听组合式函数
 */
function useEventListener<T extends Event>(
  target: Ref<HTMLElement | Document | Window | null> | HTMLElement | Document | Window,
  event: string,
  handler: (e: T) => void,
) {
  let isListening = false
  const getTarget = () => (target as Ref<any>).value ?? target

  const startListening = () => {
    const el = getTarget()
    if (el && !isListening) {
      el.addEventListener(event, handler as EventListener)
      isListening = true
    }
  }

  const stopListening = () => {
    const el = getTarget()
    if (el && isListening) {
      el.removeEventListener(event, handler as EventListener)
      isListening = false
    }
  }

  onUnmounted(stopListening)

  return { startListening, stopListening }
}

function isMobileDevice() {
  return /Android|iPhone|iPad|iPod|Mobile/i.test(navigator.userAgent)
}

/**
 * 主函数，监听尺寸变化并处理事件绑定
 */
export default function handleResize(el: Ref<HTMLElement>) {
  const { setMobileState, setMobileSubmenuState } = useSettingStore()

  useResizeObserver(document.body, (entries) => {
    const [entry] = entries
    const { width, height } = entry.contentRect

    const searchPanelNode = document.querySelector('.mine-search-panel-container') as HTMLElement
    if (searchPanelNode) {
      searchPanelNode.style.top
        = height < 500
        ? '0px'
        : height < 800
          ? 'calc(100% - 50% - 250px)'
          : 'calc(100% - 50% - 350px)'
    }

    // 设置是否是移动状态
    const isMobile = width < 1024
    setMobileState(isMobile)
    setMobileSubmenuState(!isMobile)

    // 清理并重建监听
    checkMobileSubAside(el)
  })
}

/**
 * 判断是否点击在外部并根据设备类型动态绑定 mousemove
 */
function checkMobileSubAside(el: Ref<HTMLElement>) {
  const { isOutside } = useMouseInElement(el)
  const settingStore = useSettingStore()

  // 清理旧的监听器
  stopMouseListener?.()
  stopMouseListener = null
  startMouseListener = null

  const handleMouseMove = () => {
    if (settingStore.getMobileSubmenuState() && isOutside.value) {
      settingStore.setMobileSubmenuState(false)
    }
  }

  const { startListening, stopListening } = useEventListener<MouseEvent>(
    window,
    'mousemove',
    handleMouseMove,
  )

  // 缓存控制函数
  startMouseListener = startListening
  stopMouseListener = stopListening

  const isMobile = isMobileDevice() && window.innerWidth < 1024
  isMobile ? startMouseListener() : stopMouseListener()
}
