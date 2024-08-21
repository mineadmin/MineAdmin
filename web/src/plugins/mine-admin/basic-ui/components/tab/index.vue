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
import useParentNode from '@/hooks/useParentNode.ts'

defineOptions({ name: 'MTabs' })

const props = withDefaults(
  defineProps<{
    options: optionItems[]
  }>(), {})

const emit = defineEmits<{
  change: [T]
}>()

const id = ref<string>(`tabDomId_${Math.floor(Math.random() * 100000 + Math.random() * 20000 + Math.random() * 5000)}`)

const value = defineModel<T>()
const selectedEl = ref<HTMLElement>()

interface optionItems {
  label: string | (() => string)
  value: T
  icon?: string
}

function handleClick(e: MouseEvent, item: optionItems): any {
  e.preventDefault()
  if (value.value !== item.value) {
    value.value = item.value
    const node = useParentNode(e, 'a') as HTMLElement
    setSelectedElStyle(node)
    emit('change', item.value)
  }
}

function setSelectedElStyle(node: HTMLElement) {
  if (selectedEl.value) {
    selectedEl.value.style.width = `${node.offsetWidth}px`
    selectedEl.value.style.transform = `translateX(${node.offsetLeft - 4}px)`
  }
}

function initSelectedElStyle() {
  const node = document.querySelector(`#${id.value} .tab-list-item.active`) as HTMLElement
  node && setSelectedElStyle(node)
}

onMounted(() => {
  selectedEl.value = document.querySelector(`#${id.value} .tab-list-item-selected`) as HTMLElement
  useResizeObserver(document.body, initSelectedElStyle)
})
</script>

<template>
  <div :id="id" class="tabs-list-container">
    <div class="tab-list-item-selected" />
    <a
      v-for="item in props.options"
      class="tab-list-item"
      :class="{ active: item.value === value }"
      @click="(e: MouseEvent) => handleClick(e, item)"
    >
      <ma-svg-icon v-if="item.icon" :name="item.icon" :size="16" />
      <span>{{ item.label }}</span>
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
    px-2 py-1.5 rounded cursor-pointer w-full transition-all duration-500
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
