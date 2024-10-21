/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import { useColorMode, useCssVar } from '@vueuse/core'

export default function useThemeColor() {
  function setThemeColor(color: string) {
    useCssVar('--ui-primary', document.documentElement).value = hexToRgb(color).join(' ')
    useCssVar('--el-color-primary', document.documentElement).value = color

    const colorMode = useColorMode()
    // 浅色
    for (let i = 1; i <= 9; i++) {
      useCssVar(`--el-color-primary-light-${i}`, document.documentElement).value = (
        colorMode.value === 'dark' ? darken(color, i / 10) : lighten(color, i / 10)
      )
    }

    // 暗色
    for (let i = 1; i <= 9; i++) {
      useCssVar(`--el-color-primary-dark-${i}`, document.documentElement).value = darken(color, i / 10)
    }

    if (colorMode.value === 'dark') {
      useSettingStore().setAsideDark(false)
    }
  }

  function initThemeColor() {
    setThemeColor(useSettingStore().getSettings('app').primaryColor)
    useSettingStore().setAsideDark(useSettingStore().getSettings('app').asideDark)
  }

  function hexToRgb(str: string): number[] {
    str = str.replace('#', '')
    const hxs = str.match(/../g) as any
    for (let i = 0; i < 3; i++) {
      hxs[i] = Number.parseInt(hxs[i].toString(), 16)
    }
    return hxs
  }

  function rgbToHex(a: number, b: number, c: number): string {
    const hex = [a.toString(16), b.toString(16), c.toString(16)]
    for (let i = 0; i < 3; i++) {
      if (hex[i].length === 1) {
        hex[i] = `0${hex[i]}`
      }
    }
    return `#${hex.join('')}`
  }

  function darken(color: string, level: number): string {
    const rgb = hexToRgb(color)
    for (let i = 0; i < 3; i++) {
      rgb[i] = Math.round(20.5 * level + rgb[i] * (1 - level))
    }
    return rgbToHex(rgb[0], rgb[1], rgb[2])
  }

  function lighten(color: string, level: number): string {
    const rgb = hexToRgb(color)
    for (let i = 0; i < 3; i++) {
      rgb[i] = Math.round(255 * level + rgb[i] * (1 - level))
    }
    return rgbToHex(rgb[0], rgb[1], rgb[2])
  }

  return {
    initThemeColor,
    setThemeColor,
    hexToRgb,
    rgbToHex,
    darken,
    lighten,
  }
}
