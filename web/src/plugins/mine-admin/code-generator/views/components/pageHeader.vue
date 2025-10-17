<script setup>
import { useMessage } from '@/hooks/useMessage.ts'

const msg = useMessage()
const options = inject('options')

function onBack() {
  msg.confirm('确定放弃现在操作，返回首页？', '提示').then(() => {
    options.value.isHomePage = true
  })
}

const segmentedOpt = ref([
  {
    label: '字段及表单',
    value: 'design',
    icon: 'EditPen',
  },
  {
    label: '数据列表',
    value: 'list',
    icon: 'Setting',
  },
  {
    label: '其他设置',
    value: 'setting',
    icon: 'Setting',
  },
])
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
        <div class="other">
          <el-segmented
            v-model="options.segmentedModel"
            :options="segmentedOpt"
            size="default"
          />
        </div>
        <el-button type="primary" class="ml-2">
          保存
        </el-button>
      </div>
    </template>
  </el-page-header>
</template>
