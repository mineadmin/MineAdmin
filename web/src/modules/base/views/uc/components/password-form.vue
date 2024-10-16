<i18n lang="yaml">
en:
  changeInfo:
    oldPasswordLabel: Old password
    newPasswordLabel: New password
    newPassword_confirmationLabel: Confirm password
    oldPasswordPlaceholder: please input old password
    newPasswordPlaceholder: please input new password
    newPassword_confirmationPlaceholder: please input confirm password
    newPasswordLength: The password needs to be at least 8 digits
    passwordsAreInconsistent: The new password is inconsistent with the old password
zh_CN:
  changeInfo:
    oldPasswordLabel: 旧密码
    newPasswordLabel: 新密码
    newPassword_confirmationLabel: 确认密码
    oldPasswordPlaceholder: 请输入旧密码
    newPasswordPlaceholder: 请输入新密码
    newPassword_confirmationPlaceholder: 请输入确认密码
    newPasswordLength: 密码至少需要8位
    passwordsAreInconsistent: 新密码与旧密码不一致
zh_TW:
  changeInfo:
    oldPasswordLabel: 舊密碼
    newPasswordLabel: 新密碼
    newPassword_confirmationLabel: 確認密碼
    oldPasswordPlaceholder: 請輸入舊密碼
    newPasswordPlaceholder: 請輸入新密碼
    newPassword_confirmationPlaceholder: 請輸入確認密碼
    newPasswordLength: 密碼至少需要8位
    passwordsAreInconsistent: 新密碼與舊密碼不一致
</i18n>

<script setup lang="ts">
import { useLocalTrans } from '@/hooks/useLocalTrans.ts'
import Message from 'vue-m-message'

const emit = defineEmits<{
  (event: 'submit', value: any)
}>()

const t = useLocalTrans()

const form = reactive({
  oldPassword: '',
  newPassword: '',
  newPassword_confirmation: '',
})

const isValidState = ref(true)

function easyValidate(event: Event) {
  const dom = event?.target as HTMLInputElement
  if (form[dom.name] === undefined || form[dom.name] === '') {
    dom.classList.add('!ring-red-5')
    Message.error(t(`changeInfo.${dom.name}Placeholder`), { zIndex: 9999 })
    isValidState.value = false
  }
  else {
    dom.classList.remove('!ring-red-5')
    isValidState.value = true
  }
}

async function submit() {
  Object.keys(form).forEach((key) => {
    if (form[key] === undefined || form[key] === '') {
      Message.error(t(`changeInfo.${key}Placeholder`), { zIndex: 9999 })
      isValidState.value = false
    }
  })
  if (!isValidState.value) {
    return false
  }

  if (form.newPassword.length < 8) {
    Message.error(t('changeInfo.newPasswordLength'), { zIndex: 9999 })
  }

  if (form.newPassword !== form.newPassword_confirmation) {
    Message.error(t('changeInfo.passwordsAreInconsistent'), { zIndex: 9999 })
  }

  emit('submit', { form, type: 'password' })
}

defineExpose({ submit })
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
        @blur="easyValidate"
      />
    </div>
    <div class="mine-form-item">
      <div class="mine-form-item-title">
        {{ useLocalTrans('changeInfo.newPasswordLabel') }}
      </div>
      <m-input
        v-model="form.newPassword"
        name="newPassword"
        :placeholder="useLocalTrans('changeInfo.newPasswordPlaceholder')"
        @blur="easyValidate"
      />
    </div>
    <div class="mine-form-item">
      <div class="mine-form-item-title">
        {{ useLocalTrans('changeInfo.newPassword_confirmationLabel') }}
      </div>
      <m-input
        v-model="form.newPassword_confirmation"
        name="newPassword_confirmation"
        :placeholder="useLocalTrans('changeInfo.newPassword_confirmationPlaceholder')"
        @blur="easyValidate"
      />
    </div>
  </form>
</template>

<style scoped lang="scss">
.mine-form-item-title {
  @apply text-left text-sm mt-3 mb-1.5;
}
</style>
