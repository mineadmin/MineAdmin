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
import { useLocalTrans } from '@/hooks/useLocalTrans.ts'
import { useThrottleFn } from '@vueuse/core'

const {
  getMobileState,
  isMixedLayout,
  getUserBarState,
  getSettings,
} = useSettingStore()

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
          <el-upload :show-file-list="false" accept=".zip,.rar">
            <el-button>
              <ma-svg-icon name="i-line-md:uploading-loop" :size="18" class="mr-1" />
              {{ t('localUpload') }}
            </el-button>
          </el-upload>
          <el-button status="success">
            <ma-svg-icon name="i-material-symbols:account-circle-outline" :size="18" class="mr-1" />
            {{ t('personalInfo') }}
          </el-button>
        </el-space>
      </div>
      <div class="mt-2 flex items-center gap-x-3 md:mt-0">
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
