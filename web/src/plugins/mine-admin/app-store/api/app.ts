/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */

const http = useHttp()

interface versionType {
  preview_url?: string
  require?: string
  update_log?: string
  version?: string
  version_desc?: string
  created_at?: string
}

export interface AppVo {
  id?: number
  name?: string
  username?: string
  identifier?: string
  avatar?: string
  homepage?: string[]
  space?: string
  app?: Record<string, any>
  tags?: {
    name?: string
    color?: string
  }[]
  updated_at?: string
  auth?: {
    type?: number
    advance_quota?: string
    basic_quota?: string
    integral_discount?: string
    integral_quota?: string
    advance_discount?: string
    basic_discount?: string
  }
  created_at?: string
  created_by?: string | Record<string, any>
  description?: string
  version?: versionType[]
  versions?: versionType[]
}

/**
 * 检查是.env 是否设置了 access_token
 */
export function hasAccessToken() {
  return http.get('/admin/plugin/store/hasAccessToken')
}

/**
 * 请求应用列表
 */
export function getAppList(params: Record<string, string>) {
  return http.get('/admin/plugin/store/index', { params })
}

/**
 * 已购买应用
 */
export function getPayApp() {
  return http.get('/admin/plugin/store/getPayApp')
}

/**
 * 本地应用安装状态
 */
export function getLocalAppInstallList() {
  return http.get('/admin/plugin/store/getLocalAppInstallList')
}

/**
 * 详情
 */
export function getDetail(params: Record<string, string>) {
  return http.get('/admin/plugin/store/detail', { params })
}

/**
 * 下载应用
 */
export function download(data: any) {
  return http.post(
    '/admin/plugin/store/download',
    data,
    {
      timeout: 500000,
    },
  )
}

export function uploadLocalApp(formData: FormData) {
  return http.post('/admin/plugin/store/uploadLocalApp', formData)
}

/**
 * 安装应用
 */
export function install(data: any) {
  return http.post('/admin/plugin/store/install', data)
}

/**
 * 安装应用
 */
export function unInstall(data: any) {
  return http.post('/admin/plugin/store/unInstall', data)
}
