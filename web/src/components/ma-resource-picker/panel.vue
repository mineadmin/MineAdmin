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
import { ElImageViewer, ElMessage } from 'element-plus'
import { Search } from '@element-plus/icons-vue'
import ContextMenu from '@imengyu/vue3-context-menu'
import { render } from 'vue'
import MTabs from '$/mine-admin/basic-ui/components/tab/index.vue'
import { useMessage } from '@/hooks/useMessage.ts'

defineOptions({ name: 'MaResourcePanel' })

const props = withDefaults(defineProps<{
  multiple?: boolean
  limit?: number
  pageSize?: number
}>(), {
  multiple: false,
  limit: undefined,
  pageSize: 40,
})

const message = useMessage()

const imageViewerRef = ref()

// 可以将此功能抽离 usrImageViewer
function openImageViewer(images, initialIndex = 0) {
  const vnode = h(ElImageViewer, {
    urlList: images,
    hideOnClickModal: true,
    zIndex: 2500,
    initialIndex,
    onClose: () => {
      if (imageViewerRef.value) {
        render(null, imageViewerRef.value)
      }
    },
  })
  imageViewerRef.value = document.createElement('div')
  document.body.appendChild(imageViewerRef.value)
  render(vnode, imageViewerRef.value)
}

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
  pageSize: props.pageSize,
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
    }, Math.floor(Math.random() * 900 + 100))
  })
}

watch(queryParams, () => {
  query()
}, { deep: true, immediate: true })
function toggleSelect(item: Resource) {
  const key = item.url
  // 多选
  if (pathSelected.value.includes(key)) {
    pathSelected.value = pathSelected.value.filter(i => i !== key)
  }
  else {
    if (props.multiple) {
      // 判断是否上限
      if (props.limit && pathSelected.value.length >= props.limit) {
        return ElMessage.warning(`最多选择${props.limit}个`)
      }
      pathSelected.value.push(key)
    }
    else {
      pathSelected.value = [key]
    }
  }
}
function clearSelected() {
  pathSelected.value = []
}

function isSelected(item: Resource) {
  return pathSelected.value.includes(item.url)
}

function canPreview(item: Resource) {
  return item.mime_type.startsWith('image')
}

function handleDoubleClick(item: Resource) {
  if (canPreview(item)) {
    openImageViewer([item.url])
  }
  else {
    message.warning('该资源无法预览,下载请右键')
  }
}

function executeContextmenu(e: MouseEvent, item: Resource) {
  e.preventDefault()
  ContextMenu.showContextMenu({
    x: e.x,
    y: e.y,
    zIndex: 9999,
    iconFontClass: '',
    customClass: 'mine-tab-contextmenu',
    items: [
      {
        label: '选中',
        hidden: isSelected(item),
        icon: 'i-ri:check-fill',
        onClick: () => {
          toggleSelect(item)
        },
      },
      {
        label: '取消选中',
        hidden: !isSelected(item),
        icon: 'i-ri:close-fill',
        onClick: () => {
          toggleSelect(item)
        },
      },
      // 独选此项
      {
        label: '独选此项',
        icon: 'i-ri:checkbox-circle-line',
        divided: true,
        onClick: () => {
          clearSelected()
          toggleSelect(item)
        },
      },
      {
        label: '查看',
        icon: 'i-ri:search-eye-line',
        disabled: !canPreview(item),
        onClick: () => {
          openImageViewer([item.url])
        },
      },
      {
        label: '下载',
        icon: 'i-ri:download-line',
        onClick: () => {
          // 下载待接入
        },
      },

    ],
  })
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
      <OverlayScrollbarsComponent ref="scrollbarRef" class="max-h-full px-[2px] py-3" :options="{ scrollbars: { autoHide: 'leave', autoHideDelay: 100 } }">
        <div class="flex flex-wrap">
          <el-space wrap fill :fill-ratio="9">
            <template v-for="resource in resourceList" :key="resource.id">
              <div
                class="resource-item"
                :class="{ active: isSelected(resource) }"
                @click="toggleSelect(resource)"
                @dblclick="handleDoubleClick(resource)"
                @contextmenu="(e: MouseEvent) => executeContextmenu(e, resource)"
              >
                <div class="resource-item__image">
                  <el-image :src="resource.url" fit="cover" />
                </div>
                <div class="resource-item__name">
                  {{ resource.origin_name }}
                </div>
                <div class="resource-item__selected">
                  <ma-svg-icon class="resource-item__selected-icon" name="gravity-ui:circle-check-fill" :size="18" />
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
  animation: fadeIn 0.38s ease-out forwards;
  --un-bg-opacity: 0.3;
  @apply relative min-w-[var(--resource-item-size)] pb-[100%] rounded overflow-hidden border-box;
  background-color: rgb(var(--ui-primary) / var(--un-bg-opacity));
}
.resource-item__image{
  @apply absolute bottom-0 left-0 h-full w-full;
}
.resource-item__name{
  @apply absolute bottom-0 left-0 h-24px w-[calc(100%-20px)] overflow-hidden bg-gray:20 px-10px text-12px leading-24px whitespace-nowrap text-ellipsis;
}
.resource-item__selected{
  @apply absolute top--30px right--30px w-40px h-40px;
  //transition: all 0.1s ease-in-out;
  background-image: linear-gradient(to top right, transparent 50%, rgb(var(--ui-primary)) 50%);
}
.resource-item__selected-icon{
  @apply absolute top-0 right-0 p-2px c-white;
}
.resource-item.active .resource-item__selected{
  @apply top-0 right-0;
}

.resource-placeholder{
  @apply min-w-[var(--resource-item-size)] h-0 pointer-events-none p-0;
}
.resource-skeleton{
  @apply min-w-[var(--resource-item-size)] pb-[100%];
}

.resource-item:hover,
.resource-item.active {
  @apply ring-2 ring-[rgb(var(--ui-primary))];
}
</style>
