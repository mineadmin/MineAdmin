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
import type { UploadRequestOptions, UploadUserFile } from 'element-plus'

defineOptions({ name: 'MaUploadImage' })

const props = defineProps<{
  title?: string
  size?: number
}>()

const t = useLocalTrans()
const uploadBtnRef = ref<HTMLElement>()
const isOpenResource = ref<boolean>(false)
const size = computed(() => {
  return {
    width: `${props?.size ?? 120}px`,
    height: `${props?.size ?? 120}px`,
  }
})

function btnRender() {
  return (
    <a class="ma-upload-container" style={size.value}>
      <el-tooltip content={t('openResource')}>
        <a
          class="ma-resource-btn"
          onClick={(e: MouseEvent) => {
            e.stopPropagation()
            isOpenResource.value = true
          }}
        >
          <ma-svg-icon name="material-symbols:folder-open-outline-rounded" size={18} />
          <MaResourcePicker v-model:visible={isOpenResource.value} />
        </a>
      </el-tooltip>
      <div class="mt-18% flex flex-col items-center">
        <ma-svg-icon name="ep:plus" size={20} />
        <span class="mt-1 text-[14px]">{ props?.title ?? t('uploadImage') }</span>
      </div>
    </a>
  )
}

const fileList = ref<UploadUserFile[]>([
  { name: 'food.jpeg', url: 'https://picsum.photos/120/120?random=1' },
  { name: 'food.jpeg', url: 'https://picsum.photos/120/120?random=2' },
  { name: 'food.jpeg', url: 'https://picsum.photos/120/120?random=3' },
  { name: 'food.jpeg', url: 'https://picsum.photos/120/120?random=4' },
  { name: 'food.jpeg', url: 'https://picsum.photos/120/120?random=5' },
])

function upload(formData: FormData) {
  return useHttp().post('/admin/attachment/upload', formData)
}

function handleUpload(options: UploadRequestOptions): any {
  return new Promise((resolve, reject) => {
    const formData = new FormData()
    formData.append('file', options.file)
    upload(formData).then((res: Record<string, any>) => {
      if (res.code === 200) {
        resolve(res)
      }
      else {
        reject(res)
      }
    }).catch((err) => {
      reject(err)
    })
  })
}

function handleSuccess(res: any) {
  // 删除自身
  const index = fileList.value.findIndex((item: any) => item.response?.data === res.data)
  fileList.value.splice(index, 1)
  fileList.value.push({
    name: res.data.origin_name,
    url: res.data.url,
  })
}

function handlePreview(res: any) {
  console.log(res)
}
</script>

<template>
  <el-upload
    v-model:file-list="fileList"
    :http-request="handleUpload"
    :on-success="handleSuccess"
    :on-preview="handlePreview"
    v-bind="$attrs"
  >
    <slot name="default">
      <component :is="btnRender()" v-show="fileList.length < 1" ref="uploadBtnRef" />
    </slot>
    <template #file="{ file, index }">
      <div class="ma-upload-container" :style="size">
        <el-image :src="file?.url" class="rounded-md" :style="size" fit="cover" />
      </div>
      <component
        :is="btnRender()"
        v-if="index === (fileList.length - 1)"
        class="cursor-pointer"
        @click="() => uploadBtnRef?.click?.()"
      />
    </template>
    <template #tip>
      <div v-if="fileList.length < 1" class="pt-1 text-sm text-dark-50 dark-text-gray-3">
        <slot name="tip" />
      </div>
    </template>
  </el-upload>
</template>

<style scoped lang="scss">
:deep(.el-upload-list) {
  @apply flex gap-1.5;
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
    transition-all duration-300 text-gray-5 dark-bg-dark-5 relative z-50;
  ;

  .ma-resource-btn {
    @apply absolute top-0 b-1 b-dashed b-gray-3 dark-b-dark-50 transition-all duration-300 rounded-t-md
      w-[calc(100%)] mx-auto b-t-0 b-l-0 b-r-0 text-gray-5 dark-bg-dark-8 bg-gray-1 h-[calc(100%-80%)]
      flex items-center justify-center
    ;
  }

  &:hover, .ma-resource-btn:hover {
    @apply text-[rgb(var(--ui-primary))] b-[rgb(var(--ui-primary))];
  }
}
</style>
