<script setup lang="ts">
import { uid } from 'radash'
import { useResizeObserver } from '@vueuse/core'

defineOptions({ name: 'MaColCard' })

const props = defineProps<{
  // 一行多少个
  cols?: number
  // 自定义样式
  cardStyle?: Record<string, any> | string
}>()

const id = ref<string>(uid(5))
const columnCount = ref<number>(props.cols ?? 4)

async function setGridColumns() {
  await nextTick(() => {
    (document?.querySelector(`.${id.value}`) as HTMLDivElement)!.style!.setProperty('grid-template-columns', `repeat(${columnCount.value}, minmax(0,1fr))`)
  })
}

onMounted(async () => {
  await setGridColumns()

  useResizeObserver(document.body, async (entries) => {
    const [entry] = entries
    const { width } = entry.contentRect
    if (width < 640) {
      columnCount.value = 1
    }
    else if (width < 960) {
      columnCount.value = 2
    }
    else if (width < 1280) {
      columnCount.value = 3
    }
    else {
      columnCount.value = props.cols ?? 4
    }
    await setGridColumns()
  })
})
</script>

<template>
  <div class="mine-card-container" :class="id">
    <slot name="card">
      <template v-for="col in (props.cols ?? 4)">
        <div class="mine-com-card" :class="`mine-com-card${col}`" :style="cardStyle">
          <slot name="card-content" v-bind="{ index: col }" />
        </div>
      </template>
    </slot>
  </div>
</template>

<style scoped lang="scss">
.mine-card-container {
  @apply grid gap-3 grid-cols-4;

  .mine-com-card {
    @apply border border-stone-2 border-solid rounded p-3 hover-shadow-md
      dark-border-dark-1 dark-shadow-dark-9
    transition-all duration-280 ease-in-out
    ;
  }
}
</style>
