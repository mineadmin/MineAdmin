<script setup lang="ts">
import mergeClassName from '../../utils/mergeClassName'

defineOptions({ name: 'MButton' })

const props = withDefaults(
  defineProps<{
    class?: string | string[] | object
  }>(),
  {
    class: '',
  },
)
const el = ref()
const buttonClass = computed(() => {
  return mergeClassName([
    'bg-[rgb(var(--ui-primary))]',
    'dark-bg-[rgb(var(--ui-primary))]',
    'hover-bg-[rgb(var(--ui-primary)/.75)]',
    'dark-hover-bg-[rgb(var(--ui-primary)/.65)]',
    'active-bg-[rgb(var(--ui-primary))]',
    'dark-active-bg-[rgb(var(--ui-primary)/55%)]',
    'text-gray-1',
    'dark-text-gray-2',
    'hover-shadow-inner',
    'outline-none border-0 rounded py-2.5 px-5',
    'cursor-pointer inline-flex',
    'text-[.85rem] text-center',
    'items-center justify-center',
    'gap-x-1 transition-duration-[0.15s] shadow focus-visible-outline-none',
  ], props.class)
})

defineExpose({ el })
</script>

<template>
  <button v-bind="$attrs" ref="el" :class="buttonClass" :disabled="buttonClass.indexOf('loading') !== -1">
    <slot />
  </button>
</template>
