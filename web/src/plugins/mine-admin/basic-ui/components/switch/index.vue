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
defineOptions({ name: 'MSwitch' })

const props = withDefaults(
  defineProps<{
    disabled?: boolean
    loading?: boolean
  }>(),
  {
    disabled: false,
    loading: false,
  },
)

const emit = defineEmits<{
  (event: 'update:modelValue', value: boolean): void
  (event: 'change', value: boolean): void
}>()

const value = defineModel<boolean>({
  default: false,
})

function handleChange() {
  if (props.disabled || props.loading) {
    return
  }

  emit('update:modelValue', !value.value)
  emit('change', !value.value)
}
</script>

<template>
  <div class="switch-wrapper">
    <button
      :class="{
        'bg-gray-2 dark-bg-dark-1': !value,
        '!bg-[rgb(var(--ui-primary))]': value,
        'switch-disabled': disabled || loading,
      }"
      aria-checked="false"
      class="switch-inner"
      role="switch"
      type="button"
      :disabled="disabled || loading"
      @click="handleChange"
    >
      <span
        :class="{
          'translate-x-0': !modelValue,
          'translate-x-5': modelValue,
        }"
        aria-hidden="true"
        class="switch-indicator"
      >
        <svg
          v-if="loading"
          class="animate-spin"
          fill="none"
          viewBox="0 0 24 24"
          xmlns="http://www.w3.org/2000/svg"
        >
          <circle
            class="opacity-0"
            cx="12"
            cy="12"
            r="10"
            stroke="currentColor"
            stroke-width="4"
          />
          <path
            class="opacity-30"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            fill="currentColor"
          />
        </svg>
        <slot v-else name="icon" />
      </span>
    </button>
  </div>
</template>

<style lang="scss">
.switch-wrapper {
  @apply inline-flex box-border;

  .switch-inner {
    @apply relative inline-flex flex-shrink-0
      h-6 w-11 border-2 border-transparent rounded-full
      cursor-pointer transition-colors ease-in-out duration-200;

    &.switch-disabled {
      @apply opacity-60 cursor-not-allowed;
    }

    .switch-indicator {
      @apply pointer-events-none inline-block h-5 w-5 rounded-full
        bg-white shadow transform ring-0 transition ease-in-out duration-200;
    }
  }
}
</style>
