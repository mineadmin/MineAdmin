<script setup lang="tsx">
import type { MaTableExpose } from '@mineadmin/table'
import useTable from '@/hooks/useTable.ts'

defineOptions({ name: 'welcome' })

const userinfo = useUserStore().getUserInfo()
const icon = ref('')

useTable('table').then((table: MaTableExpose) => {
  table.setPagination({
    total: 1000,
  })

  table.setColumns([
    { label: '姓名', prop: 'name' },
    { label: '年龄', prop: 'age' },
    { label: '地址', prop: 'address' },
  ])

  table.setData([
    { name: '张三', age: 18, address: '上海市普陀区金沙江路 1518 弄' },
    { name: '李四', age: 24, address: '上海市普陀区金沙江路 1517 弄' },
    { name: '王五', age: 28, address: '上海市普陀区金沙江路 1519 弄' },
    { name: '赵六', age: 32, address: '上海市普陀区金沙江路 1516 弄' },
    { name: '田七', age: 36, address: '上海市普陀区金沙江路 1516 弄' },
  ])
})
</script>

<template>
  <div class="mine-layout">
    <div class="flex justify-between bg-white p-5 dark-bg-dark-8">
      <div class="flex gap-x-5">
        <el-avatar v-role="['userinfo:add']" :src="userinfo?.avatar" :size="80" />
        <div class="flex flex-col justify-center gap-y-2">
          <span class="text-xl">早安，{{ userinfo?.nickname ?? userinfo?.username }} 愿你今天有个好心情！</span>
          <span class="text-sm text-gray-5">某某公司 - 某某部门 - 技术总监</span>
        </div>
      </div>
    </div>

    <div class="flex justify-between p-2">
      <div class="mine-card w-8/12">
        <div class="text-base">
          <div>进行中的项目</div>
          <div class="grid grid-cols-3 mt-3">
            <div class="rounded p-3 transition-all duration-300 hover-shadow-md">
              asd
            </div>
          </div>
        </div>
      </div>
      <div class="mine-card w-4/12">
        asd
      </div>
    </div>
    <div class="mine-card">
      <ma-icon-picker v-model="icon" />

      <ma-table ref="table" class="mt-5" />
    </div>
  </div>
</template>
