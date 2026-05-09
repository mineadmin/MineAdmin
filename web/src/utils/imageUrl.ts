/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */

/**
 * 判断 URL 是否为完整 URL（包含协议和域名）
 */
export function isFullUrl(url: string | undefined | null): boolean {
  if (!url) return false
  return /^https?:\/\//i.test(url)
}

/**
 * 获取 API 基础域名
 * 从环境变量中获取 API 基础地址
 */
export function getApiBaseUrl(): string {
  const env = import.meta.env
  return env.VITE_APP_API_BASEURL || ''
}

/**
 * 智能处理图片 URL
 * - 如果 URL 已经是完整 URL（带协议和域名），直接返回
 * - 如果是相对路径，则拼接 API 基础域名
 *
 * @param url 图片 URL（可以是完整 URL 或相对路径）
 * @param defaultBaseUrl 可选的默认基础域名（当环境变量未配置时使用）
 */
export function getImageUrl(url: string | undefined | null, defaultBaseUrl?: string): string {
  // 空值处理
  if (!url) return ''

  // 去除空白字符
  const trimmedUrl = url.trim()
  if (!trimmedUrl) return ''

  // 如果是完整 URL，直接返回
  if (isFullUrl(trimmedUrl)) {
    return trimmedUrl
  }

  // 处理相对路径
  let baseUrl = getApiBaseUrl() || defaultBaseUrl || ''

  // 确保 baseUrl 不以斜杠结尾
  if (baseUrl && !baseUrl.endsWith('/')) {
    baseUrl += '/'
  }

  // 去除 URL 开头的斜杠，避免双斜杠
  const cleanPath = trimmedUrl.startsWith('/') ? trimmedUrl.substring(1) : trimmedUrl

  return baseUrl + cleanPath
}

/**
 * 批量处理图片 URL 数组
 * @param urls 图片 URL 数组
 * @param defaultBaseUrl 可选的默认基础域名
 */
export function getImageUrls(urls: (string | undefined | null)[] | undefined, defaultBaseUrl?: string): string[] {
  if (!urls || !Array.isArray(urls)) return []
  return urls.map(url => getImageUrl(url, defaultBaseUrl))
}
