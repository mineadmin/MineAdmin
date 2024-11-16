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

defineOptions({ name: 'MaDictSelect' })

const {
  dictName = '',
  data = [],
  transScope = 'global',
} = defineProps<{
  // 字典名称
  dictName?: string
  // 字典数据
  data?: Dictionary[] | (() => Dictionary[])
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
  <el-select v-model="model" v-bind="$attrs">
    <slot name="default">
      <template v-if="dictionaryData">
        <template v-for="item in dictionaryData as Dictionary[]" :key="item">
          <el-option :value="item.value" :label="item?.i18n ? t(item.i18n) : item.label">
            <slot v-if="$slots.optionDefault" name="optionDefault" />
          </el-option>
        </template>
      </template>
    </slot>
    <template v-for="slot in Object.keys($slots)" #[slot]>
      <slot v-if="slot !== 'default'" :name="slot" />
    </template>
  </el-select>
</template>

<style scoped lang="scss">

</style>
