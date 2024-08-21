<script setup lang="ts" generic="T extends string | string[]">
import hasAuth from '@/utils/permission/hasAuth'

defineOptions({ name: 'MaAuth' })

withDefaults(defineProps<{
  value: string | string[]
}>(), {
  value: '',
})
</script>

<template>
  <div>
    <slot v-if="hasAuth(value)" />
    <slot v-else name="notAuth" />
  </div>
</template>
