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
en:
  changeInfo:
    nicknameLabel: Nick name
    signedLabel: Signed
    nicknamePlaceholder: please input nickname
    signedPlaceholder: please input signed
zh_CN:
  changeInfo:
    nicknameLabel: 昵称
    signedLabel: 个人签名
    nicknamePlaceholder: 请输入昵称
    signedPlaceholder: 请输入个人签名
zh_TW:
  changeInfo:
    nicknameLabel: 昵稱
    signedLabel: 個人簽名
    nicknamePlaceholder: 請輸入昵稱
    signedPlaceholder: 請輸入個人簽名
</i18n>

<script setup lang="ts">
import { useLocalTrans } from '@/hooks/useLocalTrans.ts'

const emit = defineEmits<{
  (event: 'submit', value: any)
}>()

const userStore = useUserStore()

const form = reactive({
  nickname: userStore.getUserInfo().nickname,
  signed: userStore.getUserInfo().signed,
})

async function submit() {
  emit('submit', { form, type: 'userinfo' })
}

defineExpose({ submit })
</script>

<template>
  <form class="mine-form" @submit.prevent="submit">
    <div class="mine-form-item">
      <div class="mine-form-item-title">
        {{ useLocalTrans('changeInfo.nicknameLabel') }}
      </div>
      <m-input
        v-model="form.nickname"
        name="nickname"
        :placeholder="useLocalTrans('changeInfo.nicknamePlaceholder')"
      />
    </div>
    <div class="mine-form-item">
      <div class="mine-form-item-title">
        {{ useLocalTrans('changeInfo.signedLabel') }}
      </div>
      <m-textarea
        v-model="form.signed"
        :placeholder="useLocalTrans('changeInfo.signedPlaceholder')"
      />
    </div>
  </form>
</template>

<style scoped lang="scss">
.mine-form-item-title {
  @apply text-left text-sm mt-3 mb-1.5;
}
</style>
