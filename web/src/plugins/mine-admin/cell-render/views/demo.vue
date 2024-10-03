<script setup lang="tsx">
import type { MaProTableOptions, MaProTableSchema } from '@mineadmin/pro-table'
import { onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import { useCellRender } from '$/mine-admin/cell-render/hooks/useCellRender.tsx'
import { useMessage } from '@/hooks/useMessage.ts'
import { useResourcePicker } from '@/hooks/useResourcePicker.ts'

const message = useMessage()
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
  adaptionOffsetBottom: 160,
  tableOptions: {
    border: true,
    stripe: true,
    data: [],
  },
  // 右键菜单
  rowContextMenu: {
    enabled: true,
    items: [{
      label: '删除此条数据',
      icon: 'i-ri:close-line',
      disabled: false,
      onMenuClick: (row, column, event) => {
        message.success(`删除成功：${row.origin_name}`)
      },
    }],
  },
  searchOptions: {
    fold: true,
  },
  requestOptions: {
    // api: (params: any) => {
    //   return new Promise((resolve) => {
    //     // 模拟网络延时
    //     setTimeout(() => {
    //       useHttp().get('/admin/attachment/list', { params }).then(resolve)
    //     }, Math.floor(Math.random() * 900 + 100))
    //   })
    // },
    response: {
      dataKey: 'items',
    },
  },
})

const schema: MaProTableSchema = reactive({
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
    { type: 'sort', width: '70px' },
    { label: 'ID', prop: 'id' },
    {
      label: '存储类型',
      prop: 'storage_mode',
      align: 'left',
      cellRender: useCellRender().label({
        1: '七牛云',
        2: <el-tag type="danger">本地</el-tag>,
        3: <el-tag type="success">腾讯云</el-tag>,
      }),
      // cellRenderTo: useCellRenderTo().label({
      //   1: '本地',
      //   2: '七牛云',
      //   3: <el-tag>腾讯云</el-tag>,
      // }),
      // cellRenderTo: {
      //   name: 'ma-label',
      //   props: {
      //     map: {
      //       1: '本地',
      //       2: '七牛云',
      //       3: <el-tag>腾讯云</el-tag>,
      //     },
      //   },
      // },
    },
    { label: '原始名称', prop: 'origin_name' },
    { label: '对象名称', prop: 'object_name' },
    { label: 'Hash', prop: 'hash' },
    { label: 'Mime Type', prop: 'mime_type' },
    { label: '存储路径', prop: 'storage_path' },
    {
      label: '文件后缀',
      prop: 'suffix',
      // cellRender: useCellRender().tag(),
      cellRenderTo: { name: 'tag' },
    },
    { label: '文件大小', prop: 'size_byte' },
    { label: '文件大小(友好)', prop: 'size_info' },
    {
      label: '文件路径',
      prop: 'url',
      align: 'left',
      width: 300,
      cellRender: useCellRender().image(),
    },
    {
      label: '状态',
      prop: 'status',
      // cellRender: useCellRender().switch('/mock/switch/changeStatus'),
      cellRender: useCellRender().switch('admin/attachment/list', {
        beforeChange: (value, row, scope) => message.confirm(value === 2 ? '确定要启用吗？' : '确定要禁用吗？'),
      }),
    },
    { label: '创建时间', prop: 'created_at' },
    { label: '更新时间', prop: 'updated_at' },
    { type: 'operation',
      operationConfigure: {
        type: 'tile',
        actions: [
          {
            name: 'edit', text: '编辑', onClick: (row) => {
              console.log(row)
            }, icon: 'i-ri:refresh-line',
          },
        ],
      },
    },
  ],
})

// async function query(): Promise<void> {
//   const tableRef: MaTableExpose = proTableRef.value.getMaTableRef()
//   if (!tableRef) {
//     // 抛异常
//     throw new Error('tableRef is undefined')
//   }
//
//   tableRef.setLoadingState(true)
//   tableRef.setData([])
//   return useHttp().get('/mock/attachment/list', { params: { ...queryParams.value } }).then(({ data }) => {
//     setTimeout(() => {
//       tableRef.setData(data.items)
//       console.log(data.items, 'setData')
//       console.log(tableRef)
//       tableRef.setPagination({
//         total: data.total,
//       })
//       tableRef.setLoadingState(false)
//     }, Math.floor(Math.random() * 900 + 100))
//   })
// }

// watch(queryParams, query, { deep: true })
onMounted(() => {
  // query()
})

// 打印所有插件
const dialogVisible = ref(false)
function onClickx() {
  useResourcePicker({
    multiple: true,
    limit: 2,
    defaultFileType: 'image',
    // fileTypes: [
    //   {
    //     label: '图片',
    //     value: 'image',
    //     suffix: 'jpg,png,gif,jpeg',
    //   },
    //   {
    //     label: '视频',
    //     value: 'video',
    //     suffix: 'mp4,avi,wmv,mov,flv,mkv webm',
    //   },
    // ],
    onConfirm: (value) => {
      ElMessage.warning('您选择了资源,请控制台查看')
      console.log(value)
    },
    onCancel: () => {
      ElMessage.warning('您取消资源选择')
    },
  })
}
</script>

<template>
  <div class="pt-2.5">
    <!--    <ma-pro-table :options="options" :schema="schema" /> -->
    <MaProTable ref="proTableRef" :options="options" :schema="schema" />
    <el-button @click="onClickx">
      资源选择
    </el-button>
    <!--    <MaResourceDialog v-model:visible="dialogVisible" /> -->
  </div>
</template>

<style scoped lang="scss">

</style>
