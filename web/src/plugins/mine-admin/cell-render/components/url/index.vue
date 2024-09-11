<script setup lang="ts">
import { Link } from '@element-plus/icons-vue'
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
export interface Options {
  copyable?: boolean
  openable?: boolean
}
</script>

<template>
  <el-input
    size="small"
    :model-value="value"
    placeholder="暂无链接"
    readonly
  >
    <template #prepend>
      <el-button :icon="Link" />
    </template>
  </el-input>
</template>
