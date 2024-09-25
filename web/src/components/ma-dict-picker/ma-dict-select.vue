<script setup lang="ts">
import type { Dictionary } from '#/global'
import { useLocalTrans } from '@/hooks/useLocalTrans.ts'

defineOptions({ name: 'MaDictSelect' })

const {
  dictName = undefined,
  transScope = 'global',
} = defineProps<{
  // 字典名称
  dictName: string
  // 翻译范围
  transScope?: 'global' | 'local'
}>()
const dictStore = useDictStore()
const dictionaryData = computed<Dictionary[]>(() => {
  return dictStore.find(dictName)
})

const t = (transScope === 'global' ? useTrans() : useLocalTrans())
console.log(dictStore.find(dictName))
const model = defineModel()
</script>

<template>
  <el-select v-model="model" v-bind="$attrs">
    <slot name="default">
      <template v-for="item in dictionaryData as Dictionary[]" :key="item">
        <el-option :value="item.value" :label="item?.i18n ? t(item.i18n) : item.label">
          <slot v-if="$slots.optionDefault" name="optionDefault" />
        </el-option>
      </template>
    </slot>
    <template v-if="$slots.empty" #empty>
      <slot name="empty" />
    </template>
    <template v-if="$slots.footer " #footer>
      <slot name="footer" />
    </template>
    <template v-if="$slots.prefix" #prefix>
      <slot name="prefix" />
    </template>
    <template v-if="$slots.tag" #tag>
      <slot name="tag" />
    </template>
    <template v-if="$slots.loading" #loading>
      <slot name="loading" />
    </template>
    <template v-if="$slots.label" #label>
      <slot name="label" />
    </template>
  </el-select>
</template>

<style scoped lang="scss">

</style>
