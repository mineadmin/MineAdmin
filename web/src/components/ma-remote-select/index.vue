<script setup lang="ts">
import { ElSelectV2 } from 'element-plus'

defineOptions({ name: 'MaRemoteSelect' })

const model = defineModel<any>()
const options = ref<any[]>([])
</script>

<template>
  <ElSelectV2 v-bind="$attrs" v-model="model" :options="options">
    <template v-for="slot in Object.keys($slots)" #[slot]>
      <slot :name="slot" />
    </template>
  </ElSelectV2>
</template>

<style scoped lang="scss">

</style>
