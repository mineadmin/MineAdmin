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
  alert: 这里是 MineAdmin 官方应用市场，您可以在此页面放心下载喜欢的应用。注意：只有开发环境才能使用应用市场
  refresh: 刷新
  localUpload: 本地上传
  localUploadLoading: 应用上传中，请稍后...
  uploadFail: 应用上传失败
  uploadSuccess: 上传成功，是否刷新网页?
  personalInfo: 个人信息
  filterType: 类型
  filterPrice: 价格
  searchPlaceholder: 搜索应用
  all: 全部
  allApp: 全部应用
  localApp: 本地应用
  typeItem:
    mixed: 完整应用
    backend: 后端应用
    frontend: 前端应用
  priceItem:
    free: 免费应用
    integral: 积分应用
    paid: 付费应用
zh_TW:
  alert: 這裡是 MineAdmin 官方應用市場，您可以在此頁面安心下載喜歡的應用。注意：只有開發環境才能使用應用市場
  refresh: 刷新
  localUpload: 本地上傳
  localUploadLoading: 應用程式上傳中，請稍後...
  uploadFail: 應用程式上傳失敗
  uploadSuccess: 上傳成功，是否刷新網頁?
  personalInfo: 個人資訊
  filterType: 類型篩選
  filterPrice: 價格篩選
  searchPlaceholder: 搜尋應用
  all: 全部
  allApp: 全部應用
  localApp: 本地應用
  typeItem:
    mixed: 完整應用
    backend: 後端應用
    frontend: 前端應用
  priceItem:
    free: 免費應用
    integral: 積分應用
    paid: 付費應用
en:
  alert: This is the MineAdmin official app marketplace. You can safely download your favorite apps on this page. Note:The app marketplace is only available in the development environment.
  refresh: Refresh
  localUpload: Upload Locally
  localUploadLoading: Uploading application, please wait...
  uploadFail: Application upload failed
  uploadSuccess: Upload successful, do you want to refresh the page?
  personalInfo: Personal Information
  filterType: Type Filter
  filterPrice: Price Filter
  searchPlaceholder: Search for Apps
  all: All
  allApp: All Apps
  localApp: Local Apps
  typeItem:
    mixed: Full Apps
    backend: Backend Apps
    frontend: Frontend Apps
  priceItem:
    free: Free Apps
    integral: Apps with Integral Payment
    paid: Paid Apps
</i18n>

<script setup lang="ts">
import type { UploadRequestOptions } from 'element-plus'
import { uploadLocalApp } from '../api/app.ts'
import { useLocalTrans } from '@/hooks/useLocalTrans.ts'
import { useThrottleFn } from '@vueuse/core'
import { useMessage } from '@/hooks/useMessage.ts'

const {
  getMobileState,
  isMixedLayout,
  getUserBarState,
  getSettings,
} = useSettingStore()

const msg = useMessage()
const storeMeta = inject('storeMeta') as Record<string, any>
const filterParams = inject('filterParams') as Record<string, any>
const requestAppList = inject('requestAppList') as () => void
const t = useLocalTrans()
const params = reactive({
  add_type: 'all',
  type: 'all',
  keywords: undefined,
})

const paramsOptions = reactive({
  addType: [
    { label: 'all', value: 'all' },
    { label: 'typeItem.mixed', value: 'mixed' },
    { label: 'typeItem.backend', value: 'backend' },
    { label: 'typeItem.frontend', value: 'frontend' },
  ],
  types: [
    { label: 'all', value: 'all' },
    { label: 'priceItem.free', value: '0' },
    { label: 'priceItem.integral', value: '1' },
    { label: 'priceItem.paid', value: '2' },
  ],
})

const tabsOptions = ref([
  { label: () => t('allApp'), value: true, icon: 'i-material-symbols:done-all-rounded' },
  { label: () => t('localApp'), value: false, icon: 'i-material-symbols:local-library-outline-rounded' },
])

