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
import ContextMenu from '@imengyu/vue3-context-menu'
import type { FileType, Resource, ResourcePanelProps } from './type.ts'

import { useImageViewer } from '@/hooks/useImageViewer.ts'
import { useMessage } from '@/hooks/useMessage.ts'

defineOptions({ name: 'MaResourcePanel' })

const props = withDefaults(defineProps<ResourcePanelProps>(), {
  multiple: false,
  limit: undefined,
  pageSize: 15,
  dbClickConfirm: false,
  fileTypes: () => [
    { label: '所有', value: '', icon: 'ri:gallery-view-2', suffix: '' },
    { label: '图片', value: 'image', icon: 'ri:image-line', suffix: 'png,jpg,jpeg,gif,bmp' },
    { label: '视频', value: 'video', icon: 'ri:folder-video-line', suffix: 'mp4,avi,wmv,mov,flv,mkv webm' },
    { label: '音频', value: 'audio', icon: 'ri:file-music-line', suffix: 'mp3,wav,ogg,wma,aac,flac,ape,wavpack' },
    { label: '文档', value: 'document', icon: 'ri:file-text-line', suffix: 'doc,docx,xls,xlsx,ppt,pptx,pdf' },
  ],
})

const emit = defineEmits<{
  (e: 'cancel'): void
  (e: 'confirm', value: any[]): void
}>()

const msg = useMessage()

const modelValue = defineModel<string | string[] | undefined>()

const fileTypeSelected = ref(props.defaultFileType ?? '')
const returnType = 'url'

const fileTypes = computed<FileType[]>(() => props.fileTypes)

/**
 * 加载状态
 */
const loading = ref(false)

/**
 * 当前资源列表
 */
const resources = ref<Resource[]>([])

/**
 * 资源总数
 */
const total = ref<number>(0)

/**
 * 选中资源的key列表,该数据可用做直接返回
 */
const selectedKeys = ref<Array<string | number>>([])

const selected = ref<Resource[]>([])

async function getResourceList(params: Resource = {}) {
  loading.value = true
  const { data } = await useHttp().get(
    '/admin/attachment/list',
    { params: Object.assign({ pageSize: props.pageSize }, params) },
  )
  total.value = data.total
  resources.value = data.list
  loading.value = false
}

// 监听v-model变化，更新selectedKeys
watch(() => modelValue.value, (newValue) => {
  selectedKeys.value = Array.isArray(newValue) ? newValue : newValue ? [newValue] : []
}, { deep: true })

// 监听selectedKeys变化，更新v-model
watch(() => selectedKeys.value, (newKeys) => {
  const newValue = props.multiple ? newKeys : newKeys[0]
  // 同样，只有在modelValue真正改变时才更新
  if (modelValue.value && modelValue.value !== newValue) {
    modelValue.value = newValue as string | string[]
  }
}, { deep: true })

/**
 * 查询参数
 */
const queryParams = ref<Record<string, any>>({
  pageNo: 1,
  pageSize: props.pageSize,
  origin_name: '',
  suffix: [],
})

/**
 * 加载占位符数量
 */
const skeletonNum = computed(() => {
  return loading.value ? queryParams.value.pageSize : 15
})

function onfileTypesChange(value: any) {
  fileTypeSelected.value = value
  queryParams.value.suffix = (fileTypes.value?.find(i => i.value === value)?.suffix || []) as string[]
  getResourceList(queryParams.value)
}

/**
 * 获取封面
 * @param resource
 */
function getCover(resource: Resource): string | undefined {
  if (resource?.mime_type?.startsWith('image')) {
    return resource.url
  }
  return undefined
}

/**
 * 判断是否被选中
 * @param resource
 */
function isSelected(resource: Resource) {
  const key = resource[returnType] as string
  return selectedKeys.value.includes(key)
}

/**
 * 判断是否能预览
 * @param resource
 */
function canPreview(resource: Resource) {
  return resource?.mime_type?.startsWith('image')
}

/**
 * 选中资源
 */
function select(resource: Resource) {
  const key = resource[returnType] as string
  // 单选
  if (props.multiple) {
    // 判断是否上限
    if (props.limit && selectedKeys.value.length >= props.limit) {
      return msg.warning(`最多选择${props.limit}个`)
    }
    selectedKeys.value.push(key)
    if (!selected.value.find(i => i[returnType] === key)) {
      selected.value.push(resource)
    }
  }
  else {
    selected.value = [resource]
    selectedKeys.value = [key]
  }
}

/**
 * 取消选中
 */
function unSelect(resource: Resource) {
  const key = resource[returnType] as string
  selectedKeys.value = selectedKeys.value.filter(i => i !== key)
  selected.value = selected.value.filter(i => i[returnType] !== key)
}

/**
 * 清空选中
 */
function clearSelected() {
  selectedKeys.value = []
  selected.value = []
}

function cancel() {
  emit('cancel')
}

function confirm() {
  emit('confirm', selected.value)
}

/**
 * 处理点击资源事件
 */
function handleClick(resource: Resource) {
  isSelected(resource) ? unSelect(resource) : select(resource)
}

/**
 * 处理双击资源事件
 */
function handleDbClick(resource: Resource) {
  // 双击确认选中单个元素
  clearSelected()
  select(resource)
  confirm()
}

/**
 * 右键菜单
 */
