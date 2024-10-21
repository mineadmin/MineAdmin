<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<script setup lang="ts">
const {
  getMobileState,
  isMixedLayout,
  getUserBarState,
  getSettings,
} = useSettingStore()

const storeMeta = inject('storeMeta') as Record<string, any>
const filterParams = inject('filterParams') as Record<string, any>

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

const tabsOptions = reactive([
  { label: '全部应用', value: true, icon: 'i-material-symbols:done-all-rounded' },
  { label: '本地应用', value: false, icon: 'i-material-symbols:local-library-outline-rounded' },
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
</template>

<style scoped lang="scss">

</style>
