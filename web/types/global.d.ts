import type { ResultCode } from '@/utils/ResultCode.ts'
import type { App, Ref } from 'vue'
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { Router, RouteRecordName, RouteRecordRaw } from 'vue-router'

type RecursiveRequired<T> = {
  [P in keyof T]-?: RecursiveRequired<T[P]>
}
type RecursivePartial<T> = {
  [P in keyof T]?: RecursivePartial<T[P]>
}

type SFCModule<T> = {
  [P in keyof T]?: T
}

interface Dictionary {
  label?: string
  value?: any
  i18n?: string
  color?: string
  disabled?: boolean
  options?: Dictionary[]
}

interface PageList<T> {
  total: number
  list: T[]
}
interface ResponseStruct<T> {
  code: ResultCode
  message: string
  data: T
}

declare namespace Resources {

  interface Args {
    [key: string]: any
  }

  interface Button {
    name: string
    label: string
    icon: string
    click?: (btn: Resources.Button, selected: any[]) => void
    upload?: (files: FileList, args: Args) => void
    uploadConfig?: Record<string, any>
    order?: number
  }

  interface TypeSetting {
    name: string | string[]
    icon: string
    click?: (...args: any[]) => void
  }
}

declare namespace Plugin {

  /**
   * 插件基础信息
   */
  interface Info {
    name: string
    version: string
    author: string
    description: string
    order?: number
  }

  interface Config {
    /**
     * 插件信息
     */
    info: Info
    /**
     * 是否开启插件
     */
    enable: boolean
  }

  interface Views extends Route.RouteRecordRaw {}

  interface PluginConfig {
    install: (app: App) => void
    config: Config
    views?: Views[]
    /**
     * 插件hooks
     * 插件禁用时，定义的hook不会被触发
     */
    hooks?: {
      // 插件启动时触发，比如可以设置 enable = false，阻止插件启动
      start?: (config: Config) => any | void
      // 插件在系统初始化完毕后触发，可调用Vue的上下文、inject等服务
      setup?: () => any | void
      // 注册路由时触发，可以对路由进行所有操作
      registerRoute?: (router: Router, routesRaw: Route.RouteRecordRaw[] | Plugin.Views[] | MineRoute.routeRecord[]) => any | void
      // 登录前时触发，用于处理提交的数据
      loginBefore?: (data: Record<string, any>) => any | void
      // 登录时触发
      login?: (formInfo: any) => any | void
      // 退出登录时触发
      logout?: () => any | void
      // 获取用户信息时触发
      getUserInfo?: (userInfo: any) => any | void
      // 路由跳转时钩子，外链形式路由不生效
      routerRedirect?: ({ oldRoute: RouteRecordRaw, newRoute: RouteRecordRaw }, router: Router) => any | void
      // 网络请求时钩子
      networkRequest?: (request: T) => any | void
      // 网络返回后钩子
      networkResponse?: (response: T) => any | void
    }
    [key: string]: T
  }
}

