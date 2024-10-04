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
  searchPlaceholder: Search for icons under this category
zh_CN:
  searchPlaceholder: 搜索此分类下的图标
zh_TW:
  searchPlaceholder: 搜索此分類下的圖示
</i18n>

<script setup lang="ts">
import { OverlayScrollbarsComponent } from 'overlayscrollbars-vue'
import type { TabPaneName } from 'element-plus'
import data from '@/iconify/data.json'
import { useLocalTrans } from '@/hooks/useLocalTrans.ts'

defineOptions({ name: 'MaIconPanel' })

const { pageSize = 70, className = 'w-full' } = defineProps<{
  pageSize?: number
  className?: string
}>()

const emit = defineEmits<{
  (event: 'select', value: string): void
}>()

const model = defineModel<string>()

const keywords = ref<string>('')
const currentPage = ref<number>(1)
const currentName = ref<TabPaneName>('ant-design')
const currentIconList = ref<string[]>([])
const validIcons = ref<string[]>([])
const pageData = ref<string[]>([])

const t = useLocalTrans()

function getIcons() {
  currentIconList.value = data.filter((item: any) => item.prefix === currentName.value)[0].icons
  validIcons.value = currentIconList.value
}

function handleTabChange(name: TabPaneName) {
  currentPage.value = 1
  currentName.value = name
  getIcons()
  getValidIcons()
  handlePageChange()
}
function getValidIcons() {
  validIcons.value = keywords.value === '' ? currentIconList.value : currentIconList.value.filter((item: string) => item.includes(keywords.value))
}
function handlePageChange() {
  pageData.value = validIcons.value.slice((currentPage.value - 1) * pageSize, currentPage.value * pageSize)
}

watch(keywords, () => {
  currentPage.value = 1
  getValidIcons()
  handlePageChange()
})

function selected(name: string) {
  model.value = name
  emit('select', name)
}

function clear() {
  model.value = ''
}

defineExpose({ clear })

handleTabChange(currentName.value)
</script>

<template>
  <el-tabs v-model="currentName" tab-position="left" class="h-[500px]" :class="className" @tab-change="handleTabChange">
    <div class="pl-1.5 pr-3">
      <el-input v-model="keywords" :placeholder="t('searchPlaceholder')" clearable />
    </div>
    <template v-for="item in data" :key="item.prefix">
      <el-tab-pane :label="item.info.name" :name="item.prefix" class="mt-2">
        <div class="relative">
          <OverlayScrollbarsComponent class="h-[400px] p-3 pl-2 pr-4">
            <div class="icon-content">
              <template v-for="icon in pageData">
                <div
                  class="icon-item" :class="{
                    active: `${item.prefix}:${icon}` === model,
                  }"
                  @click="selected(`${item.prefix}:${icon}`)"
                >
                  <ma-svg-icon :name="`${item.prefix}:${icon}`" :size="26" />
                </div>
              </template>
            </div>
          </OverlayScrollbarsComponent>
        </div>
      </el-tab-pane>
    </template>
    <div class="pagination">
      <el-pagination
        v-model:current-page="currentPage"
        :total="validIcons.length"
        :page-size="pageSize"
        background
        layout="prev, pager, next"
        :pager-count="5"
        @current-change="handlePageChange"
      />
    </div>
  </el-tabs>
</template>

<style scoped lang="scss">
.icon-item {
  @apply flex items-center justify-center;
}

.pagination {
  @apply absolute bottom-0 w-full b-t-1 b-t-solid pt-2 flex justify-end items-end
  bg-white  dark-bg-dark-8
  b-t-gray-2 dark-b-t-dark-5
  ;
}

.icon-content {
  @apply grid grid-cols-10 gap-1;

  .icon-item {
    @apply flex items-center justify-center py-3 rounded-md
    hover-ring-2 hover-ring-[rgb(var(--ui-primary))];
  }

  .icon-item.active {
    @apply rounded-md ring-2 ring-[rgb(var(--ui-primary))];
  }
}
</style>
