<script setup lang="ts">
import getOnlyWorkAreaHeight from '@/utils/getOnlyWorkAreaHeight.ts'
import GeneratorTypeSelect from './components/generatorTypeSelect.vue'
import PageHeader from '$/mine-admin/code-generator/views/components/pageHeader.vue'
import Create from '$/mine-admin/code-generator/views/components/create.vue'

defineOptions({ name: 'CodeGenerator' })

const options = ref<Record<string, any>>({
  isHomePage: true,
  createType: 'create',
  typeInfo: {},
})

provide('options', options)

onMounted(() => {
  const dom = document.querySelector('#code-generator-area') as HTMLElement
  if (dom) {
    dom.style.height = `${getOnlyWorkAreaHeight()}px`
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

    <div v-if="!options.isHomePage" class="code-main p-5">
      <PageHeader>
        <template #content>
          <Create />
        </template>
      </PageHeader>
    </div>
  </div>
</template>