const filterClass = computed(() => {
  return {
    'mine-appstore-filter': true,
    '!md:top-[41px]': !getMobileState() && isMixedLayout() && !getUserBarState() && getSettings('tabbar').enable,
    '!md:top-[0px]': !getMobileState() && !getUserBarState() && !getSettings('tabbar').enable,
    '!md:top-[56px]': !getMobileState() && !isMixedLayout() && !getSettings('tabbar').enable,
    '!md:top-[97px]': !getMobileState() && ((isMixedLayout() && getUserBarState() && getSettings('tabbar').enable) || (!isMixedLayout() && getSettings('tabbar').enable)),
  }
})

function go() {
  window?.open('https://www.mineadmin.com/member/setting')
}

function handleUpload(options: UploadRequestOptions): any {
  return new Promise((resolve, reject) => {
    storeMeta.value.uploadLoading = true
    const formData = new FormData()
    formData.append('file', options.file)
    uploadLocalApp(formData).then((res: Record<string, any>) => {
      storeMeta.value.uploadLoading = false
      if (res.code === 200) {
        resolve(res)
      }
      else {
        reject(res)
      }
    }).catch((err) => {
      storeMeta.value.uploadLoading = false
      reject(err)
    })
  })
}

function handleSuccess(res: any) {
  if (res.code === 200) {
    msg.confirm(t('uploadSuccess')).then(() => {
      window.location.reload()
    })
  }
}

function handleError() {
  msg.error(t('uploadFail'))
}

const execSearchKeywords = useThrottleFn(() => {
  filterParams.value.keywords = params.keywords
  requestAppList()
}, 300)

function filterRequest(name: string, value: string) {
  filterParams.value[name] = value === 'all' ? undefined : value
  requestAppList()
}
</script>

<template>
  <div :class="filterClass">
    <div class="flex justify-between gap-x-6">
      <m-tabs
        v-model="storeMeta.allStore"
        class="h-[30px] max-w-full text-sm md:max-w-[308px] md:min-w-[200px]"
        :options="tabsOptions"
      />
      <el-alert
        class="hidden w-9/12 md:flex"
        type="success"
        :closable="false"
      >
        {{ t('alert') }}
      </el-alert>
    </div>
    <div class="mt-2 justify-between gap-x-2 rounded-md md:flex">
      <div>
        <el-space>
          <el-button @click="requestAppList">
            <ma-svg-icon name="i-ri:refresh-line" :size="16" class="mr-1" />
            {{ t('refresh') }}
          </el-button>
          <el-upload
            :show-file-list="false"
            accept=".zip,.rar"
            :http-request="handleUpload"
            :on-success="handleSuccess"
            :on-error="handleError"
            :disabled="storeMeta.uploadLoading"
          >
            <el-button :type="storeMeta.uploadLoading ? 'primary' : 'default'">
              <ma-svg-icon name="i-line-md:uploading-loop" :size="18" class="mr-1" :disbaled="storeMeta.uploadLoading" :loading="storeMeta.uploadLoading" />
              {{ t(storeMeta.uploadLoading ? 'localUploadLoading' : 'localUpload') }}
            </el-button>
          </el-upload>
          <el-button status="success" @click="go">
            <ma-svg-icon name="i-material-symbols:account-circle-outline" :size="18" class="mr-1" />
            {{ t('personalInfo') }}
          </el-button>
        </el-space>
      </div>
      <div v-if="storeMeta.allStore" class="mt-2 flex items-center gap-x-3 md:mt-0">
        <el-select
          v-model="params.add_type"
          class="w-150px"
          @change="(v) => filterRequest('add_type', v)"
        >
          <el-option v-for="item in paramsOptions.addType" :key="item.value" :value="item.value" :label="t(item.label)" />
          <template #label="{ label }">
            {{ t('filterType') }}：{{ label }}
          </template>
        </el-select>
        <el-select
          v-model="params.type"
          class="w-150px"
          @change="(v) => filterRequest('type', v)"
        >
          <el-option v-for="item in paramsOptions.types" :key="item.value" :value="item.value" :label="t(item.label)" />
          <template #label="{ label }">
            {{ t('filterPrice') }}：{{ label }}
          </template>
        </el-select>
        <el-input
          v-model="params.keywords"
          :placeholder="t('searchPlaceholder')"
          class="w-150px"
          @input="execSearchKeywords"
        />
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">

</style>
