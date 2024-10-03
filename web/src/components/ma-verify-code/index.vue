<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<i18n lang="yaml">
zh_CN:
  MaVerifyCodeError: 验证码错误
  MaVerifyCodeNullError: 验证码不能为空
zh_TW:
  MaVerifyCodeError: 驗證碼錯誤
  MaVerifyCodeNullError: 驗證碼不能為空
en:
  MaVerifyCodeError: Verification code error
  MaVerifyCodeNullError: The verification code cannot be empty
</i18n>

<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import Message from 'vue-m-message'

defineOptions({ name: 'MaVerifyCode' })

const props = withDefaults(
  defineProps<{
    height?: number
    width?: number
    pool?: string
    size?: number
    showError?: boolean
  }>(),
  {
    height: 36,
    width: 120,
    pool: 'abcdefghjkmnpqrstuvwxyz23456789',
    size: 4,
    showError: true,
  },
)
const { t } = useI18n({
  inheritLocale: true,
  useScope: 'local',
})
const codeText = ref('')
const el = ref()

function checkResult(verifyCode: string) {
  if ((!verifyCode || verifyCode.length === 0) && props.showError) {
    Message.error(t('MaVerifyCodeNullError'))
    return false
  }

  if ((verifyCode.toLowerCase() !== codeText.value.toLowerCase()) && props.showError) {
    Message.error(t('MaVerifyCodeError'))
    generateCode()
    return false
  }
  else {
    return true
  }
}

function randomNum(min: number, max: number) {
  return Number.parseInt((Math.random() * (max - min) + min).toString())
}

function randomColor(min: number, max: number) {
  const r = randomNum(min, max)
  const g = randomNum(min, max)
  const b = randomNum(min, max)
  return `rgb(${r},${g},${b})`
}

function generateCode() {
  codeText.value = ''
  const ctx = el.value.getContext('2d')
  ctx.fillStyle = randomColor(230, 255)
  ctx.fillRect(0, 0, props.width, props.height)

  for (let i = 0; i < props.size; i++) {
    const currentText = `${props.pool[randomNum(0, props.pool.length)]}`
    codeText.value += currentText
    ctx.font = '36px Simhei'
    ctx.textAlign = 'center'
    ctx.fillStyle = randomColor(80, 150)
    ctx.fillText(currentText, (i + 1) * randomNum(20, 25), props.height / 2 + 13)
  }

  for (let i = 0; i < 5; i++) {
    ctx.beginPath()
    ctx.moveTo(randomNum(0, props.width), randomNum(0, props.height))
    ctx.lineTo(randomNum(0, props.width), randomNum(0, props.height))
    ctx.strokeStyle = randomColor(180, 230)
    ctx.closePath()
    ctx.stroke()
  }

  for (let i = 0; i < 40; i++) {
    ctx.beginPath()
    ctx.arc(randomNum(0, props.width), randomNum(0, props.height), 1, 0, 2 * Math.PI)
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

function refresh() {
  generateCode()
}

defineExpose({ checkResult, refresh })
</script>

<template>
  <canvas
    ref="el"
    class="canvas"
    :width="props.width"
    :height="props.height"
    @click="refresh"
  />
</template>

<style scoped lang="scss">
.canvas {
  @apply cursor-pointer rounded;
}
</style>
