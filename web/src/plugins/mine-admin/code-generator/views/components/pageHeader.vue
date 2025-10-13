<script setup>
import { useMessage } from '@/hooks/useMessage.ts'

const msg = useMessage()
const options = inject('options')

function onBack() {
  msg.confirm('确定放弃现在操作，返回首页？', '提示').then(() => {
    options.value.isHomePage = true
  })
}
</script>

<template>
  <el-page-header @back="onBack">
    <template #content>
      <div class="flex items-center">
        <ma-svg-icon :name="options.typeInfo.icon" />
        <span class="text-large ml-2 mr-3 font-600"> {{ options.typeInfo.title }} </span>
        <span
          class="mr-2 text-sm"
          style="color: var(--el-text-color-regular)"
        >
          {{ options.typeInfo.desc }}
        </span>
      </div>
    </template>
    <template #extra>
      <div class="flex items-center">
        <el-button type="primary" class="ml-2">
          保存
        </el-button>
      </div>
    </template>
    <slot name="content" />
  </el-page-header>
</template>
