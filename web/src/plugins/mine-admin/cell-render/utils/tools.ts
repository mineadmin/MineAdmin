import type { ComputedRef } from 'vue'
import { get } from 'lodash-es'

interface Props {
  scope: Record<string, any>
  options: Record<string, any>
}

interface RFV {
  row: ComputedRef<any>
  field: ComputedRef<string>
  value: ComputedRef<any>
}

export type WithOnEventListeners<T> = {
  [K in keyof T as `on${Capitalize<string & K>}`]?: T[K];
}

export function createRowFieldValues(props: Props): RFV {
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
