<template>
  <div class="chunk-upload">
    <el-upload drag action="" :show-file-list="false" :disabled="isUpload" :http-request="handleUpload">
      <el-icon-upload-filled style="width:120px; color: #ccc" />
      <div class="el-upload__text">
        <h3>分片上传（不限制文件类型）</h3>
        将文件拖到此处，或<em>点击上传</em>。分片大小：{{ tool.formatSize(props.chunkSize * 1024) }}
        </div>
    </el-upload>
    <el-progress class="progress" :text-inside="true" :stroke-width="26" :percentage="progress" />
  </div>
</template>
<script setup>
import { ref, reactive, defineProps } from 'vue'
import tool from '@/utils/tool'
import { ElMessage } from 'element-plus'

const isUpload = ref(false)
const progress = ref(20)

const props = defineProps({
  modelValue: {
    type: String,
    default: () => {}
  },
  chunkSize: {
    type: Number,
    default: 204800,  // 默认 200k
  }
})

const handleUpload = (data) => {
  if (! data) {
    ElMessage.error('没有上传任何文件')
    return
  }
  if(data.file.size < props.chunkSize) {
    ElMessage.error('文件大小不足分片，请使用普通方式上传')
    return
  }
}


</script>

<style scoped>
.chunk-upload {
  width: 360px;
}
.progress {
  margin: 10px 0 10px 0;
}
</style>