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
  appstore: 应用商店
  noDevMainTitle: 应用商店无法使用
  noDevSubTitle: 对不起，应用商店只在开发环境开放使用。
  noTokenMainTitle: 未设置 MINE_ACCESS_TOKEN
  noTokenSubTitle: 目前无法获取到应用商店数据，请先设置 MINE_ACCESS_TOKEN。
  checkingTokenMainTitle: 正在检查 MINE_ACCESS_TOKEN
  checkingTokenSubTitle: 正在验证访问令牌，请稍候...
  refreshAppStore: 刷新应用商店
  notFoundApp: 没有找到任何应用
zh_TW:
  appstore: 應用商店
  noDevMainTitle: 應用商店無法使用
  noDevSubTitle: 對不起，應用商店僅在開發環境開放使用。
  noTokenMainTitle: 未設置 MINE_ACCESS_TOKEN
  noTokenSubTitle: 目前無法獲取到應用商店數據，請先設置 MINE_ACCESS_TOKEN。
  checkingTokenMainTitle: 正在檢查 MINE_ACCESS_TOKEN
  checkingTokenSubTitle: 正在驗證訪問令牌，請稍候...
  refreshAppStore: 刷新應用商店
  notFoundApp: 沒有找到任何應用
en:
  appstore: App Store
  noDevMainTitle: App Store is not available
  noDevSubTitle: Sorry, the App Store is only open for use in the development environment.
  noTokenMainTitle: MINE_ACCESS_TOKEN is not set
  noTokenSubTitle: Currently unable to retrieve App Store data. Please set MINE_ACCESS_TOKEN first.
  checkingTokenMainTitle: Checking MINE_ACCESS_TOKEN
  checkingTokenSubTitle: Verifying access token, please wait...
  refreshAppStore: Refresh App Store
  notFoundApp: No apps found
</i18n>

<script setup lang="ts">
import { useLocalTrans } from '@/hooks/useLocalTrans.ts'
import { getAppList, getLocalAppInstallList, getPayApp, hasAccessToken } from '../api/app.ts'
import AppStoreNotice from './notice.vue'
import AppStoreFilter from './filter.vue'
import AppStoreList from './list.vue'
import AppStoreLocalList from './localList.vue'

defineOptions({ name: 'MineAppStoreRoute' })

const tabStore = useTabStore()
const t = useLocalTrans()
const noticeRef = ref()
const appList = ref<any[]>()
const storeMeta = ref<Record<string, any>>({
  isDev: import.meta.env.DEV,
  isHasAccessToken: false,
  tokenCheckCompleted: false,
  uploadLoading: false,
  loading: false,
  allStore: true,
  total: 0,
})
const dataList = ref<Record<string, any[]>>({
  my: [],
  local: [],
  list: [],
})

const filterParams = ref<Record<string, any>>({
  page: 1,
  size: 9999,
  keywords: undefined,
  add_type: undefined,
  type: undefined,
  tag: undefined,
})

function requestAppList(params = { page: 1, size: 9999, created_at_desc: true }) {
  const requestParams = Object.assign(filterParams.value, params)
  storeMeta.value.loading = true
  getAppList(requestParams).then((res: any) => {
    if (res.code === 200) {
      const { list, rowTotal } = res.data?.data
      dataList.value.list = list
      storeMeta.value.total = rowTotal
      storeMeta.value.loading = false
    }
  })
}

if (storeMeta.value.isDev) {
  hasAccessToken().then((res: any) => {
    if (res.code === 200) {
      storeMeta.value.isHasAccessToken = res.data.isHas
      storeMeta.value.tokenCheckCompleted = true
      if (!res.data.isHas) {
        noticeRef.value?.open?.()
      }
      else {
        requestAppList()
        getPayApp().then((res: any) => {
          if (res.code === 200) {
            dataList.value.my = res.data
          }
        })
        getLocalAppInstallList().then((res: any) => {
          if (res.code === 200) {
            dataList.value.local = res.data
          }
        })
      }
    }
  })
}

provide('storeMeta', storeMeta)
provide('filterParams', filterParams)
provide('dataList', dataList)
provide('appList', appList)
provide('requestAppList', requestAppList)
</script>

<template>
  <div class="mine-layout relative top-0">
    <AppStoreNotice ref="noticeRef" />
    <template v-if="storeMeta.isDev && storeMeta.isHasAccessToken">
      <AppStoreFilter />
      <AppStoreList v-if="dataList.list.length > 0 && storeMeta.allStore" />
      <AppStoreLocalList v-if="!storeMeta.allStore" />
      <el-empty v-if="dataList.list.length === 0 && !storeMeta.loading" class="mt-40" :description="t('notFoundApp')" />
    </template>
    <el-result v-if="!storeMeta.isDev && !storeMeta.loading" class="h-680px" icon="warning" :title="t('noDevMainTitle')"
      :sub-title="t('noDevSubTitle')" />
    <el-result v-if="storeMeta.isDev && !storeMeta.tokenCheckCompleted && !storeMeta.loading" class="h-680px"
      :title="t('checkingTokenMainTitle')" :sub-title="t('checkingTokenSubTitle')">
      <template #icon>
        <el-icon :size="75">
          <ma-svg-icon name="svg-spinners:180-ring" />
        </el-icon>
      </template>
    </el-result>
    <el-result
      v-if="storeMeta.isDev && storeMeta.tokenCheckCompleted && !storeMeta.isHasAccessToken && !storeMeta.loading"
      class="h-680px" icon="error" :title="t('noTokenMainTitle')" :sub-title="t('noTokenSubTitle')">
      <template #extra>
        <el-button type="primary" @click="tabStore.refreshTab()">
          {{ t('refreshAppStore') }}
        </el-button>
      </template>
    </el-result>
  </div>
</template>

<style scoped lang="scss">
.mine-appstore-filter {
  @apply sticky top-[56px] z-999 bg-white p-3 dark-bg-dark-8 shadow-md dark-shadow-dark-4 transition-all duration-300;
}

.mine-appstore-list {
  @apply sm:grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 min-h-60 gap-4 mt-3 relative;
}
</style>
