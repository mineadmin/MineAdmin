<script setup lang="ts">
import ComponentSelect from './componentSelect.vue'
import DesignRender from './designRender.vue'
import AttrList from './attrList.vue'

const options = inject<Record<string, any>>('options')
</script>

<template>
  <div v-if="options?.createType === 'create'" class="mt-[20px] h-full w-full">
    <div v-show="options.segmentedModel === 'design'" class="design">
      <ComponentSelect />
      <DesignRender />
      <AttrList />
    </div>
    <div v-show="options.segmentedModel === 'list'" class="design">
      <ma-table />
    </div>
    <div v-show="options.segmentedModel === 'setting'" class="design">
      <ma-form v-model="options.settingModel">
        <el-form-item label="数据表名称">
          <el-input v-model="options.settingModel.tableName" />
        </el-form-item>
      </ma-form>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.design {
  @apply relative z-5 h-full w-full flex gap-x-2;
}
</style>
