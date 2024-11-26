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
  openResource: Open resource
  uploadImage: Upload image
zh_CN:
  openResource: 打开资源选择器
  uploadImage: 上传图片
zh_TW:
  openResource: 打開資源選擇器
  uploadImage: 上載圖片
</i18n>

<script setup lang="tsx">
import { useLocalTrans } from '@/hooks/useLocalTrans.ts'
import type { UploadUserFile } from 'element-plus'
import { isArray, uid } from 'radash'
import { useDebounceFn } from '@vueuse/core'
import { useMessage } from '@/hooks/useMessage.ts'
import { uploadLocal } from '@/utils/uploadLocal.ts'

defineOptions({ name: 'MaUploadImage' })

const {
  modelValue = null,
  title = null,
  size = 120,
  fileSize = 10 * 1024 * 1024,
  fileType = ['image/jpeg', 'image/png', 'image/gif'],
  limit = 5,
  multiple = false,
} = defineProps<{
  modelValue: string | string[] | null
  title?: string
  size?: number
  fileSize?: number
  fileType?: string[]
  limit?: number
  multiple?: boolean
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: string | string[]): void
}>()

const id = uid(5)
const msg = useMessage()
const t = useLocalTrans()

const uploadBtnRef = ref<HTMLElement>()
const isOpenResource = ref<boolean>(false)
const previewList = ref<string[]>([])
const ElImageRefs = ref([])

const getSize = computed(() => {
  return {
    width: `${size ?? 120}px`,
    height: `${size ?? 120}px`,
  }
})

function btnRender() {
  return (
    <a class="ma-upload-container" style={getSize.value}>
      <el-tooltip content={t('openResource')}>
        <a
          class="ma-resource-btn"
          onClick={(e: MouseEvent) => {
            e.stopPropagation()
            isOpenResource.value = true
          }}
        >
          <ma-svg-icon name="material-symbols:folder-open-outline-rounded" size={18} />
        </a>
      </el-tooltip>
      <div class="mt-18% flex flex-col items-center">
        <ma-svg-icon name="ep:plus" size={20} />
        <span class="mt-1 text-[14px]">{ title ?? t('uploadImage') }</span>
      </div>
    </a>
  )
}

const fileList = ref<UploadUserFile[]>([])

function updateModelValue() {
  emit(
    'update:modelValue',
    (multiple ? fileList.value.map(file => file.url!) : fileList.value[0]?.url) as string | string[],
  )
}

function handleSuccess(res: any) {
  const index = fileList.value.findIndex((item: any) => item.response?.data.id === res.data.id)
  fileList.value[index].name = res.data.origin_name
  fileList.value[index].url = res.data.url

  updateModelValue()
}

function beforeUpload(rawFile: File) {
  if (!fileType.includes(rawFile.type)) {
    msg.error(`只允许上传：${fileType.join(', ')}`)
    return false
  }
  if (fileSize < rawFile.size) {
    msg.error(`只允许上传${fileSize}字节大小的文件`)
    return false
  }

  return true
}

function handleExceed() {
  msg.error(`当前最多只能上传 ${limit} 张图片，请重新选择上传！`)
}

function handleError() {
  msg.error(`图片上传失败，请您重新上传！`)
}

watch(
  () => fileList.value.length,
  (length: number) => {
    const uploadTextDom: HTMLElement | null = document.querySelector(`.ma-upload-${id} .el-upload--text`)
    if (uploadTextDom) {
      uploadTextDom.style.display = length > 0 ? 'none' : 'block'
    }
  },
  { immediate: true },
)

const setPreviewData = useDebounceFn(() => {
  previewList.value = []
  fileList.value?.map((item: any) => {
    previewList.value.push(item.url)
  })
})

watch(
  () => fileList.value,
  async () => {
    await setPreviewData()
  },
  { immediate: true, deep: true },
)

