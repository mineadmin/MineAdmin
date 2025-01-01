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
  uploadFile: Upload File
zh_CN:
  uploadFile: 上传文件
zh_TW:
  uploadFile: 上載文件
</i18n>

<script setup lang="tsx">
import { useLocalTrans } from '@/hooks/useLocalTrans.ts'
import type { UploadUserFile } from 'element-plus'
import { isArray, uid } from 'radash'
import { useMessage } from '@/hooks/useMessage.ts'
import { uploadLocal } from '@/utils/uploadLocal.ts'

defineOptions({ name: 'MaUploadFile' })

const {
  modelValue = null,
  title = null,
  fileSize = 10 * 1024 * 1024,
  fileType = ['doc', 'xls', 'ppt', 'txt', 'pdf'],
  limit = 1,
  multiple = false,
} = defineProps<{
  modelValue: string | string[] | null
  title?: string
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
  let fileExtension = ''
  if (rawFile.name.includes('.')) {
    fileExtension = rawFile.name.slice(rawFile.name.lastIndexOf('.') + 1)
  }

  const isAllowFile = fileType.some((type: string) => {
    if (rawFile.type.includes(type)) {
      return true
    }
    return !!(fileExtension && fileExtension.includes(type))
  })
  if (!isAllowFile) {
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
  msg.error(`当前最多只能上传 ${limit} 个文件，请重新选择上传！`)
}

function handleError() {
  msg.error(`文件上传失败，请您重新上传！`)
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

watch(
  () => modelValue,
  (val: string | string[] | null) => {
    if (!val) {
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
    :before-upload="beforeUpload"
    :http-request="uploadLocal"
    :on-success="handleSuccess"
    :on-exceed="handleExceed"
    :on-error="handleError"
    :multiple="multiple"
    :limit="limit"
    v-bind="$attrs"
    class="w-full"
  >
    <slot name="default">
      <el-button type="primary">
        {{ title ?? t('uploadFile') }}
      </el-button>
    </slot>
    <template #tip>
      <div v-if="fileList.length < 1" class="pt-1 text-sm text-dark-50 dark-text-gray-3">
        <slot name="tip">
          {{ $attrs?.tip }}
        </slot>
      </div>
    </template>
  </el-upload>
</template>

<style scoped lang="scss">
</style>
