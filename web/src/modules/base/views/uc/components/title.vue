<script setup lang="ts">
import type { MineRoute } from '#/global'

const route = useRoute() as MineRoute.routeRecord
const title = computed(() => {
  return route.meta?.i18n ? useTrans(route.meta?.i18n) : route?.meta?.title ?? '未知'
})
</script>

<template>
  <div class="title">
    <div class="flex items-center gap-x-2">
      <ma-svg-icon v-if="route.meta?.icon" :name="route.meta?.icon ?? ''" size="20" />
      {{ title }}
    </div>
    <div>
      <slot name="extra" />
    </div>
  </div>
</template>

<style scoped lang="scss">
.title {
  @apply h-[55px] flex items-center justify-between
    bg-white px-3 dark-bg-dark-8 dark-b-b-dark-4 b-b-1 b-b-solid b-b-gray-2;
}
</style>
