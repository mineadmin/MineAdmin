/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type {AxiosInstance, AxiosResponse} from 'axios'
import axios from 'axios'
import Message from 'vue-m-message'
import {useDebounceFn} from '@vueuse/core'
import {useNProgress} from '@vueuse/integrations/useNProgress'
import type {ResponseStruct} from '#/global'
import useCache from '@/hooks/useCache.ts'
import {ResultCode} from "#/ResultCode.ts";

const { isLoading } = useNProgress()
const cache = useCache()

function createHttp(baseUrl: string | null = null): AxiosInstance {
  const env = import.meta.env
  return axios.create({
    baseURL: baseUrl ?? (env.VITE_OPEN_PROXY === 'true' ? env.VITE_PROXY_PREFIX : env.VITE_APP_API_BASEURL),
    timeout: 1000 * 60,
    responseType: 'json',
  })
}

const http:AxiosInstance = createHttp()

http.interceptors.request.use(

  async (config) => {
    isLoading.value = true
    const userStore = useUserStore()
    /**
     * 全局拦截请求发送前提交的参数
     */
    if (userStore.isLogin && config.headers) {
      config.headers = Object.assign({
        'Authorization': `Bearer ${userStore.token}`,
        'Accept-Language': userStore.getLanguage(),
      }, config.headers)
    }

    // 检查token是否需要刷新
    if (userStore.isLogin && (Number(cache.get('expire', 0)) - useDayjs().unix()) < 600) {
      console.log('需要刷新token了')
    }

    await usePluginStore().callHooks('networkRequest', config)
    return config
  },
)

http.interceptors.response.use(
  async (response: AxiosResponse): Promise<any> => {
    isLoading.value = false
    await usePluginStore().callHooks('networkResponse', response)
    if ((response.headers['content-disposition'] || !/^application\/json/.test(response.headers['content-type'])) && response.status === 200) {
      return Promise.resolve(response.data)
    }

    const responseRaw: ResponseStruct = response.data
    const mineResponse = { raw: response, data: responseRaw?.data ?? null }
    if (response.status === 200) {
      if (responseRaw.code !== ResultCode.SUCCESS){
        Message.error(responseRaw.message, {
          zIndex: 2000,
        })
        return Promise.reject(responseRaw)
      }
      return Promise.resolve(responseRaw)
    } else {
      Message.error(responseRaw.message, {
        zIndex: 2000,
      })
      return Promise.reject(responseRaw)
    }
  },
  async (error) => {
    isLoading.value = false
    switch (error.response.status) {
      case 401:
        Message.error('登录状态已过期，需要重新登录', { zIndex: 2000 })
        await (useDebounceFn(
          () => useUserStore().logout(),
          1000,
          { maxWait: 5000 },
        ))()
        break
      case 404:
        Message.error('服务器资源不存在', { zIndex: 2000 })
        break
      case 500:
        Message.error('服务器内部错误', { zIndex: 2000 })
        break
      default:
        Message.error(error.message ?? '未知错误', { zIndex: 2000 })
        break
    }

    return Promise.reject(error.response && error.response.data ? error.response.data : null)
  },
)

export default {
  http,
  createHttp,
}
