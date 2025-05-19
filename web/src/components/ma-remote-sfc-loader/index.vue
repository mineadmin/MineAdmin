<script setup lang="ts">
import type { Component } from 'vue'
import type { SFCModule } from '#/global'

import * as Vue from 'vue'
import { loadModule } from 'vue3-sfc-loader/dist/vue3-sfc-loader'
import { useMessage } from '@/hooks/useMessage.ts'
import { uid } from 'radash'

import MaProTable from '@mineadmin/pro-table'

defineOptions({ name: 'MaRemoteSfcLoader' })

const {
  url,
  module = {},
} = defineProps<{
  url: string | (() => Promise<any>)
  module?: SFCModule<any>
}>()

const builtInModule = {
  vue: Vue,
  useMessage,
  useHttp,
  MaProTable,
}

const name = ref(`${uid(8)}.vue`)
const baseModule = ref<SFCModule<any>>(Object.assign(builtInModule, module))
const remoteComponent = ref<Component>()

const remoteOptions = ref({
  moduleCache: baseModule.value,
  async getFile() {
    const template: string = '<template><div>页面加载失败</div></template>'
    if (typeof url === 'string') {
      const response = await useHttp().get(url as string)
      name.value = `${response.data?.name ?? uid(7)}.vue`
      return response.data?.content ?? template
    }
    else {
      const response = await url?.()
      name.value = `${response.data?.name ?? uid(7)}.vue`
      return response.data?.content ?? template
    }
  },
  addStyle(textContent: string) {
    const style = Object.assign(document.createElement('style'), { textContent })
    const ref = document.head.getElementsByTagName('style')[0] || null
    document.head.insertBefore(style, ref)
  },
})

remoteComponent.value = markRaw(defineAsyncComponent(() => loadModule(name.value, remoteOptions.value)))
</script>

<template>
  <component
    :is="remoteComponent"
    v-bind="$attrs"
  />
</template>

<style scoped lang="scss">
</style>
