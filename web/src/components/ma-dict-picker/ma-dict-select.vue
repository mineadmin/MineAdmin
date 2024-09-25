<script setup lang="ts">
import type { Dictionary } from '#/global'
import { useLocalTrans } from '@/hooks/useLocalTrans.ts'

defineOptions({ name: 'MaDictSelect' })

const {
  dictName = '',
  transScope = 'global',
} = defineProps<{
  // 字典名称
  dictName: string
  // 翻译范围
  transScope?: 'global' | 'local'
}>()

const dictStore = useDictStore()
const dictionaryData = computed<Dictionary[] | null>(() => {
  return dictStore.find(dictName)
})

const t = (transScope === 'global' ? useTrans() : useLocalTrans())
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
