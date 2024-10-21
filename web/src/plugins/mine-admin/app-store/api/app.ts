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
  return http.get('/admin/plugin/store/index', params)
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
  return http.get('plugin/store/detail', params)
}

/**
 * 下载应用
 */
export function download(data: any) {
  return http.post(
    '/admin/plugin/store/download',
    {
      timeout: 500000,
      data,
    },
  )
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
