<script setup lang="ts">
import getOnlyWorkAreaHeight from '@/utils/getOnlyWorkAreaHeight.ts'
import GeneratorTypeSelect from './components/generatorTypeSelect.vue'
import PageHeader from './components/pageHeader.vue'
import Design from './components/design.vue'
import useComponent from '../hooks/useComponent.ts'
import type { DesignComponent } from '../configs/component.tsx'
import ExcludeRenderComponent from '../configs/excludeRenderComponent.ts'

defineOptions({ name: 'CodeGenerator' })

const designComponents = ref<DesignComponent[]>([])
const currentSelection = ref<DesignComponent>()
const options = ref<Record<string, any>>({
  isHomePage: true,
  createType: 'create',
  segmentedModel: 'design',
  componentCollapseModel: ['base', 'mineadmin'],
  excludeRender: ExcludeRenderComponent,
  attrCollapseModel: 'base',
  isDrag: false,
  typeInfo: {},
  model: {},
  settingModel: {},
  proTableOptions: {
    tableOptions: {
      adaption: false,
    },
    header: {
      show: true,
      mainTitle: '表格主标题',
      subTitle: '',
    },
    toolbar: true,
    requestOptions: {
      api: () => {},
      autoRequest: false,
    },
  },
})

const componentHook = ref(useComponent())

provide('options', options)
provide('componentHook', componentHook)
provide('designComponents', designComponents)
provide('currentSelection', currentSelection)

onMounted(() => {
  const dom = document.querySelector('#code-generator-area') as HTMLElement
  if (dom) {
    dom.style.height = `${getOnlyWorkAreaHeight() + 35}px`
  }
})
</script>

<template>
  <div id="code-generator-area" class="relative w-full bg-white dark:bg-black">
    <div class="absolute left-10 top-100 h-[300px] w-[300px] rounded-full bg-blue-700/20 blur-3xl" />
    <div class="absolute right-[35%] top-20 h-[700px] w-[700px] rounded-full bg-cyan-700/20 blur-3xl" />
    <div class="absolute right-10 top-30 h-[400px] w-[400px] rounded-full bg-green-700/20 blur-3xl" />
    <div v-show="options.isHomePage" class="pt-50 text-center text-7xl font-bold">
      <span class="text-gray-700 dark:text-white">MineAdmin</span><span class="text-[rgb(var(--ui-primary))]">代码生成</span>
    </div>

    <GeneratorTypeSelect v-show="options.isHomePage" />

    <div v-if="!options.isHomePage" class="code-main h-[calc(100%-80px)] p-5">
      <PageHeader />
      <Design />
    </div>
  </div>
</template>

<style lang="scss">
.mine-worker-area {
  @apply overflow-hidden;
}
.componentStyle {
  @apply b-1 b-[var(--el-color-primary-light-7)] rounded b-solid bg-[var(--el-color-primary-light-9)] px-2 py-1 text-dark-1
  dark-b-[var(--el-color-primary-dark-6)] dark-bg-[var(--el-color-primary-dark-8)] dark-text-gray-3 overflow-hidden
  ;
}
</style>
