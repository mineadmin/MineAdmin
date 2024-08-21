/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */

import type { Ref } from 'vue'
import type echarts from 'echarts'
import { useColorMode, useThrottleFn } from '@vueuse/core'

export default function useEcharts(el: Ref<HTMLElement>, options?: MineEChartOptions) {
  const echarts = useGlobal().$echarts
  const instance = shallowRef<echarts.ECharts | null>(null)
  const getInstance = (): echarts.ECharts | null => instance.value as echarts.ECharts

  const createEcharts = async () => {
    await nextTick()
    if (el.value) {
      instance.value = echarts.init(
        el.value,
        options?.theme ?? (useColorMode().value === 'dark' ? 'mineDark' : 'default'),
        options,
      )
    }
  }

  const setOption = (options: echarts.EChartsOption, params?: OptionsParams[]) => {
    watch(() => instance.value, () => {
      instance.value && instance.value.setOption(options)
      if (params && params?.length > 0 && instance.value) {
        params.map((item: OptionsParams) => {
          if (item?.type !== 'zrender' && typeof item?.callback == 'function') {
            instance.value?.on(item?.name as string, item?.query ?? '', (args: any) => {
              item?.callback(args)
            })
          }
          if (item?.type === 'zrender' && typeof item?.callback == 'function') {
            instance.value?.getZr().on(item?.name, (args: any) => {
              args.target || item?.callback(args)
            })
          }
        })
      }
    })
  }

  const getOption = () => instance.value && instance.value.getOption()

  const showLoading = (loadingOpts: LoadingOpts) => {
    const type = loadingOpts?.type ?? 'default'
    const opts = loadingOpts?.opts ?? {}
    if (instance.value) {
      instance.value.showLoading(type, opts)
    }
  }

  const hideLoading = () => instance.value && instance.value.hideLoading()

  const getDom = (): HTMLElement | undefined => {
    return instance.value?.getDom() ?? undefined
  }

  const resize = () => instance.value && instance.value.resize()

  const handleResize = useThrottleFn(() => {
    resize()
  }, 100)

  const clear = () => {
    if (instance.value) {
      instance.value.dispose()
      instance.value.clear()
      window.removeEventListener('resize', handleResize)
      instance.value = null
      el.value?.remove()
    }
  }

  const appendData = (opts: AppendDataOpts) => {
    instance.value && instance.value.appendData(opts)
  }

  const getWidth = (): number | undefined => {
    return instance.value?.getWidth() ?? undefined
  }

  const getHeight = (): number | undefined => {
    return instance?.value?.getHeight() ?? undefined
  }

  onMounted(async () => {
    await createEcharts()
    window.addEventListener('resize', handleResize)
  })

  onActivated(() => resize())

  return {
    echarts,
    getInstance,
    setOption,
    showLoading,
    hideLoading,
    clear,
    resize,
    appendData,
    getDom,
    getWidth,
    getHeight,
    getOption,
  }
}