function executeContextmenu(e: MouseEvent, resource: Resource) {
  e.preventDefault()
  ContextMenu.showContextMenu({
    x: e.x,
    y: e.y,
    zIndex: 9999,
    iconFontClass: '',
    customClass: 'mine-contextmenu',
    items: [
      {
        label: '选中',
        hidden: isSelected(resource),
        icon: 'i-ri:check-fill',
        onClick: () => {
          select(resource)
        },
      },
      {
        label: '取消选中',
        hidden: !isSelected(resource),
        icon: 'i-ri:close-fill',
        onClick: () => {
          unSelect(resource)
        },
      },
      // 独选此项
      {
        label: '独选此项',
        icon: 'i-ri:checkbox-circle-line',
        divided: true,
        onClick: () => {
          clearSelected()
          select(resource)
        },
      },
      {
        label: '查看',
        icon: 'i-ri:search-eye-line',
        disabled: !canPreview(resource),
        onClick: () => {
          useImageViewer([resource?.url ?? ''])
        },
      },
    ],
  })
}

onMounted(async () => {
  await getResourceList()
})
</script>

<template>
  <div class="ma-resource-panel h-full flex flex-col">
    <div class="flex justify-between">
      <div>
        <el-segmented
          v-model="fileTypeSelected"
          :options="fileTypes"
          size="default"
          block
          @change="onfileTypesChange"
        >
          <template #default="{ item }">
            <div class="flex items-center justify-center">
              <ma-svg-icon v-if="item?.icon" :name="item!.icon" :size="17" class="mr-1 flex items-center justify-center" />
              <span>{{ item!.label }}</span>
            </div>
          </template>
        </el-segmented>
      </div>

      <div class="flex justify-end">
        <el-input
          v-model="queryParams.origin_name"
          placeholder="搜索资源名"
          clearable
          class="w-[180px]"
          @input="() => {
            getResourceList(queryParams)
          }"
        >
          <template #suffix>
            <ma-svg-icon name="i-material-symbols:search-rounded" :size="20" />
          </template>
        </el-input>
      </div>
    </div>
    <div class="mt-2 min-h-0 flex-1">
      <OverlayScrollbarsComponent v-if="loading || resources.length" class="max-h-full" :options="{ scrollbars: { autoHide: 'leave', autoHideDelay: 100 } }">
        <div class="flex flex-wrap px-[2px] pt-[2px]">
          <el-space fill wrap :fill-ratio="9">
            <template v-for="resource in resources" :key="resource.id">
              <div
                class="resource-item"
                :class="{ active: isSelected(resource) }"
                @click="handleClick(resource)"
                @dblclick="handleDbClick(resource)"
                @contextmenu="(e: MouseEvent) => executeContextmenu(e, resource)"
              >
                <div class="resource-item__cover">
                  <template v-if="getCover(resource)">
                    <el-image :src="getCover(resource)" fit="cover" class="h-full w-full" lazy>
                      <template #error>
                        <div class="relative m-[8px] h-[calc(100%-16px)] w-[calc(100%-16px)] flex items-center justify-center overflow-hidden">
                          <div class="cursor-default overflow-hidden text-ellipsis whitespace-pre-wrap">
                            {{ resource.origin_name }}
                          </div>
                        </div>
                      </template>
                    </el-image>
                  </template>
                  <template v-else>
                    <div class="relative m-[8px] h-[calc(100%-16px)] w-[calc(100%-16px)] flex items-center justify-center overflow-hidden">
                      <div class="cursor-default overflow-hidden text-ellipsis whitespace-pre-wrap">
                        {{ resource.origin_name }}
                      </div>
                    </div>
                  </template>
                </div>
                <div v-if="getCover(resource)" class="resource-item__name cursor-default">
                  {{ resource.origin_name }}
                </div>
                <div class="resource-item__selected">
                  <ma-svg-icon class="resource-item__selected-icon" name="gravity-ui:circle-check-fill" :size="18" />
                </div>
              </div>
            </template>
            <template v-if="resources.length === 0">
              <el-skeleton v-for="i in skeletonNum" :key="i" class="resource-skeleton relative" animated>
                <template #template>
                  <el-skeleton-item class="absolute h-full w-full" variant="rect" />
                </template>
              </el-skeleton>
            </template>
            <div v-for="i in 10" :key="i" class="resource-placeholder" />
          </el-space>
        </div>
      </OverlayScrollbarsComponent>
      <div v-else class="h-full w-full flex flex-1 items-center justify-center">
        <el-empty />
      </div>
    </div>
    <div class="ma-resource-panel__footer flex justify-between pt-2">
      <div class="flex items-center">
        <el-tag v-if="props.multiple && props.limit" size="large" class="mr-2" :class="{ 'color-[var(--el-color-danger)]': props.limit && selectedKeys.length >= props.limit }">
          {{ selectedKeys.length }}
          <template v-if="props.multiple && props.limit">
            /{{ props.limit }}
          </template>
        </el-tag>
        <el-pagination
          v-model:current-page="queryParams.pageNo"
          :disabled="loading"
          :total="total"
          :page-size="queryParams.pageSize"
          background
          layout="prev, pager, next"
          :pager-count="5"
        />
      </div>
      <div>
        <el-button @click="cancel">
          取消
        </el-button>
        <el-button type="primary" @click="confirm">
          确认
        </el-button>
      </div>
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
  @apply relative min-w-[var(--resource-item-size)] pb-[100%] rounded overflow-hidden border-box bg-gray-1  dark-bg-dark-3;
}
.resource-item__cover{
  @apply absolute bottom-0 left-0 h-full w-full;
}
.resource-item__name{
  @apply absolute bottom-0 left-0 h-24px w-[calc(100%-20px)] overflow-hidden bg-gray:60 px-10px text-12px leading-24px whitespace-nowrap text-ellipsis c-white;

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
