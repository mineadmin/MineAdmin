<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<script setup lang="ts">
import Message from 'vue-m-message'
import { useI18n } from 'vue-i18n'
import useUserStore from '@/store/modules/useUserStore.ts'
import useSettingStore from '@/store/modules/useSettingStore.ts'

const { t } = useI18n()
const isProduction: boolean = import.meta.env.MODE === 'production'
const userStore = useUserStore()
const settingStore = useSettingStore()
const router = useRouter()
const isFormSubmit = ref(false)
const isValidState = ref(true)
const codeRef = ref()
const form = reactive<{
  username: string
  password: string
  code: string
}>({
  username: isProduction ? '' : 'admin',
  password: isProduction ? '' : '123456',
  code: isProduction ? '' : '1234',
})

function easyValidate(event: Event) {
  const dom = event?.target as HTMLInputElement
  if (form[dom.name] === undefined || form[dom.name] === '') {
    dom.classList.add('!ring-red-5')
    Message.error(t(`loginForm.${dom.name}Placeholder`))
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
      Message.error(t(`loginForm.${key}Placeholder`))
      isValidState.value = false
    }
  })
  if (!isValidState.value) {
    return false
  }

  if (isProduction && !codeRef.value.checkResult(form.code)) {
    form.code = ''
    return false
  }

  isFormSubmit.value = true
  userStore.login(form).then(async (userData: any) => {
    const welcomePath = settingStore.getSettings('welcomePage').path ?? null
    const redirect = router.currentRoute.value.query?.redirect ?? undefined
    if (userData) {
      await router.push({ path: redirect ?? welcomePath ?? '/' })
    }
    isFormSubmit.value = false
  }).catch(() => isFormSubmit.value = false)
}
</script>

<template>
  <form class="mine-login-form" @submit.prevent="submit">
    <div class="mine-login-form-item">
      <div class="mine-login-form-item-title">
        {{ t('loginForm.usernameLabel') }}
      </div>
      <m-input
        v-model="form.username"
        class="!bg-white !text-black !ring-gray-2 !focus-ring-[rgb(var(--ui-primary))] !placeholder-stone-4"
        name="username"
        :placeholder="t('loginForm.usernamePlaceholder')"
        @blur="easyValidate"
      />
    </div>
    <div class="mine-login-form-item">
      <div class="mine-login-form-item-title">
        {{ t('loginForm.passwordLabel') }}
      </div>
      <m-input
        v-model="form.password"
        class="!bg-white !text-black !ring-gray-2 !focus-ring-[rgb(var(--ui-primary))] !placeholder-stone-4"
        name="password"
        type="password"
        :placeholder="t('loginForm.passwordPlaceholder')"
        @blur="easyValidate"
      />
    </div>
    <div v-if="isProduction" class="mine-login-form-item">
      <div class="mine-login-form-item-title">
        {{ t('loginForm.codeLabel') }}
      </div>
      <m-input
        v-model="form.code"
        class="!bg-white !text-black !ring-gray-2 !focus-ring-[rgb(var(--ui-primary))] !placeholder-stone-4"
        name="code"
        :placeholder="t('loginForm.codePlaceholder')"
        @blur="easyValidate"
      >
        <template #suffix>
          <div class="ml-0.5 w-30 flex items-center justify-center text-sm">
            <ma-verify-code ref="codeRef" />
          </div>
        </template>
      </m-input>
    </div>
    <div class="mine-login-form-item mt-2">
      <m-button
        type="submit"
        class="!bg-[rgb(var(--ui-primary))] !text-gray-1 !active-bg-[rgb(var(--ui-primary))] !hover-bg-[rgb(var(--ui-primary)/.75)]"
        :class="{
          // 'py-3': userStore.getLanguage() === 'en',
          loading: isFormSubmit,
        }"
      >
        <ma-svg-icon name="formkit:submit" /> {{ t('loginForm.loginButton') }}
      </m-button>
    </div>
  </form>
</template>

<style scoped lang="scss">
.loading {
  @apply cursor-wait;

  background-color: rgb(var(--ui-primary) / 45%) !important;
}
</style>
