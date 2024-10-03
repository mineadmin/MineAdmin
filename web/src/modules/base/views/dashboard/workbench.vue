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
