<template>
  <iframe :src="url" frameborder="0" width="100%" class="frame"></iframe>
</template>

<script setup>
import { ref } from 'vue'
const url = ref(null)
url.value = `${window.location.protocol}//${window.location.host}/formDesigner/index.html` 
</script>

<style scoped>
.frame {
  height: calc(100% - 3px)
}
</style>