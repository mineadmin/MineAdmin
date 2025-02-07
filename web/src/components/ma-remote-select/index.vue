<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<script setup lang="ts">
import { ElSelectV2 } from 'element-plus'
import { useMessage } from '@/hooks/useMessage.ts'

defineOptions({ name: 'MaRemoteSelect' })

const props = defineProps<{
  api?: <T>(...args: T[]) => Promise<T>
  url?: string
  axiosConfig?: {
    autoRequest: boolean
    method?: string
    params?: Record<string, any>
    data?: Record<string, any>
    header?: Record<string, any>
    timeout?: number
  }
  dataHandle?: (response: any) => any[]
}>()

const emit = defineEmits<{
  (event: 'select-item', value: Record<string, any>): void
  (event: 'change', value: any): void
}>()

const elSelectV2Ref = ref<any>()
const msg = useMessage()
const model = defineModel<any>()
const options = ref<any[]>([])

function handleChange(value: any) {
  emit('change', value)
  const key = elSelectV2Ref.value.valueKey
  emit('select-item', options.value.find(item => item[key] === value) ?? null)
}

function request() {
  if (props?.api && typeof props.api === 'function') {
    props.api(props.axiosConfig).then((res: any) => {
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
}

(props?.axiosConfig?.autoRequest ?? true) && request()

defineExpose({
  refresh: () => request(),
  selectRef: elSelectV2Ref,
})
</script>

<template>
  <ElSelectV2
    ref="elSelectV2Ref"
    v-bind="$attrs"
    v-model="model"
    :options="options"
    clearable
    @change="handleChange"
  >
    <template v-for="(_, name) in $slots" #[name]="scopedData">
      <slot :name="name" v-bind="scopedData" />
    </template>
  </ElSelectV2>
</template>

<style scoped lang="scss">

</style>
