<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<script setup lang="tsx">
import type { MaTableExpose } from '@mineadmin/table'
import useTable from '@/hooks/useTable.ts'

useTable('table').then((table: MaTableExpose) => {
  table.setOptions({
    stripe: true,
  })
  table.setColumns([
    { type: 'index', label: '排名', width: '80px' },
    { prop: 'author', label: '作者' },
    { prop: 'content', label: '内容量' },
    { prop: 'click', label: '点击量' },
  ])

  table.setData([
    { author: '张三', content: '5000字', click: '9000' },
    { author: '李四', content: '4500字', click: '12000' },
    { author: '王五', content: '3500字', click: '7000' },
    { author: '赵六', content: '4800字', click: '8500' },
    { author: '刘七', content: '5200字', click: '11000' },
    { author: '孙八', content: '3000字', click: '6000' },
    { author: '周九', content: '5500字', click: '9800' },
  ])
})
</script>

<template>
  <div class="mine-card w-auto !ml-3 xl:w-4/12 !xl:ml-0">
    <div class="text-bas1 mt-1.5">
      <div>热门作者榜单</div>
    </div>

    <div class="mt-5">
      <ma-table ref="table" />
    </div>
  </div>
</template>

<style scoped lang="scss">

</style>
