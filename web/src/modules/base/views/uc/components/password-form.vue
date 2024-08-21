<i18n lang="yaml">
en:
  changeInfo:
    oldPasswordLabel: Old password
    newPasswordLabel: New password
    newPassword_confirmationLabel: Confirm password
    oldPasswordPlaceholder: please input old password
    newPasswordPlaceholder: please input new password
    newPassword_confirmationPlaceholder: please input confirm password
zh_CN:
  changeInfo:
    oldPasswordLabel: 旧密码
    newPasswordLabel: 新密码
    newPassword_confirmationLabel: 确认密码
    oldPasswordPlaceholder: 请输入旧密码
    newPasswordPlaceholder: 请输入新密码
    newPassword_confirmationPlaceholder: 请输入确认密码
zh_TW:
  changeInfo:
    oldPasswordLabel: 舊密碼
    newPasswordLabel: 新密碼
    newPassword_confirmationLabel: 確認密碼
    oldPasswordPlaceholder: 請輸入舊密碼
    newPasswordPlaceholder: 請輸入新密碼
    newPassword_confirmationPlaceholder: 請輸入確認密碼
</i18n>

<script setup lang="ts">
import type { Ref } from 'vue'
import { useLocalTrans } from '@/hooks/useLocalTrans.ts'

// const userStore = useUserStore()
const isFormSubmit = inject('isFormSubmit') as Ref

const form = reactive({
  oldPassword: '',
  newPassword: '',
  newPassword_confirmation: '',
})

async function submit() {
  isFormSubmit.value = true
}
</script>

<template>
  <form class="mine-form" @submit.prevent="submit">
    <div class="mine-form-item">
      <div class="mine-form-item-title">
        {{ useLocalTrans('changeInfo.oldPasswordLabel') }}
      </div>
      <m-input
        v-model="form.oldPassword"
        name="oldPassword"
        :placeholder="useLocalTrans('changeInfo.oldPasswordPlaceholder')"
      />
    </div>
    <div class="mine-form-item">
      <div class="mine-form-item-title">
        {{ useLocalTrans('changeInfo.newPasswordLabel') }}
      </div>
      <m-input
        v-model="form.newPassword"
        :placeholder="useLocalTrans('changeInfo.newPasswordPlaceholder')"
      />
    </div>
    <div class="mine-form-item">
      <div class="mine-form-item-title">
        {{ useLocalTrans('changeInfo.newPassword_confirmationLabel') }}
      </div>
      <m-input
        v-model="form.newPassword_confirmation"
        :placeholder="useLocalTrans('changeInfo.newPassword_confirmationPlaceholder')"
      />
    </div>
  </form>
</template>

<style scoped lang="scss">
.mine-form-item-title {
  @apply text-left text-sm mt-3 mb-1.5;
}
</style>
