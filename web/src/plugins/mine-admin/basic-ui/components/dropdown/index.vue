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
import type { Placement, TriggerEvent } from 'floating-vue'
import mergeClassName from '../../utils/mergeClassName.ts'
import DropdownContextInjectionKey from './symbols'

defineOptions({ name: 'MDropdown' })

const props = withDefaults(
  defineProps<{
    placement?: Placement
    triggers?: TriggerEvent[]
    class?: string | string[] | object
  }>(),
  {
    placement: 'bottom',
    triggers: () => ['hover'],
    class: '',
  },
)

const emit = defineEmits<{
  (event: 'show'): void
}>()

const dropdownRef = ref()

function hide() {
  dropdownRef.value?.hide()
}

const dropdownClass = computed(() => {
  return mergeClassName([
    'min-w-[13rem] p-1.5',
  ], props.class)
})

provide(DropdownContextInjectionKey, {
  hide,
})

defineExpose({
  hide,
})
</script>

<template>
  <!-- @vue-ignore -->
  <v-dropdown
    ref="dropdownRef"
    :placement="props.placement"
    :triggers="props.triggers"
    :show-triggers="props.triggers.includes('click') ? undefined : ['hover']"
    :popper-triggers="props.triggers.includes('click') ? undefined : ['hover']"
    :dispose-timeout="null"
    @show="emit('show')"
  >
    <slot />
    <template #popper>
      <div
        :class="dropdownClass"
        role="menu"
        aria-orientation="vertical"
        aria-labelledby="menu-button"
        tabindex="-1"
      >
        <slot name="popper" />
      </div>
    </template>
  </v-dropdown>
</template>
