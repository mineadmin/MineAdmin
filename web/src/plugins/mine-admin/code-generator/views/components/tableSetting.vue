<script setup lang="tsx">
import type { Ref } from 'vue'
import type { DesignComponent } from '$/mine-admin/code-generator/configs/component.tsx'
import type { MaProTableColumns, MaProTableExpose, MaProTableSchema } from '@mineadmin/pro-table'
import draggable from 'vuedraggable'

const options = inject<any>('options')
const designComponents = inject<Ref<DesignComponent[]>>('designComponents')
const tableSettingRef = ref<MaProTableExpose>()
const selection = ref({})
const dragging = ref<boolean>(false)
const refreshComponent = ref<boolean>(true)
const tabModel = ref<string>('table')

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
      width: 'auto',
      fixed: false,
      align: 'center',
    } as MaProTableColumns
  })
  schema.value.tableColumns?.push({
    type: 'operation',
    label: '操作',
  })
}

function updateSelection(element: any) {
  refreshComponent.value = false
  selection.value = element
  nextTick(() => refreshComponent.value = true)
}

const optionsItems = reactive([
  {
    label: '启用表格标题',
    prop: 'header.show',
    render: 'switch',
  },
  {
    label: '表格主标题',
    prop: 'header.mainTitle',
    render: 'input',
    show: (item, model) => model.header.show,
  },
  {
    label: '表格副标题',
    prop: 'header.subTitle',
    render: 'input',
    show: (item, model) => model.header.show,
  },
  {
    label: '启用工具栏',
    prop: 'toolbar',
    render: 'switch',
  },
  {
    label: '启用展开行',
    prop: 'expand',
    render: 'switch',
    renderProps: {
      onChange: (value: boolean) => {
        if (value) {
          schema.value.tableColumns.unshift({ type: 'expand', label: '展开行' })
        }
        else {
          schema.value.tableColumns.splice(schema.value.tableColumns.findIndex((item: any) => item.type === 'expand'), 1)
        }
      },
    },
  },
  {
    label: '启用选择列',
    prop: 'selection',
    render: 'switch',
    renderProps: {
      onChange: (value: boolean) => {
        if (value) {
          schema.value.tableColumns.unshift({ type: 'selection', label: '选择' })
        }
        else {
          schema.value.tableColumns.splice(schema.value.tableColumns.findIndex((item: any) => item.type === 'selection'), 1)
        }
      },
    },
  },
])

const columnItems = reactive([
  {
    label: '列标题',
    prop: 'label',
    render: 'input',
  },
  {
    label: '列索引',
    prop: 'prop',
    render: 'input',
    renderProps: {
      readonly: true,
    },
  },
  {
    label: '列宽度',
    prop: 'width',
    render: 'input',
    renderSlots: {
      suffix: () => 'px',
    },
  },
  {
    label: '是否隐藏',
    prop: 'hide',
    render: 'switch',
  },
  {
    label: '工具设置里隐藏',
    prop: 'toolHide',
    render: 'switch',
  },
  {
    label: '是否排序',
    prop: 'sortable',
    render: 'switch',
  },
  {
    label: '冻结列',
    prop: 'fixed',
    render: () => <ma-dict-radio />,
    renderProps: {
      data: [
        { label: '不固定', value: false },
        { label: '固定左侧', value: 'left' },
        { label: '固定右侧', value: 'right' },
      ],
    },
  },
  {
    label: '对齐方式',
    prop: 'align',
    render: () => <ma-dict-radio />,
    renderProps: {
      data: [
        { label: '居左显示', value: 'left' },
        { label: '居中显示', value: 'center' },
        { label: '居右显示', value: 'right' },
      ],
    },
  },
])

onMounted(() => {
  initColumns()
})
</script>

<template>
  <div class="w-full flex gap-x-5">
    <div class="block-style w-9/12">
      <div>
        <ma-pro-table
          v-if="(schema?.tableColumns?.length ?? 0) > 0"
          ref="tableSettingRef" class="w-full" :schema="schema" :options="options.proTableOptions"
        >
          <template #empty>
            <div class="mt-3 text-lg">
              选择字段修改列属性，拖动可设置列显示顺序：
            </div>
            <draggable
              :list="schema.tableColumns"
              item-key="id"
              class="grid grid-cols-5 mb-5 mt-5 w-full w-full gap-2"
              ghost-class="ghost"
              @start="dragging = true"
              @end="dragging = false"
            >
              <template #item="{ element }">
                <div v-if="!['operation', 'expand', 'selection'].includes(element.type)" class="col-style" :class="[selection?.id === element.id ? 'selection' : '']" @click="updateSelection(element)">
                  {{ `${element.label}` }}
                </div>
              </template>
            </draggable>
          </template>
        </ma-pro-table>
      </div>
    </div>
    <div class="block-style w-3/12">
      配置区域
      <el-tabs v-model="tabModel">
        <el-tab-pane label="表格配置" name="table">
          <ma-form v-model="options.proTableOptions" :items="optionsItems" />
        </el-tab-pane>
        <el-tab-pane label="列配置" name="column">
          <div v-if="selection?.id && refreshComponent">
            <ma-form v-model="selection" :items="columnItems" />
          </div>
          <div v-else>
            请选择列后再进行操作
          </div>
        </el-tab-pane>
      </el-tabs>
    </div>
  </div>
</template>

<style scoped lang="scss">
:deep(.mine-card) {
  margin-left: 0; margin-right: 0;
}
:deep(.mineadmin-pro-table-header-title) {
  @apply flex gap-x-2 items-end;
  .secondary-title {
    @apply text-sm text-gray-600 dark-text-gray-400;
  }
}
:deep(.mineadmin-pro-table-toolbar) {
  @apply flex justify-between;
}
:deep(.ma-pro-table) {
  @apply mt-2
}
.block-style {
  @apply overflow-y-auto b-1 b-gray-2 rounded b-solid bg-[rgba(255,255,255,0.9)] p-2 dark-b-gray-6 dark-bg-[rgba(0,0,0,0.9)];
}
.col-style {
  @apply b-1 b-[var(--el-color-primary-light-7)] rounded b-solid bg-[var(--el-color-primary-light-9)] px-2 py-1 text-dark-1
  dark-b-[var(--el-color-primary-dark-6)] dark-bg-[var(--el-color-primary-dark-8)] dark-text-gray-3
  cursor-pointer truncate;
}
.selection {
  @apply border-2 !border-[rgb(var(--ui-primary))] shadow-lg;
}
.ghost {
  @apply opacity-30 bg-[var(--el-color-primary-light-9)] dark-bg-[var(--el-color-primary-dark-8)] !border-1;
}
</style>
