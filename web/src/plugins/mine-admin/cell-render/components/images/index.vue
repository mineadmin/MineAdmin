<script setup lang="ts">
import { makeRFV } from '../../utils/tools.ts'

const props = withDefaults(defineProps<{
  scope: any
  options: Options
}>(), {
  scope: () => {},
  options: () => {
    return {
      fit: 'cover',
    }
  },
})

const {
  row,
  field,
  value,
} = makeRFV(props)
export interface Options {
  size?: number
  fit?: 'cover' | 'contain' | 'fill' | 'none' | 'scale-down'
}

const imageArr = computed(() => {
  if (value.value && typeof value.value === 'string') {
    return value.value.split(',')
  }
  return value.value
})

// 默认大小需要在config中配置,方便开发者全局修改
const style = computed(() => {
  return {
    width: `${props.options.size ?? 40}px`,
    height: `${props.options.size ?? 40}px`,
  }
})
</script>

<template>
  <div>
    <el-space>
      <template v-for="image in imageArr">
        <el-image :style="style" :src="image" :preview-src-list="imageArr" :fit="options.fit ?? 'cover'" preview-teleported />
      </template>
    </el-space>
  </div>
</template>
