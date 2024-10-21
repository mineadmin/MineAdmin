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
zh_TW:
  appstore: 應用商店
en:
  appstore: App store
</i18n>

<script setup lang="ts">
import { useLocalTrans } from '@/hooks/useLocalTrans.ts'
import { hasAccessToken } from '../api/app.ts'
import AppStoreNotice from './notice.vue'
import AppStoreFilter from './filter.vue'
import AppStoreList from './list.vue'

defineOptions({ name: 'MineAppStoreRoute' })

const tabStore = useTabStore()
const t = useLocalTrans()
const noticeRef = ref()
const storeMeta = ref<Record<string, any>>({
  isDev: import.meta.env.DEV,
  isHasAccessToken: false,
  loading: false,
  allStore: true,
})

const filterParams = ref<Record<string, any>>({
  page: 1,
  size: 9999,
  keywords: undefined,
  add_type: 'all',
  type: 'all',
  tag: undefined,
})

if (storeMeta.value.isDev) {
  hasAccessToken().then((res: any) => {
    if (res.code === 200) {
      storeMeta.value.isHasAccessToken = res.data.isHas
      if (!res.data.isHas) {
        noticeRef.value?.open?.()
      }
    }
  })
}

provide('storeMeta', storeMeta)
provide('filterParams', filterParams)
</script>

<template>
  <div class="mine-layout relative top-0">
    <AppStoreNotice ref="noticeRef" />
    <template v-if="storeMeta.isDev && storeMeta.isHasAccessToken">
      <AppStoreFilter />
      <AppStoreList />
    </template>
    <el-result
      v-if="!storeMeta.isDev"
      class="h-680px"
      icon="warning"
      title="应用商店无法使用"
      sub-title="对不起，应用商店只在开发环境开放使用。"
    />
    <el-result
      v-if="!storeMeta.isHasAccessToken"
      class="h-680px"
      icon="error"
      title="未设置 MINE_ACCESS_TOKEN"
      sub-title="目前无法获取到应用商店数据，请先设置 MINE_ACCESS_TOKEN。"
    >
      <template #extra>
        <el-button type="primary" @click="tabStore.refreshTab()">
          刷新应用商店
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
