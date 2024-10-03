<i18n lang="yaml">
en:
  changeInfo:
    nicknameLabel: Nick name
    phoneLabel: Phone
    emailLabel: Email
    signedLabel: Signed
    nicknamePlaceholder: please input nickname
    phonePlaceholder: please input phone
    emailPlaceholder: please input email
    signedPlaceholder: please input signed
zh_CN:
  changeInfo:
    nicknameLabel: 昵称
    phoneLabel: 手机
    emailLabel: 邮箱
    signedLabel: 个人签名
    nicknamePlaceholder: 请输入昵称
    phonePlaceholder: 请输入手机
    emailPlaceholder: 请输入邮箱
    signedPlaceholder: 请输入个人签名
zh_TW:
  changeInfo:
    nicknameLabel: 昵稱
    phoneLabel: 手機
    emailLabel: 郵箱
    signedLabel: 個人簽名
    nicknamePlaceholder: 請輸入昵稱
    phonePlaceholder: 請輸入手機
    emailPlaceholder: 請輸入郵箱
    signedPlaceholder: 請輸入個人簽名
</i18n>

<script setup lang="ts">
import type { Ref } from 'vue'
import { useLocalTrans } from '@/hooks/useLocalTrans.ts'

// const userStore = useUserStore()
const isFormSubmit = inject('isFormSubmit') as Ref

const form = reactive({
  nickname: '',
  phone: '',
  email: '',
  signed: '',
})

async function submit() {
  isFormSubmit.value = true
}
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
        {{ useLocalTrans('changeInfo.phoneLabel') }}
      </div>
      <m-input
        v-model="form.phone"
        :placeholder="useLocalTrans('changeInfo.phonePlaceholder')"
      />
    </div>
    <div class="mine-form-item">
      <div class="mine-form-item-title">
        {{ useLocalTrans('changeInfo.emailLabel') }}
      </div>
      <m-input
        v-model="form.email"
        :placeholder="useLocalTrans('changeInfo.emailPlaceholder')"
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
