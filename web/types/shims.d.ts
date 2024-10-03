/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
declare interface Window {
  webkitDevicePixelRatio: any
  mozDevicePixelRatio: any
}

declare namespace JSX {
  interface Element extends VNode {}
  interface ElementClass extends Vue {}
  interface IntrinsicElements {
    [elem: string]: any
  }
}

declare const __MINE_SYSTEM_INFO__: {
  pkg: {
    version: Recordable<string>
    dependencies: Recordable<string>
    devDependencies: Recordable<string>
  }
  lastBuildTime: string
}
