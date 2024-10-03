<i18n lang="yaml">
en:
  change:
    info: Modify info
    password: Modify password
  changeProfileOrPassword: Modify profile or password
zh_CN:
  change:
    info: 修改信息
    password: 修改密码
  changeProfileOrPassword: 修改个人信息或密码
zh_TW:
  change:
    info: 修改資料
    password: 修改密碼
  changeProfileOrPassword: 修改個人資料或密碼
</i18n>

<script setup lang="ts">
import { useLocalTrans } from '@/hooks/useLocalTrans.ts'
import passwordForm from './password-form.vue'
import userinfoForm from './userinfo-form.vue'

defineOptions({ name: 'UcModifyInfo' })

const selected = ref('info')
const isFormSubmit = ref(false)
provide('isFormSubmit', isFormSubmit)

const tabOptions = reactive([
  { label: useLocalTrans('change.info'), value: 'info' },
  { label: useLocalTrans('change.password'), value: 'password' },
])

const state = reactive({
  isOpen: false,
})

function openModal() {
  state.isOpen = true
}

defineExpose({ openModal })
</script>

<template>
  <m-modal
    v-model="state.isOpen"
    :title="useLocalTrans(selected === 'info' ? 'change.info' : 'change.password')"
    content-class="w-[450px] h-[470px]"
  >
    <m-tabs v-model="selected" :options="tabOptions" class="text-sm" />

    <div class="my-5">
      <userinfoForm v-if="selected === 'info'" />
      <passwordForm v-if="selected === 'password'" />
    </div>
    <template #footer>
      <m-button
        type="submit"
        class="!bg-[rgb(var(--ui-primary))] !text-gray-1 !active-bg-[rgb(var(--ui-primary))] !hover-bg-[rgb(var(--ui-primary)/.75)]"
        :class="{
          loading: isFormSubmit,
        }"
      >
        {{ useLocalTrans(selected === 'info' ? 'change.info' : 'change.password') }}
      </m-button>
    </template>
  </m-modal>
</template>

<style scoped lang="scss">

</style>
