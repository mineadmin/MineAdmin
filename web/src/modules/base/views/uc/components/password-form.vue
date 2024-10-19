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
    old_passwordLabel: Old password
    new_passwordLabel: New password
    new_password_confirmationLabel: Confirm password
    old_passwordPlaceholder: please input old password
    new_passwordPlaceholder: please input new password
    new_password_confirmationPlaceholder: please input confirm password
    new_passwordLength: The password needs to be at least 8 digits
    passwordsAreInconsistent: The new password is inconsistent with the old password
zh_CN:
  changeInfo:
    old_passwordLabel: 旧密码
    new_passwordLabel: 新密码
    new_password_confirmationLabel: 确认密码
    old_passwordPlaceholder: 请输入旧密码
    new_passwordPlaceholder: 请输入新密码
    new_password_confirmationPlaceholder: 请输入确认密码
    new_passwordLength: 密码至少需要8位
    passwordsAreInconsistent: 新密码与旧密码不一致
zh_TW:
  changeInfo:
    old_passwordLabel: 舊密碼
    new_passwordLabel: 新密碼
    new_password_confirmationLabel: 確認密碼
    old_passwordPlaceholder: 請輸入舊密碼
    new_passwordPlaceholder: 請輸入新密碼
    new_password_confirmationPlaceholder: 請輸入確認密碼
    new_passwordLength: 密碼至少需要8位
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
  old_password: '',
  new_password: '',
  new_password_confirmation: '',
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

  if (form.new_password.length < 8) {
    Message.error(t('changeInfo.new_passwordLength'), { zIndex: 9999 })
  }

  if (form.new_password !== form.new_password_confirmation) {
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
        {{ useLocalTrans('changeInfo.old_passwordLabel') }}
      </div>
      <m-input
        v-model="form.old_password"
        type="password"
        name="old_password"
        :placeholder="useLocalTrans('changeInfo.old_passwordPlaceholder')"
        @blur="easyValidate"
      />
    </div>
    <div class="mine-form-item">
      <div class="mine-form-item-title">
        {{ useLocalTrans('changeInfo.new_passwordLabel') }}
      </div>
      <m-input
        v-model="form.new_password"
        type="password"
        name="new_password"
        :placeholder="useLocalTrans('changeInfo.new_passwordPlaceholder')"
        @blur="easyValidate"
      />
    </div>
    <div class="mine-form-item">
      <div class="mine-form-item-title">
        {{ useLocalTrans('changeInfo.new_password_confirmationLabel') }}
      </div>
      <m-input
        v-model="form.new_password_confirmation"
        type="password"
        name="new_password_confirmation"
        :placeholder="useLocalTrans('changeInfo.new_password_confirmationPlaceholder')"
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
