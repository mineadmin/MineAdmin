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
import { useLocalTrans } from '@/hooks/useLocalTrans.ts'

const storeMeta = inject('storeMeta') as Record<string, any>
const filterParams = inject('storeMeta') as Record<string, any>

const t = useLocalTrans()
const route = useRoute()
const dayjs = useDayjs(null, true) as any

function getData() {
  return Array.from({ length: 12 }).fill(0).map((_item, index) => ({
    name: '测试插件',
    homepage: [`https://picsum.photos/600/240?random=${index}`],
    tags: [{ name: '官方应用', color: 'red' }],
    created_at: dayjs().format('YYYY-MM-DD HH:mm:ss'),
    created_by: 'X.Mo',
    auth: { type: 0 },
    description: '一个应用插件',
  }))
}

onMounted(() => {
  if (route.query?.install) {
    const installQuery = route.query.install.split('/')[1] ?? undefined
    if (installQuery) {
      openDetailModal({ identifier: installQuery })
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
      v-for="(item, idx) in getData()"
      class="border-gray-150 hover:border-primary-500 group relative top-0 mt-8 h-auto overflow-hidden border rounded-md transition-all duration-300 sm:mt-0 dark:border-gray-600 dark:shadow-gray-600 hover:shadow-md hover:-top-1 dark:hover:border-gray-400"
    >
      <a class="h-44 w-full" href="javascript:">
        <div class="relative">
          <el-image
            class="pointer-events-none h-48 w-full transform object-cover transition-transform duration-200 group-hover:scale-105 sm:rounded-md !rounded-b-none !rounded-t-md"
            :src="item.homepage[0]"
          >
            <div class="absolute bottom-2 right-2 space-x-2">
              <el-tag v-for="tag in item.tags" :color="tag.color">{{ tag.name }}</el-tag>
            </div>
          </el-image></div>
      </a>
      <div class="p-3 pb-2">
        <div class="grid grid-cols-2">
          <div class="text-sm">
            <a class="hover:underline" href="javascript:">{{ item.name }}</a>
          </div>
          <div class="text-right text-xs text-gray-500 leading-5 dark:text-gray-400">
            {{ `${dayjs(item.created_at).fromNow()}更新` }}
          </div>
        </div>
        <div class="mt-1 truncate text-xs text-gray-500 dark:text-gray-400">
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
  </div>
</template>

<style scoped lang="scss">

</style>
