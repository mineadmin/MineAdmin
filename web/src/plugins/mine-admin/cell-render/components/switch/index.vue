<script setup lang="ts">
import { makeRFV } from '../../utils/tools.ts'

const props = withDefaults(defineProps<{
  scope: any
  options: Options
}>(), {
  scope: () => {},
  options: () => {
    return {}
  },
})

const {
  row,
  field,
  value,
} = makeRFV(props)

export interface Options {

}
</script>

<template>
  <el-switch v-model="value" />
</template>
