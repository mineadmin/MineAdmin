<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<script setup lang="ts">
import { Icon } from '@iconify/vue'

defineOptions({
  name: 'MaSvgIcon',
})

const props = defineProps<{
  name: string
  flip?: 'horizontal' | 'vertical' | 'both'
  rotate?: number
  color?: string
  size?: string | number
}>()

const outputType = computed(() => {
  if (/i-[^:]+:[^:]+/.test(props.name)) {
    return 'unocss'
  }
  else if (props.name && props.name.includes(':')) {
    return 'iconify'
  }
  else {
    return 'svg'
  }
})

const style = computed(() => {
  const transform: string[] = []
  if (props.flip) {
    switch (props.flip) {
      case 'horizontal':
        transform.push('rotateY(180deg)')
        break
      case 'vertical':
        transform.push('rotateX(180deg)')
        break
      case 'both':
        transform.push('rotateX(180deg)')
        transform.push('rotateY(180deg)')
        break
    }
  }
  if (props.rotate) {
    transform.push(`rotate(${props.rotate % 360}deg)`)
  }
  return {
    ...(props.color && { color: props.color }),
    ...(props.size && { fontSize: typeof props.size === 'number' ? `${props.size}px` : props.size }),
    ...(transform.length && { transform: transform.join(' ') }),
  }
})
</script>

<template>
  <i class="relative h-[1em] w-[1em] flex-inline items-center justify-center fill-current leading-[1em]" v-bind="$attrs" :class="{ [name]: outputType === 'unocss' }" :style="style">
    <Icon v-if="outputType === 'iconify'" :icon="name" />
    <svg v-else-if="outputType === 'svg'" class="h-[1em] w-[1em]">
      <use :xlink:href="`#icon-${name}`" />
    </svg>
  </i>
</template>
