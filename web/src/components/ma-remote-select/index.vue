<script setup lang="ts">
import { ElSelectV2 } from 'element-plus'
import { useMessage } from '@/hooks/useMessage.ts'

defineOptions({ name: 'MaRemoteSelect' })

const props = defineProps<{
  api?: <T>(...args: T[]) => Promise<T>
  url?: string
  axiosConfig?: {
    method?: string
    params?: Record<string, any>
    data?: Record<string, any>
    header?: Record<string, any>
    timeout?: number
  }
  dataHandle?: (response: any) => any[]
}>()

const msg = useMessage()

// const emit = defineEmits<{
//   (event: 'select', value: Record<string, any>): void
// }>()
// function handleChange(val: any) {
//
// }
const model = defineModel<any>()
const options = ref<any[]>([])

if (props?.api && typeof props.api === 'function') {
  props.api().then((res: any) => {
    options.value = props?.dataHandle?.(res) ?? res.data
  }).catch((err) => {
    msg.error(err)
  })
}
else if (props?.url) {
  const method = useHttp()[props?.axiosConfig?.method ?? 'get']
  method(props?.url, props?.axiosConfig).then((res: any) => {
    options.value = props?.dataHandle?.(res) ?? res.data
  }).catch((err: any) => {
    msg.error(err)
  })
}
else {
  msg.error('MaRemoteSelect 组件未指定 api 或 url ')
  console.error('[ma-remote-select]：api() or url error')
}
</script>

<template>
  <ElSelectV2 v-bind="$attrs" v-model="model" :options="options" clearable>
    <template v-for="slot in Object.keys($slots)" #[slot]>
      <slot :name="slot" />
    </template>
  </ElSelectV2>
</template>

<style scoped lang="scss">

</style>
