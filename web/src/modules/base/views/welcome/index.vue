<script setup lang="tsx">
import type { MaTableExpose } from '@mineadmin/table'
import type { MaFormExpose, MaModel } from '@mineadmin/form'
import { ElMessage } from 'element-plus'
import { themeMode, useEcharts } from '@/hooks/useEcharts'
import useTable from '@/hooks/useTable.ts'
import useForm from '@/hooks/useForm.ts'

defineOptions({ name: 'welcome' })

const userinfo = useUserStore().getUserInfo()
const icon = ref('')
const ecs = ref()
const resource = ref('')

const formModel = ref<MaModel>({
  productCode: '',
  productName: '',
  image: '',
})

useEcharts(ecs, { theme: themeMode() }).setOption({
  xAxis: {
    type: 'category',
    data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
  },
  yAxis: {
    type: 'value',
  },
  series: [
    {
      data: [150, 230, 224, 218, 135, 147, 260],
      type: 'line',
    },
  ],
})

useForm('form').then((form: MaFormExpose) => {
  form.setOptions({
    labelWidth: '100px',
    footerSlot: () => (
      <div class="flex-center">
        <el-button
          type="primary"
          onClick={() => {
            console.log(formModel.value)
            form.getElFormRef().validate()
          }}
        >
          提交
        </el-button>
        <el-button onClick={() => form.getElFormRef().resetFields()}>重置</el-button>
      </div>
    ),
  })

  form.setItems([
    {
      label: '产成品编码',
      prop: 'productCode',
      render: 'input',
      itemProps: {
        rules: [{
          required: true,
          message: '请输入产成品编码',
          trigger: 'blur',
        }],
      },
      cols: { xl: 6, lg: 8, md: 12, sm: 24 },
      renderProps: {
        placeholder: '请输入产成品编码',
      },
    },
    {
      label: '产成品名称',
      prop: 'productName',
      cols: { xl: 6, lg: 8, md: 12, sm: 24 },
      render: () => <el-input v-model={formModel.value.productName} />,
      renderProps: {
        onInput: (value: any) => ElMessage.success(value),
        placeholder: '请输入产成品名称',
        type: 'primary',
      },
      renderSlots: {
        prepend: () => '设置文本框前置文字',
      },
    },
    {
      label: '选择图片',
      prop: 'image',
      cols: { lg: 24, md: 24, sm: 24 },
      render: () => <ma-icon-picker v-model={formModel.value.image} class="w-full" />,
    },
  ])
})

useTable('table').then((table: MaTableExpose) => {
  table.setPagination({
    total: 1000,
  })

  table.setOptions({
    border: true,
    stripe: true,
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

useTable('table2').then((table: MaTableExpose) => {
  table.setPagination({
    total: 1000,
  })

  table.setOptions({
    border: true,
    stripe: true,
  })

  table.setColumns([
    { label: '姓名', prop: 'name' },
    { label: '年龄', prop: 'age' },
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
        <el-avatar :src="userinfo?.avatar" :size="80" />
        <div class="flex flex-col justify-center gap-y-2">
          <span class="text-xl">早安，{{ userinfo?.nickname ?? userinfo?.username }} 愿你今天有个好心情！</span>
          <span class="text-sm text-dark-1 dark-text-gray-3">某某公司 - 某某部门 - 技术总监</span>
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
    <div class="mine-card h-200px">
      <MaResourcePicker />
      <!--      <MaResourcePanel v-model="resource" multiple :limit="5" /> -->
    </div>
    <div ref="ecs" class="mine-card h-300px" />
    <div class="mine-card">
      <ma-form ref="form" v-model="formModel" />
    </div>
    <div class="mine-card">
      <MaIconPicker v-model="icon" />
      <ma-table ref="table" class="mt-5" />
      <ma-table ref="table2" class="mt-5" />
    </div>
  </div>
</template>
