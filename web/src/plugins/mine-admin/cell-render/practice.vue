<script setup lang="tsx">
import type { MaProTableOptions, MaProTableSchema } from '@mineadmin/pro-table'
import { onMounted } from 'vue'
import type { MaTableExpose } from '@mineadmin/table'

const proTableRef = ref()
/**
 * 加载状态
 */

const queryParams = ref({
  page: 1,
  pageSize: 15,
  origin_name: '',
  suffix: '',
})

const options: MaProTableOptions = reactive({
  adaptionOffsetBottom: 64,
  tableOptions: {
    border: true,
    data: [],
  },
  searchOptions: {

  },
  searchFormOptions: {

  },
})

const schema: MaProTableSchema = ref({
  searchItems: [
    {
      label: '存储类型',
      prop: 'storage_mode',
      render: 'input',
    },
    { label: '原始名称', prop: 'origin_name', render: 'input' },
    { label: '对象名称', prop: 'object_name', render: 'input' },
    { label: 'Hash', prop: 'hash', render: 'input' },
    { label: 'Mime Type', prop: 'mime_type', render: 'input' },
    { label: '存储路径', prop: 'storage_path', render: 'input' },
    { label: '文件后缀', prop: 'suffix', render: 'input' },
    { label: '文件大小', prop: 'size_byte', render: 'input' },
    { label: '文件大小(友好)', prop: 'size_info', render: 'input' },
    { label: '文件路径', prop: 'url', render: 'input' },
    { label: '创建时间', prop: 'created_at', render: 'input' },
    { label: '更新时间', prop: 'updated_at', render: 'input' },
  ],
  tableColumns: [
    { label: 'ID', prop: 'id' },
    { label: '存储路径', prop: 'storage_mode' },
    { label: '原始名称', prop: 'origin_name' },
    { label: '对象名称', prop: 'object_name' },
    { label: 'Hash', prop: 'hash' },
    { label: 'Mime Type', prop: 'mime_type' },
    { label: '存储路径', prop: 'storage_path' },
    { label: '文件后缀', prop: 'suffix' },
    { label: '文件大小', prop: 'size_byte' },
    { label: '文件大小(友好)', prop: 'size_info' },
    { label: '文件路径', prop: 'url' },
    { label: '创建时间', prop: 'created_at' },
    { label: '更新时间', prop: 'updated_at' },
  ],
})

async function query(): Promise<void> {
  const tableRef: MaTableExpose = proTableRef.value.getMaTableRef()
  if (!tableRef) {
    // 抛异常
    throw new Error('tableRef is undefined')
  }

  tableRef.setLoadingState(true)
  tableRef.setData([])
  return useHttp().get('/mock/attachment/list', { params: { ...queryParams.value } }).then(({ data }) => {
    setTimeout(() => {
      tableRef.setData(data.items)
      console.log(data.items, 'setData')
      console.log(tableRef)
      tableRef.setPagination({
        total: data.total,
      })
      tableRef.setLoadingState(false)
    }, Math.floor(Math.random() * 900 + 100))
  })
}

watch(queryParams, query, { deep: true })
onMounted(() => {
  query()
})
</script>

<template>
  <div class="pt-2.5">
    <!--    <ma-pro-table :options="options" :schema="schema" /> -->
    <MaProTable ref="proTableRef" :options="options" :schema="schema" />
  </div>
</template>

<style scoped lang="scss">

</style>
