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
  const row = computed(() => props.scope?.row ?? {})
  const field = computed(() => props.options?.prop ?? 'buttons')
  const value = computed(() => row.value[field.value] ?? undefined)
  return { row, field, value }
}

export function makeName(name: string) {
  const prefix = 'mz-cell-render-'
  return `${prefix}${name}`
}
