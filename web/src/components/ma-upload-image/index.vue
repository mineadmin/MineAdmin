<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<script setup lang="tsx">
defineOptions({ name: 'MaUploadImage' })

const props = defineProps<{
  size?: number
}>()

const size = computed(() => {
  return {
    width: `${props?.size ?? 120}px`,
    height: `${props?.size ?? 120}px`,
  }
})

function btnRender() {
  return (
    <a class="ma-upload-btn" style={size.value}>
      <a
        class="ma-resource-btn"
        onClick={(e: MouseEvent) => {
          e.stopPropagation()
          console.log(111111111)
        }}
      >
        <ma-svg-icon name="ep:folder-opened" size={16} />
      </a>
      <ma-svg-icon name="ep:plus" size={26} class="mt-20%" />
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
