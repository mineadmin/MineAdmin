<script setup>
import useComponent from '$/mine-admin/code-generator/hooks/useComponent'

const cpt = useComponent()
cpt.initComponent()
const options = inject('options')
</script>

<template>
  <div v-if="options.createType === 'create'" class="relative z-5 mt-[20px] h-full w-full flex gap-x-2">
    <div class="componentList h-full w-2.5/12 rounded bg-white dark-bg-dark-8">
      <div class="overflow-hidden p-2">
        <el-collapse v-model="options.componentCollapseModel">
          <template v-for="(category, name) in cpt.getComponents()" :key="name">
            <el-collapse-item :title="category.title" :name="name">
              <div class="grid grid-cols-2 gap-3">
                <template v-for="item in category.list">
                  <div class="componentStyle">
                    {{ item.title }}
                  </div>
                </template>
              </div>
            </el-collapse-item>
          </template>
        </el-collapse>
      </div>
    </div>
    <div class="designArea h-full w-7/12 b-1 b-blue-300 rounded b-solid">
      设计区域aa
    </div>
    <div class="attrList w-2.5/12 b-1 b-blue-300 rounded b-solid">
      字段属性
    </div>
  </div>
</template>

<style scoped>
.componentStyle {
  @apply b-1 b-[var(--el-color-primary-light-7)] rounded b-solid bg-[var(--el-color-primary-light-9)] px-2 py-1 text-dark-1
    dark-b-[var(--el-color-primary-dark-6)] dark-bg-[var(--el-color-primary-dark-8)] dark-text-gray-3
  ;
}
</style>
