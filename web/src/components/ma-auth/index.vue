<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<script setup lang="ts" generic="T extends string | string[]">
import hasAuth from '@/utils/permission/hasAuth'

defineOptions({ name: 'MaAuth' })

withDefaults(defineProps<{
  value?: string | string[]
}>(), {
  value: '',
})
</script>

<template>
  <div>
    <slot v-if="hasAuth(value)" />
    <slot v-else name="notAuth" />
  </div>
</template>
