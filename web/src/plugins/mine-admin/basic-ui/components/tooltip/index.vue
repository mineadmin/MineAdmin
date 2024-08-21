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
defineOptions({ name: 'MTooltip' })

withDefaults(
  defineProps<{
    text?: string | (() => string)
    enable?: boolean
  }>(),
  {
    text: '',
    enable: true,
  },
)
</script>

<template>
  <v-tooltip v-if="enable" :popper-triggers="['hover']" v-bind="$attrs">
    <slot />
    <template #popper>
      <slot name="text">
        {{ text }}
      </slot>
    </template>
  </v-tooltip>
  <div v-else>
    <slot />
  </div>
</template>
