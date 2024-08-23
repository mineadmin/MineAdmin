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
  searchPlaceholder: 搜索此分类下的资源
zh_TW:
  searchPlaceholder: 搜索此分類下的资源
</i18n>

<script setup lang="ts">
import { OverlayScrollbarsComponent } from 'overlayscrollbars-vue'
import { ElMessage } from 'element-plus'
import { Search } from '@element-plus/icons-vue'
import MTabs from '$/mine-admin/basic-ui/components/tab/index.vue'

interface Resource {
  id: number
  storage_mode: number
  origin_name: string
  object_name: string
  hash: string
  mime_type: string
  storage_path: string
  suffix: string
  size_byte: number
  size_info: string
  url: string
  created_by: number | null
  updated_by: number | null
  created_at: string | null
  updated_at: string | null
  deleted_at: string | null
  remark: string | null
}

const props = withDefaults(defineProps<{
  multiple?: boolean
  limit?: number
}>(), {
  multiple: false,
  limit: undefined,
})

const resourceType = ref([
  { label: '所有', value: '', icon: 'ant-design:appstore-outlined' },
  { label: '图片', value: 'image', icon: 'ant-design:picture-outlined' },
  { label: '文档', value: 'document', icon: 'ant-design:file-text-outlined' },
  { label: '音频', value: 'audio', icon: 'ant-design:audit-outlined' },
  { label: '视频', value: 'video', icon: 'ant-design:video-camera-outlined' },
  { label: '程序', value: 'application', icon: 'ant-design:code-outlined' },
])

const loading = ref(false)
const total = ref<number>(0)
const resourceList = ref<Resource[]>([])
const pathSelected = ref<string[]>([])
const scrollbarRef = ref(null)

const queryParams = ref({
  page: 1,
  pageSize: 40,
  origin_name: '',
  mime_type: '',
})

const skeletonNum = computed(() => {
  return loading.value ? queryParams.value.pageSize : 0
})

function query() {
  resourceList.value = []
  loading.value = true
  return useHttp().get('/mock/attachment/list', { params: { ...queryParams.value } }).then(({ data }) => {
    setTimeout(() => {
      resourceList.value = data.items
      total.value = data.total
      loading.value = false
    }, 3000)
  })
}

watch(queryParams, () => {
  query()
}, { deep: true, immediate: true })
function selectResource(item: string) {
  // 多选
  if (pathSelected.value.includes(item)) {
    pathSelected.value = pathSelected.value.filter(i => i !== item)
  }
  else {
    if (props.multiple) {
      // 判断是否上限
      if (props.limit && pathSelected.value.length >= props.limit) {
        return ElMessage.warning(`最多选择${props.limit}个`)
      }
      pathSelected.value.push(item)
    }
    else {
      pathSelected.value = [item]
    }
  }
}
</script>

<template>
  <div class="ma-resource-panel h-full flex flex-col">
    <div class="h-41px flex justify-between">
      <div class="w-500px">
        <MTabs v-model="queryParams.mime_type" :options="resourceType" class="text-sm" />
      </div>

      <div class="flex flex-1 justify-end">
        <el-input v-model="queryParams.origin_name" placeholder="搜索资源名" class="max-w-[240px]">
          <template #suffix>
            <el-icon><Search /></el-icon>
          </template>
        </el-input>
        <!--        <el-button bg> -->
        <!--          <template #icon> -->
        <!--            <ma-svg-icon name="ant-design:appstore-outlined" /> -->
        <!--          </template> -->
        <!--        </el-button> -->
      </div>
    </div>
    <div class="min-h-0 flex-1">
      <OverlayScrollbarsComponent ref="scrollbarRef" class="max-h-full py-3" :options="{ scrollbars: { autoHide: 'leave', autoHideDelay: 100 } }">
        <div class="flex flex-wrap">
          <el-space wrap fill :fill-ratio="9">
            <template v-for="resource in resourceList" :key="resource.id">
              <div class="resource-item" :class="{ active: pathSelected.includes(resource.url) }" @click="selectResource(resource.url)">
                <div class="resource-item__name">
                  {{ resource.origin_name }}
                </div>
              </div>
            </template>
            <el-skeleton v-for="i in skeletonNum" class="resource-skeleton relative" animated>
              <template #template>
                <el-skeleton-item class="absolute h-full w-full" variant="rect" />
              </template>
            </el-skeleton>
            <div v-for="i in 10" class="resource-placeholder" />
          </el-space>
        </div>
      </OverlayScrollbarsComponent>
    </div>
    <div class="ma-resource-panel__footer">
      <el-pagination
        v-model:current-page="queryParams.page"
        :disabled="loading"
        class="mt-8"
        :total="total"
        :page-size="queryParams.pageSize"
        background
        layout="prev, pager, next"
        :pager-count="5"
      />
    </div>
  </div>
</template>

<style scoped lang="scss">
@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.ma-resource-panel{
  --resource-item-size:120px;
}
.resource-item{
  animation: fadeIn 0.3s ease-out forwards;
  --un-bg-opacity: 0.1;
  @apply relative min-w-[var(--resource-item-size)] pb-[100%] rounded;
  background-color: rgb(var(--ui-primary) / var(--un-bg-opacity));
}
.resource-item__name{
  @apply absolute bottom-0 left-0 h-24px w-[calc(100%-20px)] overflow-hidden bg-gray:20 px-10px text-12px leading-24px whitespace-nowrap text-ellipsis;
}
.resource-placeholder{
  @apply min-w-[var(--resource-item-size)] h-0 pointer-events-none p-0;
}
.resource-skeleton{
  @apply min-w-[var(--resource-item-size)] pb-[100%];
}

.resource-item:hover,
.resource-item.active {
  box-shadow:inset 0 0 0 2px rgb(var(--ui-primary));
}

.resource-item.active::after{
  content: '';
  position: absolute;
  width: 100%;
  height: 100%;
  z-index: 2;
  top: 0;
  background: rgba(var(--ui-primary) / var(--un-bg-opacity));
}
</style>
