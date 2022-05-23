<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage as Message } from 'element-plus'
const codeText = ref('')
const verfiyCanvas = ref(null)
const canvasSetting = reactive({
  pool: 'abcdefghjkmnpqrstuvwxyz23456789',
  width: 120,
  height: 38,
  size: 4
})

const checkResult = (verifyCode) => {
  if (! verifyCode || verifyCode.length === 0) {
    Message.error('请输入验证码')
    return false
  }

  if (verifyCode.toLowerCase() !== codeText.value.toLowerCase()) {
    Message.error('验证码错误')
    generateCode()
    return false
  } else {
    return true
  }
}

const randomNum = (min, max) => {
  return parseInt(Math.random() * (max - min) + min)
}

const randomColor = (min, max) => {
  const r = randomNum(min, max)
  const g = randomNum(min, max)
  const b = randomNum(min, max)
  return `rgb(${r},${g},${b})`
}

const generateCode = () => {
  codeText.value = ''
  const ctx = verfiyCanvas.value.getContext('2d')
  ctx.fillStyle = randomColor(230, 255)
  ctx.fillRect(0, 0, canvasSetting.width, canvasSetting.height)

  for (let i = 0; i < canvasSetting.size; i++) {
    let currentText = '' + canvasSetting.pool[randomNum(0, canvasSetting.pool.length)]
    codeText.value += currentText
    ctx.font = '36px Simhei'
    ctx.textAlign="center"
    ctx.fillStyle = randomColor(80, 150)
    ctx.fillText(currentText, (i + 1) * randomNum(20, 25), canvasSetting.height / 2 + 13)
  }

  for (let i = 0; i < 5; i++) {
    ctx.beginPath()
    ctx.moveTo(randomNum(0, canvasSetting.width), randomNum(0, canvasSetting.height))
    ctx.lineTo(randomNum(0, canvasSetting.width), randomNum(0, canvasSetting.height))
    ctx.strokeStyle = randomColor(180, 230)
    ctx.closePath()
    ctx.stroke()
  }

  for (let i = 0; i < 40; i++) {
    ctx.beginPath()
    ctx.arc(randomNum(0, canvasSetting.width), randomNum(0, canvasSetting.height), 1, 0, 2 * Math.PI)
    ctx.closePath()
    ctx.fillStyle = randomColor(150, 200)
    ctx.fill()
  }

  ctx.restore()
  ctx.save()

  return codeText
}

onMounted(() => {
  generateCode()
})

const refresh = () => {
  generateCode()
}

defineExpose({ checkResult, refresh })
</script>

<template>
  <el-tooltip content="点击切换验证码">
    <canvas
      ref="verfiyCanvas"
      class="canvas"
      :width="canvasSetting.width"
      :height="canvasSetting.height"
      @click="refresh"
    />
  </el-tooltip>
</template>

<style scoped lang="scss">
:deep(.arco-input-append){
  padding: 0 !important;
}
.canvas {
  cursor: pointer;
}
</style>
