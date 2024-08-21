/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
type Theme = 'default' | 'light' | 'mineDark' | string
declare type ElementEventType = 'echarts' | 'zrender'
declare type ElementEventName = 'click' | 'dblclick' | 'mousewheel' | 'mouseout' | 'mouseover' | 'mouseup' | 'mousedown' | 'mousemove' | 'contextmenu' | 'drag' | 'dragstart' | 'dragend' | 'dragenter' | 'dragleave' | 'dragover' | 'drop' | 'globalout' | 'selectchanged' | 'highlight' | 'downplay' | 'legendselectchanged' | 'legendselected' | 'legendunselected' | 'legendselectall' | 'legendinverseselect' | 'legendscroll' | 'datazoom' | 'datarangeselected' | 'graphroam' | 'georoam' | 'treeroam' | 'timelinechanged' | 'timelineplaychanged' | 'restore' | 'dataviewchanged' | 'magictypechanged' | 'geoselectchanged' | 'geoselected' | 'geounselected' | 'axisareaselected' | 'brush' | 'brushEnd' | 'brushselected' | 'globalcursortaken' | 'rendered' | 'finished'
interface Fn<T = any, R = T> {
  (...arg: T[]): R
}
interface RefValue<T = any> {
  value: T
}
interface ComputedRefValue<T = any> extends WritableComputedRef<T> {
  readonly value: T
}
interface MineEChartOptions {
  /** 主题色（可选`default`、`light`、`mineDark`，也可以 [自定义主题](https://echarts.apache.org/zh/theme-builder.html) ，默认`default`） */
  theme?: Theme | RefValue<Theme> | ComputedRefValue<Theme>
  /** 设备像素比，默认取浏览器的值`window.devicePixelRatio` */
  devicePixelRatio?: number
  /** 渲染模式，支持`canvas`或者`svg`，默认`canvas` */
  renderer?: 'canvas' | 'svg'
  /** 是否使用服务端渲染，只有在`SVG`渲染模式有效。开启后不再会每帧自动渲染，必须要调用 [renderToSVGString](https://echarts.apache.org/zh/api.html#echartsInstance.renderToSVGString) 方法才能得到渲染后`SVG`字符串 */
  ssr?: boolean
  /** 是否开启脏矩形渲染，只有在`Canvas`渲染模式有效，默认为`false` */
  useDirtyRect?: boolean
  /** 是否扩大可点击元素的响应范围。`null`表示对移动设备开启；`true`表示总是开启；`false`表示总是不开启 */
  useCoarsePointer?: boolean
  /** 扩大元素响应范围的像素大小，配合`opts.useCoarsePointer`使用 */
  pointerSize?: number
  /** 可显式指定实例宽度，单位为像素。如果传入值为`null/undefined/'auto'`，则表示自动取`dom`（实例容器）的宽度 */
  width?: string
  /** 可显式指定实例高度，单位为像素。如果传入值为`null/undefined/'auto'`，则表示自动取`dom`（实例容器）的高度 */
  height?: string
  /** 使用的语言，内置`ZH`和`EN`两个语言，也可以使用 [echarts.registerLocale](https://echarts.apache.org/zh/api.html#echarts.registerLocale) 方法注册新的语言包。目前支持的语言见 [src/i18n](https://github.com/apache/echarts/tree/release/src/i18n) */
  locale?: string
}
interface OptionsParams {
  /** 事件类型名称 `必传` */
  name: ElementEventName
  /** 回调函数，返回 [params](https://echarts.apache.org/zh/api.html#events.%E9%BC%A0%E6%A0%87%E4%BA%8B%E4%BB%B6) 参数 `必传` */
  callback: Fn
  /** `echarts`事件（默认）或 [zrender](https://echarts.apache.org/handbook/zh/concepts/event/#%E7%9B%91%E5%90%AC%E2%80%9C%E7%A9%BA%E7%99%BD%E5%A4%84%E2%80%9D%E7%9A%84%E4%BA%8B%E4%BB%B6) 事件 */
  type?: ElementEventType
  /** `query`属性，点击 [此处](https://echarts.apache.org/handbook/zh/concepts/event/#%E9%BC%A0%E6%A0%87%E4%BA%8B%E4%BB%B6%E7%9A%84%E5%A4%84%E7%90%86) 搜索`query`进行了解 可选 */
  query?: string | object
}
interface AppendDataOpts {
  seriesIndex: number
  data: any
}
interface LoadingOpts {
  type?: string
  opts?: {
    text?: string
    color?: string
    textColor?: string
    maskColor?: string
    zlevel?: number
    /**
     * 字体大小
     */
    fontSize?: number
    /**
     * 是否显示旋转动画（spinner）
     */
    showSpinner?: boolean
    /**
     * 旋转动画（spinner）的半径
     */
    spinnerRadius?: number
    /**
     * 旋转动画（spinner）的线宽
     */
    lineWidth?: number
    /**
     * 字体粗细
     */
    fontWeight?: string
    /**
     * 字体风格
     */
    fontStyle?: string
    /**
     * 字体系列
     */
    fontFamily?: string
  }
}
