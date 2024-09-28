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

defineOptions({ name: 'MaUploadImage' })

const props = defineProps<{
  title?: string
  size?: number
}>()
const t = useLocalTrans()
const size = computed(() => {
  return {
    width: `${props?.size ?? 120}px`,
    height: `${props?.size ?? 120}px`,
  }
})

function btnRender() {
  return (
    <a class="ma-upload-btn" style={size.value}>
      <el-tooltip content={t('openResource')}>
        <a
          class="ma-resource-btn"
          onClick={(e: MouseEvent) => {
            e.stopPropagation()
          }}
        >
          <ma-svg-icon name="material-symbols:folder-open-outline-rounded" size={18} />
        </a>
      </el-tooltip>
      <div class="mt-18% flex flex-col items-center">
        <ma-svg-icon name="ep:plus" size={20} />
        <span class="mt-1 text-[14px]">{ props?.title ?? t('uploadImage') }</span>
      </div>
    </a>
  )
}
</script>

<template>
  <el-upload
    v-bind="$attrs"
    class="block"
  >
    <slot name="default">
      <component :is="btnRender()" />
    </slot>
    <template #tip>
      <div class="pt-1 text-sm text-dark-50 dark-text-gray-3">
        <slot name="tip" />
      </div>
    </template>
  </el-upload>
</template>

<style scoped lang="scss">
.ma-upload-btn {
  @apply flex items-center justify-center bg-gray-50 b-1 b-dashed rounded b-gray-3 dark-b-dark-50
    transition-all duration-300 text-gray-5 dark-bg-dark-5 relative z-50;
  ;

  .ma-resource-btn {
    @apply absolute top-0 b-1 b-dashed b-gray-3 dark-b-dark-50 transition-all duration-300
      w-full b-t-0 b-l-0 b-r-0 text-gray-5 dark-bg-dark-8 bg-gray-1 h-[calc(100%-80%)]
      flex items-center justify-center
    ;
  }

  &:hover, .ma-resource-btn:hover {
    @apply text-[rgb(var(--ui-primary))] b-[rgb(var(--ui-primary))];
  }
}
</style>
