<script setup lang="tsx">
import type { MaProTableOptions, MaProTableSchema } from '@mineadmin/pro-table'
import { useCellRender } from '$/mine-admin/cell-render/hooks/useAsCellRender.tsx'

const options: MaProTableOptions = reactive({
  adaptionOffsetBottom: 160,
  tableOptions: {
    border: true,
    stripe: true,
    data: [],
  },
  searchOptions: {
    fold: true,
  },
  requestOptions: {
    api: 'baidu.com',
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
</script>

<template>
  <MaProTable ref="proTableRef" :options="options" :schema="schema" />
</template>
