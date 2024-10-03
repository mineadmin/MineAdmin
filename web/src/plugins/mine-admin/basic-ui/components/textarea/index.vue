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
import mergeClassName from '../../utils/mergeClassName'

defineOptions({ name: 'MTextarea' })

const props = withDefaults(
  defineProps<{
    class?: string | string[] | object
  }>(),
  {
    class: '',
  },
)

const value = defineModel<T>()
const el = ref()
const inputClass = computed(() => {
  return mergeClassName([
    'relative block w-full border-0 rounded-md',
    'bg-white text-sm shadow-sm disabled-cursor-not-allowed',
    'dark-bg-dark-2 disabled-opacity-50 focus-outline-none focus-ring-2 focus-ring-[rgb(var(--ui-primary))]',
    'placeholder-[#888] dark-focus-ring-[rgb(var(--ui-primary))]',
    'outline-none transition-duration-[0.2s] ring-1 ring-gray-2 dark-ring-dark-1',
    'px-2.5 py-2 text-gray-7 dark-text-stone-3 rounded outline-none  ring-1 ring-stone-2 ring-inset focus-visible-outline-none',
  ], props.class)
})

defineExpose({ el })
</script>

<template>
  <div class="flex">
    <textarea v-bind="$attrs" ref="el" v-model="value" :class="inputClass" />
  </div>
</template>
