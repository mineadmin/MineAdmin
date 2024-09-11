<script setup lang="tsx">
import type { MaProTableOptions, MaProTableSchema } from '@mineadmin/pro-table'
import { useProTableRenderPlugin } from '@mineadmin/pro-table'
import { useCellRenderTo } from './hooks/useCellRenderTo.ts'

const options: MaProTableOptions = reactive({
  tableOptions: {
    data: [
      {
        id: 1,
        name: '张三',
        age: 18,
        image: 'https://picsum.photos/200/300?random=1,https://picsum.photos/200/300?random=2,https://picsum.photos/200/300?random=3',
        content: '对我个人而言，随机一段废话不仅仅是一个重大的事件，还可能会改变我的人生. 现在, 解决随机一段废话的问题, 是非常非常重要的. 所以, 对我个人而言，随机一段废话不仅仅是一个重大的事件，还可能会改变我的人生. 从这个角度来看, 我们不妨可以这样来想: 富勒曾经说过一句著名的话, 苦难磨炼一些人，也毁灭另一些人。这似乎解答了我的疑惑. 那么, 我们都知道, 只要有意义, 那么就必须慎重考虑.这种事实',
      },
      {
        id: 2,
        name: '李四',
        age: 19,
        image: 'https://picsum.photos/200/300?random=1',
        content: '女',
      },
      {
        id: 3,
        name: '王五',
        age: 20,
        image: 'https://picsum.photos/200/300?random=1',
        content: '男',
      },
      {
        id: 4,
        name: '赵六',
        age: 21,
        image: 'https://picsum.photos/200/300?random=1',
        content: '女',
      },
    ],
  },
  searchOptions: {

  },
  searchFormOptions: {

  },
})

const schema: MaProTableSchema = reactive({
  searchItems: [],
  tableColumns: [
    {
      label: 'ID',
      prop: 'id',
      width: '50px',
      align: 'left',
    },
    {
      label: '姓名',
      prop: 'name',
      align: 'left',
    },
    {
      label: '年龄',
      prop: 'age',
      align: 'left',
    },
    {
      label: '图片',
      prop: 'image',
      align: 'left',
      cellRenderTo: useCellRenderTo('images'),
    },
    {
      label: '正文',
      prop: 'content',
      align: 'left',
      // cellRender: data => <el-button>性别</el-button>,
      // cellRenderTo: {
      //   name: 'mz-url',
      //   options: {
      //     openable: true,
      //     copyable: true,
      //   },
      // },
    },
  ],
})
console.log(useCellRenderTo('images', {

}), 3333)
console.log(useProTableRenderPlugin().getPlugins())
</script>

<template>
  <div>
    <!--    <ma-pro-table :options="options" :schema="schema" /> -->
    <MaProTable :options="options" :schema="schema" />
  </div>
</template>

<style scoped lang="scss">

</style>
