import type { ComputedRef } from 'vue'

interface Props {
  scope: Record<string, any>
  options: any
}

interface RFV {
  row: ComputedRef<any>
  field: ComputedRef<string>
  value: ComputedRef<any>
}
export function makeRFV(props: Props): RFV {
  const row = computed({
    get() {
      return props.scope.row
    },
    set(val) {
      props.scope.row = val
    },
  })
  const field = computed(() => props.options?.prop ?? 'buttons')
  const value = computed({
    get() {
      return row.value[field.value] ?? undefined
    },
    set(val) {
      row.value[field.value] = val
    },
  })
  return { row, field, value }
}

export function makeName(name: string) {
  const prefix = 'mz-cell-render-'
  return `${prefix}${name}`
}
