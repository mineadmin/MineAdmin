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
  profile: Profile
  setting: Account setting
  changeAvatar: Change avatar
  removeAvatar: Remove avatar
  changeProfileOrPassword: Modify profile or password
  explain: Explain
  action: Action
  whetherReceiveMsg: Whether receive message
  whetherMultiDeviceLogin: Whether multi-device login
  userinfo:
    nickname: Nick name
    username: Account
    avatar: Avatar
    signed: Signed
    email: Email
    phone: Phone
    loginIp: Login ip
    loginTime: Login Datetime
zh_CN:
  profile: 个人资料
  setting: 账号设置
  changeAvatar: 修改头像
  removeAvatar: 移除头像
  changeProfileOrPassword: 修改个人信息或密码
  explain: 说明
  action: 动作
  whetherReceiveMsg: 是否接收消息
  whetherMultiDeviceLogin: 是否多设备登录
  userinfo:
    nickname: 昵称
    username: 账号
    avatar: 头像
    signed: 个人签名
    email: 邮箱
    phone: 手机
    loginIp: 登录IP
    loginTime: 登录时间
zh_TW:
  profile: 個人資料
  setting: 賬號設置
  changeAvatar: 修改頭像
  removeAvatar: 移除頭像
  changeProfileOrPassword: 修改個人資料或密碼
  explain: 説明
  action: 動作
  whetherReceiveMsg: 是否接受消息
  whetherMultiDeviceLogin: 是否多設備登錄
  userinfo:
    nickname: 昵稱
    username: 賬號
    avatar: 頭像
    signed: 個人簽名
    email: 郵箱
    phone: 手機
    loginIp: 登錄IP
    loginTime: 登錄時間
</i18n>

<script setup lang="ts">
import UcTitle from './components/title.vue'
import UcContainer from './components/container.vue'
import UcModifyInfo from './components/modify-info.vue'
import { useLocalTrans } from '@/hooks/useLocalTrans'

const modalRef = ref()
const selected = ref('profile')
const userStore = useUserStore()
const tabOptions = reactive([
  { label: useLocalTrans('profile'), value: 'profile' },
  { label: useLocalTrans('setting'), value: 'setting' },
])

const form = reactive({
  isReceiveMsg: true,
  multiDeviceLogin: false,
})

const showFields = reactive({
  nickname: useLocalTrans('userinfo.nickname'),
  username: useLocalTrans('userinfo.username'),
  signed: useLocalTrans('userinfo.signed'),
  email: useLocalTrans('userinfo.email'),
  phone: useLocalTrans('userinfo.phone'),
  login_ip: useLocalTrans('userinfo.loginIp'),
  login_time: useLocalTrans('userinfo.loginTime'),
})

const updateImage = () => {
  // Select image
  const input = document.createElement('input')
  input.type = 'file'
  input.accept = 'image/*'
  input.onchange = (e) => {
    const file:File = e.target.files[0]
  }
  input.click()
}

</script>

<template>
  <UcContainer>
    <UcTitle>
      <template #extra>
        <m-button class="h-8" @click="() => modalRef.openModal() ">
          {{ useLocalTrans('changeProfileOrPassword') }}
        </m-button>
      </template>
    </UcTitle>
    <div class="mine-uc-layout-content">
      <div class="w-full">
        <m-tabs
          v-model="selected"
          class="text-sm lg:w-6/12"
          :options="tabOptions"
        />
        <ul v-if="selected === 'profile'" class="info-list">
          <li class="!b-none">
            <div class="desc-item">
              <div class="desc-label">
                <img
                  v-if="userStore.getUserInfo()?.avatar"
                  class="h-30 w-30 rounded-full"
                  :alt="userStore.getUserInfo()?.username"
                  :src="userStore.getUserInfo()?.avatar"
                >
              </div>
              <div class="desc-value">
                <div class="flex flex-col gap-x-5 gap-y-2 lg:flex-row">
                  <a class="cursor-pointer text-gray-7 dark-text-gray-3" @click="updateImage">{{ useLocalTrans('changeAvatar') }}</a>
                  <a class="cursor-pointer text-red-6" v-if="userStore.getUserInfo()?.avatar">{{ useLocalTrans('removeAvatar') }}</a>
                </div>
              </div>
            </div>
          </li>
          <template v-for="(value, key) in userStore.getUserInfo()">
            <li v-if="showFields[key]">
              <div class="desc-item">
                <div class="desc-label">
                  {{ showFields[key] }}
                </div>
                <div class="desc-value">
                  {{ value ?? '-' }}
                </div>
              </div>
            </li>
          </template>
        </ul>

        <ul v-if="selected === 'setting'" class="info-list b-1 b-gray-2 rounded b-solid dark-b-dark-4">
          <li class="bg-gray-1 !b-none dark-bg-dark-5">
            <div class="desc-item">
              <div class="desc-label font-bold">
                {{ useLocalTrans('explain') }}
              </div>
              <div class="desc-value font-bold">
                {{ useLocalTrans('action') }}
              </div>
            </div>
          </li>
          <li>
            <div class="desc-item">
              <div class="desc-label">
                {{ useLocalTrans('whetherReceiveMsg') }}
              </div>
              <div class="desc-value">
                <m-switch v-model="form.isReceiveMsg" />
              </div>
            </div>
          </li>
          <li>
            <div class="desc-item">
              <div class="desc-label">
                {{ useLocalTrans('whetherMultiDeviceLogin') }}
              </div>
              <div class="desc-value">
                <m-switch v-model="form.multiDeviceLogin" />
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <UcModifyInfo ref="modalRef" />
  </UcContainer>
</template>

<style scoped lang="scss">
.info-list {
  @apply w-full mt-3;

  & li {
    @apply b-t-1 b-t-gray-2 b-t-solid pr-2.5 dark-b-t-dark-4 py-4 text-sm hover-bg-gray-50 dark-hover-bg-dark-5;

    .desc-item {
      @apply w-full lg:w-6/12 flex items-center justify-between text-stone-8 dark-text-stone-3;

      .desc-label {
        @apply w-6/12 lg:w-5/12 pl-2 lg:pl-3 truncate;
      }

      .desc-value {
        @apply w-6/12 lg:w-7/12 text-left truncate;
      }
    }
  }
}
</style>
