<template>
  <el-upload drag action="" :show-file-list="false" v-show="show">
    <el-icon-upload-filled style="width:140px; color: #ccc" />
    <div class="el-upload__text">将文件拖到此处，或<em>点击上传</em></div>
  </el-upload>
</template>
<script setup>
import { ref, reactive, defineProps } from 'vue'

const show = ref(true)

defineProps({
  modelValue: {
    type: String,
    default: () => {}
  }
})
</script>
