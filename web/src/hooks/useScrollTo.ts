/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
export interface ScrollToParams {
  el: HTMLElement
  to: number
  position: string
  duration?: number
  callback?: () => void
}

function easeInOutQuad(t: number, b: number, c: number, d: number) {
  t /= d / 2
  if (t < 1) {
    return (c / 2) * t * t + b
  }
  t--
  return (-c / 2) * (t * (t - 2) - 1) + b
}
function move(el: HTMLElement, position: string, amount: number) {
  el[position] = amount
}

export default function useScrollTo({
  el,
  position = 'scrollLeft',
  to,
  duration = 500,
  callback,
}: ScrollToParams) {
  const isActiveRef = ref(false)
  const start = el[position]
  const change = to - start
  const increment = 20
  let currentTime = 0

  function animateScroll() {
    if (!unref(isActiveRef)) {
      return
    }
    currentTime += increment
    const val = easeInOutQuad(currentTime, start, change, duration)
    move(el, position, val)
    if (currentTime < duration && unref(isActiveRef)) {
      requestAnimationFrame(animateScroll)
    }
    else {
      if (callback) {
        callback()
      }
    }
  }

  function run() {
    isActiveRef.value = true
    animateScroll()
  }

  function stop() {
    isActiveRef.value = false
  }

  return { start: run, stop }
}
