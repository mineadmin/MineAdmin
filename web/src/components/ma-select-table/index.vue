<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<script setup lang="ts">
import type { MaProTableColumns, MaProTableExpose, MaProTableOptions, MaSearchItem } from '@mineadmin/pro-table'

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
const selectTableRef = ref<MaProTableExpose>()
const selectRef = ref()
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
          if (multiple.value) {
            const index = model.value.findIndex((item: any) => item[rowKey.value] === row[rowKey.value])
            if (index === -1) {
              model.value.push(row)
            }
            else {
              model.value.splice(index, 1)
            }
          }
          else {
            if (model.value?.[rowKey.value] && row[rowKey.value] === model.value[rowKey.value]) {
              model.value = null
            }
            else {
              model.value = row
            }
            selectRef.value?.blur()
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

function isRowSelected(row: any) {
  if (multiple.value) {
    return Array.isArray(model.value) && model.value.some(item => item?.[rowKey.value] === row?.[rowKey.value])
  }
  else {
    return model.value?.[rowKey.value] === row?.[rowKey.value]
  }
}

watch(() => model.value, () => {
  if (multiple.value && model.value?.length > 0) {
    selectModel.value = model.value.map((item: any) => item[showKey.value])
  }
  if (!multiple.value) {
    selectModel.value = model.value?.[showKey.value] ?? null
  }
}, { immediate: true, deep: true })

onMounted(() => {
  cols.unshift({ label: '#', prop: '__selections__', width: '50' })
  if (multiple.value && !model.value) {
    model.value = []
  }
})
</script>

<template>
  <el-select
    ref="selectRef"
    v-model="selectModel"
    :max-collapse-tags="3"
    v-bind="props?.selectProps ?? {}"
    :collapse-tags="multiple"
    :multiple="multiple"
    clearable
    @clear="() => {
      if (multiple) {
        model = []
        selectModel = []
      }
      else {
        model = null
        selectModel = null
      }
    }"
  >
    <template #empty>
      <ma-pro-table
        ref="selectTableRef"
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
            <ma-svg-icon
              v-if="isRowSelected(row)"
              name="heroicons:check-16-solid"
              class="text-green-7"
              :size="20"
            />
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
