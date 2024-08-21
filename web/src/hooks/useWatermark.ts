/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { Fn } from '@vueuse/core'
import { useColorMode } from '@vueuse/core'

const domSymbol = Symbol('watermark-dom')

export default function useWatermark(appendEl: HTMLElement | null = document.body) {
  const func: Fn = () => {}
  const id = domSymbol.toString()
  const clear = () => {
    const domId = document.getElementById(id)
    if (domId) {
      const el = appendEl

      el && el.removeChild(domId)
    }
    window.removeEventListener('resize', func)
  }
  const createWatermark = (str: string | string[]) => {
    clear()

    const can = document.createElement('canvas')
    can.width = 240
    can.height = 100

    const cans = can.getContext('2d')
    if (cans) {
      cans.rotate(-10 * Math.PI / 140)
      cans.font = 'normal 16px Arial, \'Courier New\', \'Droid Sans\', sans-serif'
      cans.fillStyle = useColorMode().value === 'dark' ? 'rgba(255, 255, 255, 0.15)' : 'rgba(0, 0, 0, 0.15)'
      cans.textAlign = 'center'
      cans.textBaseline = 'ideographic'
      if (typeof str === 'string') {
        cans.fillText(str, can.width / 2, can.height)
      }
      else {
        for (let i = 0; i < str.length; i++) {
          cans.fillText(str[i], can.width / 2, can.height + (i * 20))
        }
      }
    }

    const div = document.createElement('div')
    div.id = id
    div.style.pointerEvents = 'none'
    div.style.top = '-240px'
    div.style.left = '-300px'
    div.style.position = 'fixed'
    div.style.zIndex = '100000000'
    div.style.width = `${document.documentElement.clientWidth + 300}px`
    div.style.height = `${document.documentElement.clientHeight + 300}px`
    div.style.background = `url(${can.toDataURL('image/png')}) left top repeat`
    const el = appendEl

    el && el.appendChild(div)
    return id
  }

  function setWatermark(str: string | string[]) {
    createWatermark(str)
    window.addEventListener('resize', () => createWatermark(str))
  }

  return { setWatermark, clear }
}
