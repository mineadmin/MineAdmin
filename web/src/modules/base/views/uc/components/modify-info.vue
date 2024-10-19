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
import { useMessage } from '@/hooks/useMessage.ts'

defineOptions({ name: 'UcModifyInfo' })

const msg = useMessage()
const selected = ref('userinfo')
const isFormSubmit = ref(false)
const userinfoFormRef = ref()
const passwordFormRef = ref()

const userStore = useUserStore()
const globalTrans = useTrans().globalTrans

const tabOptions = reactive([
  { label: useLocalTrans('change.info'), value: 'userinfo' },
  { label: useLocalTrans('change.password'), value: 'password' },
])

const state = reactive({
  isOpen: false,
})

function openModal() {
  state.isOpen = true
}

function submit(data: Record<string, any>) {
  isFormSubmit.value = true
  const update = (data: Record<string, any>): Promise<any> => {
    return useHttp().post('/admin/permission/update', data)
  }

  if (data.type === 'userinfo') {
    update(data.form).then(() => {
      msg.success(globalTrans('crud.updateSuccess'))
      userStore.getUserInfo().nickname = data.form.nickname
      userStore.getUserInfo().signed = data.form.signed
      state.isOpen = false
    })
  }

  if (data.type === 'password') {
    update(data.form).then(() => {
      msg.success(globalTrans('crud.updateSuccess'))
      state.isOpen = false
    })
  }
  isFormSubmit.value = false
}

defineExpose({ openModal })
</script>

<template>
  <m-modal
    v-model="state.isOpen"
    :title="useLocalTrans(selected === 'info' ? 'change.info' : 'change.password')"
    content-class="w-[450px] h-[380px]"
  >
    <m-tabs v-model="selected" :options="tabOptions" class="text-sm" />

    <div class="my-5">
      <userinfoForm v-show="selected === 'userinfo'" ref="userinfoFormRef" @submit="submit" />
      <passwordForm v-show="selected === 'password'" ref="passwordFormRef" @submit="submit" />
    </div>
    <template #footer>
      <m-button
        type="submit"
        class="!bg-[rgb(var(--ui-primary))] !text-gray-1 !active-bg-[rgb(var(--ui-primary))] !hover-bg-[rgb(var(--ui-primary)/.75)]"
        :class="{
          loading: isFormSubmit,
        }"
        @click="() => {
          selected === 'userinfo' ? userinfoFormRef.submit() : passwordFormRef.submit()
        }"
      >
        {{ useLocalTrans(selected === 'userinfo' ? 'change.info' : 'change.password') }}
      </m-button>
    </template>
  </m-modal>
</template>

<style scoped lang="scss">

</style>
