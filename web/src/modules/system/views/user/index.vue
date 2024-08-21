<script setup lang="ts">
defineOptions({ name: 'user' })
const user = ref('')
</script>

<template>
  <div>
    <div>用户</div>
    <el-input v-model="user" />
    <router-link to="/welcome">
      welcome
    </router-link>
  </div>
</template>