watch(
  () => modelValue,
  (val: string | string[] | null) => {
    if (!val) {
      fileList.value = []
      return false
    }

    if (isArray(val)) {
      fileList.value = val.map((item: string) => {
        return {
          name: item.split('/').pop() as string,
          url: item,
        }
      })
    }
    else {
      fileList.value = [{ name: val?.split('/')?.pop() as string, url: val }]
    }
  },
  { immediate: true, deep: true },
)
</script>

<template>
  <el-upload
    v-model:file-list="fileList"
    :class="`ma-upload-${id}`"
    :before-upload="beforeUpload"
    :http-request="uploadLocal"
    :on-success="handleSuccess"
    :on-exceed="handleExceed"
    :on-error="handleError"
    :multiple="multiple"
    :limit="limit"
    v-bind="$attrs"
  >
    <slot name="default">
      <component :is="btnRender()" v-show="fileList.length === 0" ref="uploadBtnRef" />
    </slot>
    <template #file="{ file, index }">
      <div class="ma-preview-list ma-upload-container relative" :style="getSize">
        <div class="ma-preview-mask">
          <ma-svg-icon
            name="weui:eyes-on-filled"
            class="icon"
            :size="20"
            @click="() => ElImageRefs?.$el?.children[0]?.click?.()"
          />
          <ma-svg-icon
            name="material-symbols:delete"
            class="icon"
            :size="20"
            @click="() => {
              fileList.splice(index, 1)
              updateModelValue()
            }"
          />
        </div>
        <el-image
          ref="ElImageRefs"
          :src="file?.url"
          class="absolute rounded-md"
          :style="getSize"
          fit="cover"
          :zoom-rate="1.2"
          :max-scale="7"
          :min-scale="0.2"
          :preview-src-list="previewList"
          :initial-index="index"
        />
      </div>
      <component
        :is="btnRender()"
        v-if="index === (fileList.length - 1) && multiple && fileList.length < limit"
        class="cursor-pointer"
        @click="() => uploadBtnRef?.click?.()"
      />
    </template>
    <template #tip>
      <div v-if="fileList.length < 1" class="pt-1 text-sm text-dark-50 dark-text-gray-3">
        <slot name="tip">
          {{ $attrs?.tip }}
        </slot>
      </div>
    </template>
    <MaResourcePicker
      v-model:visible="isOpenResource"
      :multiple="multiple"
      :limit="limit"
      @confirm="(selected) => {
        fileList = selected.map((item: any) => {
          return { name: item.ogirin_name, url: item.url }
        })
        updateModelValue()
      }"
    />
  </el-upload>
</template>

<style scoped lang="scss">
:deep(.el-upload-list) {
  @apply flex gap-1.5 flex-wrap;
  .el-upload-list__item {
    @apply w-auto outline-none b-0;
  }
  .el-upload-list__item:hover {
    background: none;
  }
  & :last-child {
    @apply flex gap-x-1.5;
  }
}
.ma-upload-container {
  @apply flex items-center justify-center bg-gray-50 b-1 b-dashed rounded-md b-gray-3 dark-b-dark-50
    transition-all duration-300 text-gray-5 dark-bg-dark-5 relative;
  ;

  .ma-resource-btn {
    @apply absolute top-0 b-1 b-dashed b-gray-3 dark-b-dark-50 transition-all duration-300 rounded-t-md
      w-[calc(100%)] mx-auto b-t-0 b-l-0 b-r-0 text-gray-5 dark-bg-dark-8 bg-gray-1 h-[calc(100%-80%)]
      flex items-center justify-center
    ;
  }

  .ma-preview-mask {
    @apply absolute z-8 w-full h-full rounded-md transition-all duration-300 flex items-center justify-center gap-x-3;
    .icon {
      @apply hidden text-white cursor-pointer;
    }
  }
  .ma-preview-mask:hover {
    @apply bg-dark-5/50%;
    .icon {
      @apply inline;
    }
  }

  &:hover, .ma-resource-btn:hover {
    @apply text-[rgb(var(--ui-primary))] b-[rgb(var(--ui-primary))];
  }
}
</style>
