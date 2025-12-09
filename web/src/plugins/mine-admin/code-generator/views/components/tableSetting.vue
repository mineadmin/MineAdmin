<script setup lang="ts">
import type { Ref } from 'vue'
import type { DesignComponent } from '$/mine-admin/code-generator/configs/component.tsx'
import type { MaProTableColumns, MaProTableExpose, MaProTableOptions, MaProTableSchema } from '@mineadmin/pro-table'

const designComponents = inject<Ref<DesignComponent[]>>('designComponents')
const tableSettingRef = ref<MaProTableExpose>()
const selection = ref({})
const options = ref<MaProTableOptions>({
  tableOptions: {
    adaption: false,
  },
  toolbar: false,
  requestOptions: {
    api: () => {},
    autoRequest: false,
  },
})
const schema = ref<MaProTableSchema>({
  searchItems: [],
  tableColumns: [],
})

function initColumns() {
  schema.value.tableColumns = designComponents?.value?.map((item: DesignComponent) => {
    return {
      id: item.id,
      label: item.title,
      prop: item?.formConfig?.prop ?? '',
    } as MaProTableColumns
  })
}

onMounted(() => {
  initColumns()
})
</script>

<template>
  <div class="w-full flex gap-x-5">
    <div class="block-style w-9/12">
      <div>
        <ma-pro-table
          v-if="schema?.tableColumns?.length > 0"
          ref="tableSettingRef" class="w-full" :schema="schema" :options="options"
        >
          <template #empty>
            <div class="mt-3 text-lg">
              选择字段修改表格属性，拖动可设置表格列显示顺序：
            </div>
            <div class="grid grid-cols-5 mb-5 mt-5 w-full gap-2">
              <template v-for="item in schema.tableColumns">
                <div class="col-style" :class="[selection?.id === item.id ? 'selection' : '']" @click="selection = item">
                  {{ `${item.label}` }}
                </div>
              </template>
            </div>
          </template>
        </ma-pro-table>
      </div>
    </div>
    <div class="block-style w-3/12">
      配置区域
      <el-tabs>
        <el-tab-pane label="表格配置" name="first">
          表格配置
        </el-tab-pane>
        <el-tab-pane label="列配置" name="second">
          列配置
        </el-tab-pane>
      </el-tabs>
    </div>
  </div>
</template>

<style scoped lang="scss">
:deep(.mine-card) {
  margin-left: 0; margin-right: 0;
}
.block-style {
  @apply overflow-y-auto b-1 b-gray-2 rounded b-solid bg-[rgba(255,255,255,0.9)] p-2 dark-b-gray-6 dark-bg-[rgba(0,0,0,0.9)];
}
.col-style {
  @apply b-1 b-[var(--el-color-primary-light-7)] rounded b-solid bg-[var(--el-color-primary-light-9)] px-2 py-1 text-dark-1
  dark-b-[var(--el-color-primary-dark-6)] dark-bg-[var(--el-color-primary-dark-8)] dark-text-gray-3 cursor-pointer
  ;
}
.selection {
  @apply border-2 !border-[rgb(var(--ui-primary))] shadow-lg;
}
</style>
