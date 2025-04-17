<script setup lang="ts">
import type { MaProTableColumns, MaProTableOptions, MaSearchItem } from '@mineadmin/pro-table'

defineOptions({ name: 'MaSelectTable' })

const props = defineProps<{
  api: any
  multiple?: boolean
  rowKey?: string
  showKey?: string
  columns?: MaProTableColumns[]
  searchItems?: MaSearchItem[]
  proTableOptions?: MaProTableOptions
  selectProps?: Record<string, any>
}>()

const multiple = ref<boolean>(props?.multiple ?? false)
const rowKey = ref<string>(props?.rowKey ?? 'id')
const showKey = ref<string>(props?.showKey ?? 'id')
const selectModel = ref<any>()

const model = defineModel<any>()

const options = Object.assign(
  props?.proTableOptions ?? {}, {
    tableOptions: {
      adaption: false,
      on: {
        onRowClick: (row: any) => {
          if (!multiple.value) {
            row.__isSelection = !row?.__isSelection
            if (row.__isSelection) {
              model.value = row
              selectModel.value = model.value[showKey.value]
            }
            else {
              model.value = null
              selectModel.value = null
            }
          }
          else {
            row.__isSelection = !row?.__isSelection
            if (row.__isSelection) {
              if (!model.value.find((item: any) => item[rowKey.value] === row[rowKey.value])) {
                model.value.push(row)
              }
            }
            else {
              model.value = model.value.filter((item: any) => item[rowKey.value] !== row[rowKey.value])
            }
            selectModel.value = model.value.map((item: any) => item[showKey.value])
          }
        },
      },
    },
    tools: { show: false },
    header: { show: false },
    requestOptions: {
      api: props.api,
    },
  },
)

const cols = props?.columns ?? []

onMounted(() => {
  if (multiple.value) {
    selectModel.value = []
    model.value = []
  }
  else {
    selectModel.value = null
    model.value = null
  }
  cols.unshift({ label: '#', prop: '__selections__', width: '50' })
})
</script>

<template>
  <el-select
    v-model="selectModel"
    :max-collapse-tags="3"
    v-bind="props?.selectProps ?? {}"
    :collapse-tags="multiple"
    :multiple="multiple"
  >
    <template #empty>
      <ma-pro-table
        :options="options"
        :schema="{ tableColumns: cols, searchItems: props?.searchItems ?? [] }"
      >
        <template #toolbarLeft>
          <el-alert type="success">
            单击行选择数据
          </el-alert>
        </template>
        <template #column-__selections__="{ row }">
          <div class="flex items-center justify-center">
            <ma-svg-icon v-if="row.__isSelection === true" name="heroicons:check-16-solid" class="text-green-7" :size="20" />
          </div>
        </template>
      </ma-pro-table>
    </template>
  </el-select>
</template>

<style scoped>
:deep(.mine-card) {
  padding: 0;
}
</style>
