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
import WorkbenchFast from './components/workbench/workbench-fast.vue'
import WorkbenchLogin from '~/base/views/dashboard/components/workbench/workbench-login.vue'

defineOptions({ name: 'dashboard:workbench' })
</script>

<template>
  <div>
    <WorkbenchFast />
    <WorkbenchLogin />
  </div>
</template>

<style scoped lang="scss">
</style>
