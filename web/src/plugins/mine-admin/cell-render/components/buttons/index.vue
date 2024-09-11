<script setup lang="ts">
import type { TextProps } from 'element-plus'
import type { MakeOptions } from '../../utils/type.ts'
import { makeRFV } from '../../utils/tools.ts'

const props = withDefaults(defineProps<{
  scope: any
  options: Options
}>(), {
  scope: () => {},
  options: () => {
    return {
      lineClamp: 1,
    }
  },
})

const {
  row,
  field,
  value,
} = makeRFV(props)
export interface Options extends MakeOptions<TextProps> {}
</script>

<template>
  <ElText v-bind="options">
    {{ value }}
  </ElText>
</template>
