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
  pluginNotExists: '要安装的插件：%{name} 不存在'
zh_TW:
  pluginNotExists: '要安裝的外掛程式：%{name} 不存在'
en:
  pluginNotExists: 'Plugin to install：%{name} Does not exist'
</i18n>

<script setup lang="ts">
import AppStoreDetail from './detail.vue'
import { useLocalTrans } from '@/hooks/useLocalTrans.ts'
import { useMessage } from '@/hooks/useMessage.ts'
import discount from '../utils/discount.ts'

const storeMeta = inject('storeMeta') as Record<string, any>
const dataList = inject('dataList') as Record<string, any>

const t = useLocalTrans()
const msg = useMessage()
const detailRef = ref()
const route = useRoute()
const dayjs = useDayjs(null, true) as any

function openDetail(identifier: string) {
  detailRef.value.open(identifier)
}

onMounted(() => {
  if (route.query?.install) {
    const installQuery: string | undefined = (route.query.install as string)?.split('/')[1] ?? undefined
    if (installQuery) {
      openDetail(installQuery)
    }
    else {
      msg.alertError(`${t('pluginNotExists', { name: route.query.install })}`)
    }
  }
})
</script>

<template>
  <div v-loading="storeMeta.loading" class="mine-card mine-appstore-list">
    <div
      v-for="item in dataList.list"
      class="appstore-item"
    >
      <a class="h-44 w-full" href="javascript:" @click="openDetail(item.identifier)">
        <div class="relative">
          <el-image
            class="appstore-item-image"
            :src="item.homepage[0]"
          >
            <div class="absolute bottom-2 right-2 space-x-2">
              <el-tag v-for="(tag, index) in item.tags" :key="index" :color="tag.color">{{ tag.name }}</el-tag>
            </div>
          </el-image></div>
      </a>
      <div class="p-3 pb-2">
        <div class="flex items-center justify-between">
          <div class="text-sm">
            <el-link type="primary" @click="openDetail(item.identifier)">
              {{ item.name }}
            </el-link>
          </div>
          <div class="text-right text-xs text-gray-500 leading-5 dark:text-gray-400">
            {{ `${dayjs(item.created_at).fromNow()}更新` }}
          </div>
        </div>
        <div class="dark:text-dark-0.5 mt-1 truncate text-xs text-gray-500">
          {{ item?.description }}
        </div>
        <div class="grid grid-cols-2 mt-5 text-xs">
          <div class="text-gray-700 leading-6 dark:text-gray-300">
            <span class="hover:underline">{{ item.created_by }}</span>
          </div>
          <div class="text-right">
            <el-tag v-if="item.auth.type === 0" type="primary">
              免费
            </el-tag>
            <div v-else-if="item.auth.type === 1">
              <div class="flex items-center justify-end leading-6">
                <div v-if="item.auth?.integral_discount !== '0.00'" class="text-gray-400 line-through">
                  {{ item.auth?.integral_quota }} 积分
                </div>
                <div class="ml-2 text-emerald-700">
                  {{ discount(item.auth?.integral_discount, item.auth?.integral_quota) }} 积分
                </div>
              </div>
            </div>
            <div v-else-if="item.auth.type === 2">
              <div class="flex items-center justify-end leading-6">
                <div v-if="item.auth?.basic_discount !== '0.00'" class="text-gray-400 line-through">
                  ￥{{ item.auth?.basic_quota }}
                </div>
                <div class="ml-2 text-blue-600">
                  ￥{{ discount(item.auth?.basic_discount, item.auth?.basic_quota) }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <AppStoreDetail ref="detailRef" />
  </div>
</template>

<style scoped lang="scss">
.appstore-item {
  @apply b-gray-2 hover:b-[rgb(var(--ui-primary))]
  group b-1 b-solid
  relative top-0 mt-8 h-auto overflow-hidden border rounded-md transition-all duration-300
  sm:mt-0 dark:border-dark-400 dark:shadow-dark-300 hover:shadow-md hover:-top-1 dark:hover:b-[rgb(var(--ui-primary)/.95)];
}
.appstore-item-image {
  @apply pointer-events-none h-48 w-full transform object-cover dark-brightness-[0.9]
  transition-transform duration-200 group-hover:scale-105 sm:rounded-md !rounded-b-none !rounded-t-md;
}
</style>
