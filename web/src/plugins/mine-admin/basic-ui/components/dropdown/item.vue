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
defineOptions({ name: 'MDropdownItem' })

const props = withDefaults(
  defineProps<{
    selected?: boolean
    disabled?: boolean
    type: 'default' | 'danger'
    handle?: (e: MouseEvent) => any
  }>(),
  {
    selected: false,
    disabled: false,
    type: 'default',
    handle: () => {},
  },
)

function onClick(e: MouseEvent) {
  if (props.disabled) {
    return
  }
  props.handle(e)
}
</script>

<template>
  <div
    class="dropdown-item-wrapper"
    :class="[
      `dropdown-item-wrapper--${type}${selected ? '--selected' : ''}`,
      { 'dropdown-item-wrapper--disabled': disabled },
    ]"
    role="menuitem"
    tabindex="-1"
    @click.prevent="onClick"
  >
    <div class="flex items-center gap-3">
      <slot name="prefix-icon" />

      <slot />
    </div>

    <slot name="suffix-icon" />
  </div>
</template>

<style lang="scss">
.dropdown-item-wrapper {
  @apply flex cursor-pointer justify-between items-center gap-1 rounded px-4 py-2 text-sm;

  &--default {
    @apply text-stone-700 hover:bg-gray-1 hover:text-gray-900
      dark-text-gray-2 dark-hover:bg-dark-3
    ;

    &--selected {
      @apply bg-gray-100 dark-bg-dark-7 text-gray-900 dark-text-gray-100;
    }
  }

  &--danger {
    @apply text-red-500 hover:bg-red-50 hover:text-red-700;

    &--selected {
      @apply bg-red-50 text-red-700;
    }
  }

  &--disabled {
    @apply opacity-70 cursor-not-allowed;
  }
}
</style>
