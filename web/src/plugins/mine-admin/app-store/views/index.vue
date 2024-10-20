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

defineOptions({ name: 'MineAppStoreRoute' })

const dayjs = useDayjs(null, true) as any
const storeMeta = ref <Record<string, any>>({
  isDev: import.meta.env.DEV,
  isHasAccessToken: false,
  loading: false,
  allStore: true,
})

const filterParams = reactive({
  page: 1,
  size: 9999,
  keywords: undefined,
  add_type: 'all',
  type: 'all',
  tag: undefined,
})

const paramsList = reactive({
  addType: [
    { label: '全部', value: 'all' },
    { label: '完整应用', value: 'mixed' },
    { label: '后端应用', value: 'backend' },
    { label: '前端应用', value: 'frontend' },
  ],
  types: [
    { label: '全部', value: 'all' },
    { label: '免费应用', value: '0' },
    { label: '积分应用', value: '1' },
    { label: '付费应用', value: '2' },
  ],
  keywords: undefined,
})

function requestAppList(params = { page: 1, size: 9999 }) {
}

function filterRequest(name: string, item: any) {
  if (filterParams[name] === item.value) {
    return true
  }
  filterParams[name] = item.value
  requestAppList()
}

provide('storeMeta', storeMeta)

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

const t = useLocalTrans()
</script>

<template>
  <div class="mine-layout relative top-0">
    <div class="sticky top-[56px] z-999 bg-white p-3 md:top-97px dark-bg-dark-8">
      <div class="flex justify-between gap-x-6">
        <m-tabs
          v-model="storeMeta.allStore"
          class="h-[30px] max-w-full text-sm md:max-w-[308px] md:min-w-[200px]"
          :options="[
            { label: '全部应用', value: true, icon: 'i-material-symbols:done-all-rounded' },
            { label: '本地应用', value: false, icon: 'i-material-symbols:local-library-outline-rounded' },
          ]"
        />
        <el-alert
          class="hidden w-9/12 md:flex"
          type="success"
          :closable="false"
        >
          这里是 MineAdmin 官方应用市场，您可以在此页面放心下载喜欢的应用。注意：只有开发环境才能使用应用市场
        </el-alert>
      </div>
      <div class="mt-2 justify-between gap-x-2 rounded-md md:flex">
        <div>
          <el-space>
            <el-button>
              <ma-svg-icon name="i-ri:refresh-line" :size="16" class="mr-1" />
              刷新
            </el-button>
            <el-upload :show-file-list="false" accept=".zip,.rar">
              <el-button>
                <ma-svg-icon name="i-line-md:uploading-loop" :size="18" class="mr-1" />
                本地上传
              </el-button>
            </el-upload>
            <el-button status="success">
              <ma-svg-icon name="i-material-symbols:account-circle-outline" :size="18" class="mr-1" />
              个人信息
            </el-button>
          </el-space>
        </div>
        <div class="mt-2 flex items-center gap-x-3 md:mt-0">
          <el-select
            v-model="filterParams.add_type"
            class="w-150px"
          >
            <el-option v-for="item in paramsList.addType" :value="item.value" :label="item.label" />
            <template #label="{ label }">
              类型：{{ label }}
            </template>
          </el-select>
          <el-select
            v-model="filterParams.type"
            class="w-150px"
          >
            <el-option v-for="item in paramsList.types" :value="item.value" :label="item.label" />
            <template #label="{ label }">
              价格：{{ label }}
            </template>
          </el-select>
          <el-input
            placeholder="搜索应用..."
            class="w-150px"
          />
        </div>
      </div>
    </div>

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
  </div>
</template>

<style scoped lang="scss">
.mine-appstore-list {
  @apply sm:grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 min-h-60 gap-4 mt-3 relative;
}
</style>
