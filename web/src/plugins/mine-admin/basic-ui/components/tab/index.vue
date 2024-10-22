<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<script setup lang="ts" generic="T extends string | number">
import { useResizeObserver } from '@vueuse/core'
import type { MTabsEmits, MTabsOptionItems, MTabsProps } from './type.ts'
import useParentNode from '@/hooks/useParentNode.ts'

defineOptions({ name: 'MTabs' })

const props = withDefaults(
  defineProps<MTabsProps<T>>(),
  {
    direction: 'horizontal',
    align: 'center',
  },
)

const emit = defineEmits<MTabsEmits>()

const id = ref<string>(`tabDomId_${Math.floor(Math.random() * 100000 + Math.random() * 20000 + Math.random() * 5000)}`)
const value = defineModel<T>()
const selectedEl = ref<HTMLElement>()

function handleClick(e: MouseEvent, item: MTabsOptionItems<T>): any {
  e.preventDefault()
  if (value.value !== item.value) {
    value.value = item.value
    const node = useParentNode(e, 'a') as HTMLElement
    setSelectedElStyle(node)
    emit('change', item.value, item)
  }
}

function setSelectedElStyle(node: HTMLElement) {
  if (selectedEl.value) {
    if (props.direction === 'vertical') {
      selectedEl.value.style.height = `${node.offsetHeight}px`
      selectedEl.value.style.width = `${node.offsetWidth}px`
      selectedEl.value.style.transform = `translateY(${node.offsetTop - 4}px)`
    }
    else {
      selectedEl.value.style.height = `${node.offsetHeight}px`
      selectedEl.value.style.width = `${node.offsetWidth}px`
      selectedEl.value.style.transform = `translateX(${node.offsetLeft - 4}px)`
    }
  }
}

function initSelectedElStyle() {
  const node = document.querySelector(`#${id.value} .tab-list-item.active`) as HTMLElement
  node && setSelectedElStyle(node)
}

watch([
  () => props.options,
  () => props.direction,
], () => {
  nextTick(initSelectedElStyle)
}, { deep: true })

onMounted(() => {
  selectedEl.value = document.querySelector(`#${id.value} .tab-list-item-selected`) as HTMLElement
  useResizeObserver(document.body, initSelectedElStyle)
})
</script>

<template>
  <div :id="id" class="tabs-list-container" :class="{ 'flex-col': props.direction === 'vertical' }">
    <div class="tab-list-item-selected" />
    <a
      v-for="item in props.options"
      class="tab-list-item text-left"
      :class="{
        'active': item.value === value,
        'w-full': props.direction === 'horizontal',
        'h-full': props.direction === 'vertical',
      }"
      :style="{
        'justify-content': props.align,
      }"
      @click="(e: MouseEvent) => handleClick(e, item)"
    >
      <slot name="default" :item="item">
        <ma-svg-icon v-if="item.icon" :name="item.icon" :size="16" />
        <span>{{ typeof item.label === 'function' ? item.label?.() : item.label }}</span>
      </slot>
    </a>
  </div>
</template>

<style lang="scss" scoped>
.tabs-list-container {
  @apply flex p-1 rounded relative
    bg-gray-1  dark-bg-dark-3
  ;

  flex-grow: 1;
  justify-items: flex-start;
}

.tab-list-item {
  @apply flex items-center justify-center gap-1.5 relative z-3 dark-text-gray-4 text-gray-5
    px-2 py-1.5 rounded cursor-pointer transition-all duration-500
  ;
}

.tab-list-item.active {
  @apply dark-text-white text-stone-7;
}

.tab-list-item-selected {
  @apply bg-white dark-bg-dark-7 shadow absolute rounded z-2;

  height: calc(100% - 8px);
  transition: transform 0.3s;
}
</style>
