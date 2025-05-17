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
import type { Dictionary } from '#/global'
import type { TransType } from '@/hooks/auto-imports/useTrans.ts'
import { isFunction } from 'radash'

defineOptions({ name: 'MaDictRadio' })

const {
  dictName = '',
  data = [],
  renderMode = 'normal',
  transScope = 'global',
} = defineProps<{
  // 字典名称
  dictName?: string
  // 字典数据
  data?: Dictionary[] | (() => Dictionary[])
  // 渲染模式：`normal: el-radio` | `button: el-radio-button`
  renderMode?: 'normal' | 'button'
  // 翻译范围
  transScope?: 'global' | 'local'
}>()
const dictStore = useDictStore()
const dictionaryData = computed<Dictionary[] | null>(() => {
  return dictName === '' ? (isFunction(data) ? data() : data) : dictStore.find(dictName)
})

const i18n = useTrans() as TransType
const t = transScope === 'global' ? i18n.globalTrans : i18n.localTrans

const model = defineModel<any>()
</script>

<template>
  <el-radio-group v-model="model" v-bind="$attrs">
    <slot name="default">
      <template v-if="dictionaryData">
        <template v-for="item in dictionaryData as Dictionary[]" :key="item">
          <component :is="renderMode === 'normal' ? 'el-radio' : 'el-radio-button'" :value="item.value" :disabled="item.disabled">
            <slot name="optionDefault">
              {{ item?.i18n ? t(item.i18n) : item.label }}
            </slot>
          </component>
        </template>
      </template>
    </slot>
  </el-radio-group>
</template>

<style scoped lang="scss">

</style>