declare namespace SystemSettings {
  type settingType = 'app' | 'welcomePage' | 'mainAside' | 'subAside' | 'tabbar' | 'toolBars' | 'copyright'
  // 应用属性
  interface app {
    /**
     * 颜色模式，light 白天 dark 黑夜，默认为 `'light'`
     * @默认值 `'light'`
     */
    colorMode?: 'light' | 'dark' | 'autoMode'
    /**
     * 主要颜色
     */
    primaryColor?: string
    /**
     * 侧边栏是否黑夜模式
     */
    asideDark?: boolean
    /**
     * 使用的语言包
     * @默认值 `'zh_CN'`
     */
    useLocale?: string
    /**
     * 白名单路由，白名单路由不进行权限校验
     * @默认值 `['login']`
     */
    whiteRoute?: string[]
    /**
     * 页面布局方式
     * @默认值 `'columns'`
     */
    layout?: 'columns' | 'classic' | 'mixed' | 'banner'
    /**
     * 页面过场动画
     * @默认值 `'ma-fade'`
     */
    pageAnimate?: 'ma-fade' | 'ma-slide-right' | 'ma-slide-left' | 'ma-slide-down' | 'ma-slide-up'
    /**
     * 是否显示面包屑
     * @默认值 `true`
     */
    showBreadcrumb?: boolean
    /**
     * 是否开启水印
     * @默认值 `false`
     */
    enableWatermark?: boolean
    /**
     *  是否加载用户设置
     *  @默认值 `true`
     */
    loadUserSetting?: boolean
    /**
     * 水印文字
     * @默认值 `'MineAdmin'`
     */
    watermarkText?: string | string[]
  }
  interface welcomePage {
    /**
     * 欢迎页默认地址
     * @默认值 `'/welcome'`
     */
    path?: string
    /**
     * 欢迎页路由名称
     * @默认值 `'welcome'`
     */
    name?: string
    /**
     * 欢迎页默认名称
     * @默认值 `'欢迎页'`
     */
    title?: string
    /**
     * 欢迎页图标
     */
    icon?: string
  }
  // 主菜单
  interface mainAside {
    /**
     * 是否显示图标
     * @默认值 `true`
     */
    showIcon?: boolean
    /**
     * 是否显示标题
     * @默认值 `true`
     */
    showTitle?: boolean
    /**
     * 是否开启默认打开第一个路由
     * @默认值 `true`
     */
    enableOpenFirstRoute?: boolean
  }
  // 子菜单设置
  interface subAside {
    /**
     * 是否显示图标
     * @默认值 `true`
     */
    showIcon?: boolean
    /**
     * 是否显示标题
     * @默认值 `true`
     */
    showTitle?: boolean
    /**
     * 固定子菜单状态
     * @默认值 `false`
     */
    fixedAsideState?: boolean
    /**
     * 是否显示折叠按钮
     * @默认值 `true`
     */
    showCollapseButton?: boolean
  }
  // 标签页
  interface tabbar {
    /**
     * 是否显示标签页
     * @默认值 `true`
     */
    enable?: boolean
    /**
     * 模式
     */
    mode: 'rectangle' | 'card'
  }
  // 工具栏
  interface toolBars {
    /**
     * 工具栏名称
     * @默认值
     */
    name?: string
    /**
     * 是否显示工具栏
     * @默认值 `true`
     */
    show?: boolean
  }
  // 版权信息属性
  interface copyright {
    /**
     * 是否开启底部版权，同时在路由 meta 对象里可以单独设置某个路由是否显示底部版权信息
     * @默认值 `true`
     */
    enable?: boolean
    /**
     * 网站运行日期
     * @默认值 `''`
     */
    dates?: string
    /**
     * 公司名称
     * @默认值 `''`
     */
    company?: string
    /**
     * 网站地址
     * @默认值 `''`
     */
    website?: string
    /**
     * 网站备案号
     * @默认值 `''`
     */
    putOnRecord?: string
  }

  interface all {
    /** 应用设置 */
    app: app
    /** 欢迎页设置 */
    welcomePage: welcomePage
    /** 主菜单设置 */
    mainAside: mainAside
    /** 子菜单设置 */
    subAside: subAside
    /** 标签页设置 */
    tabbar: tabbar
    /** 工具栏设置 */
    toolBars: toolBars[]
    /** 底部版权设置 */
    copyright: copyright
  }
}

interface MineToolbar {
  name: string
  icon: string
  title: string | (() => string)
  show: boolean
  className?: string | (() => string)
  component?: () => any
  handle?: (toolbar: MineToolbar) => any
}

interface MineToolbarExpose {
  state: Ref<boolean>
  defaultToolbars: Ref<MineToolbar[]>
  toolbars: Ref<MineToolbar[]>
  getShowToolbar: MineToolbar[]
  add: (toolbar: MineToolbar) => void
  remove: (name: string) => void
}

interface MineTabbar {
  name: string
  title: string
  path: string
  fullPath: string
  icon?: string
  i18n?: string
  affix?: boolean
}

declare namespace ProviderService {
  interface Provider {
    name: string
    init?: () => any | void
    setProvider: (app: App) => any | void
    getProvider: () => T
  }
}

declare namespace Route {
  interface RouteRecordRaw extends MineRoute.routeRecord {}
}

declare namespace MineRoute {
  interface routeRecord {
    name?: string
    path?: string
    redirect?: string
    expand?: boolean
    component?: () => Promise<any>
    components?: () => Promise<any>
    meta?: RouteMeta
    children?: routeRecord[]
  }
  interface RouteMeta {
    title?: string | (() => string)
    i18n?: string | (() => string)
    badge?: () => string | number
    icon?: string
    affix?: boolean
    hidden?: boolean
    subForceShow?: boolean
    copyright?: boolean
    link?: string
    breadcrumb?: routeRecord[]
    breadcrumbEnable?: boolean
    activeName?: string
    cache?: boolean
    type?: 'M' | 'B' | 'I' | 'L' | string
    // 权限验证配置
    auth?: string[]
    role?: string[]
    user?: string[]
  }
}

declare namespace Tag {
  interface recordRaw {
    tagId: string
    fullPath: string
    routeName?: RouteRecordName | null
    title?: string | (() => string)
    icon?: string
    name: string[]
  }
}
