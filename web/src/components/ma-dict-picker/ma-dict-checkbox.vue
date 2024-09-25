<script setup lang="ts">
import type { Dictionary } from '#/global'
import { useLocalTrans } from '@/hooks/useLocalTrans.ts'

defineOptions({ name: 'MaDictCheckbox' })

const {
  dictName = '',
  renderMode = 'normal',
  transScope = 'global',
} = defineProps<{
  // 字典名称
  dictName: string
  // 渲染模式：`normal: el-checkbox` | `button: el-checkbox-button`
  renderMode?: 'normal' | 'button'
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
  <el-checkbox-group v-model="model" v-bind="$attrs">
    <slot name="default">
      <template v-if="dictionaryData">
        <template v-for="item in dictionaryData as Dictionary[]" :key="item">
          <component :is="renderMode === 'normal' ? 'el-checkbox' : 'el-checkbox-button'" :value="item.value">
            <slot name="optionDefault">
              {{ item?.i18n ? t(item.i18n) : item.label }}
            </slot>
          </component>
        </template>
      </template>
    </slot>
  </el-checkbox-group>
</template>

<style scoped lang="scss">

</style>
