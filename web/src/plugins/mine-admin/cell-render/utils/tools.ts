import type { ComputedRef } from 'vue'
import { get } from 'lodash-es'

interface Props {
  scope: Record<string, any>
  options: Record<string, any>
}

interface RowFieldValues {
  row: ComputedRef<any>
  field: ComputedRef<string>
  value: ComputedRef<any>
}

export type WithOnEventListeners<T> = {
  [K in keyof T as `on${Capitalize<string & K>}`]?: T[K];
}

export function createRowFieldValues(props: Props): RowFieldValues {
  const row = computed({
    get() {
      return props.scope.row
    },
    set(val) {
      props.scope.row = val
    },
  })
  const field = computed(() => props.scope?.column?.property ?? 'id')
  const value = computed({
    get() {
      return row.value[field.value] ?? undefined
    },
    set(val) {
      row.value[field.value] = val
    },
  })
  return { value, row, field }
}
export function createOptions(props: Props, defaultOptions: Record<any, any>) {
  return computed(() => {
    return Object.assign({}, defaultOptions, props.options)
  })
}

export function getConfig(key?: string, defaultValue?: any) {
  const _config = useGlobal().$pluginsConfig?.['mine-admin/cell-render'] || {}
  return key ? get(_config, key, defaultValue) : _config
}

export function cellRenderPluginName(name) {
  return `ma-${name}`
}

// 执行函数,传入一个同步函数或者异步函数,以及他的参数 获得返回结果,异步函数抛出来的异常也要捕获成返回false,始终返回一个只会成功的promise
// eslint-disable-next-line ts/no-unsafe-function-type
export async function exec(fn: Function, ...args: any[]) {
  try {
    const result = await fn(...args)
    return Promise.resolve(result)
  }
  // eslint-disable-next-line unused-imports/no-unused-vars
  catch (e) {
    return Promise.resolve(false)
  }
}
