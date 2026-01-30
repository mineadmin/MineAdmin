<script setup lang="tsx">
import useDialog from '@/hooks/useDialog.js'
import type { MaProTableExpose } from '@mineadmin/pro-table'
import { getTableList } from '$/mine-admin/code-generator/api'

const options = inject<any>('options')
const dbRef = ref<MaProTableExpose>()
const cardItems = reactive([
  {
    title: '从零生成',
    desc: '在线设计数据表结构，生成数据表和表迁移，并生成代码',
    icon: 'heroicons:beaker',
    type: 'create',
  },
  {
    title: '从数据表生成',
    desc: '根据已有数据表生成表迁移文件和代码',
    icon: 'heroicons:code-bracket-square',
    type: 'db',
  },
  {
    title: '从历史记录生成',
    desc: '选择历史生成记录，重新生成代码',
    icon: 'heroicons:check-16-solid',
    type: 'history',
  },
])

const { open, close, handleEvent, setTitle, setAttr, Dialog } = useDialog()

async function handleClick(type: string) {
  function execute() {
    options.value.createType = type
    options.value.isHomePage = false
    options.value.typeInfo = cardItems.find(item => item.type === type)
  }

  if (type === 'create') {
    execute()
  }
  else {
    open({ type })
    if (type === 'db') {
      setTitle('从数据表生成')
      setAttr({
        content: '请选择数据表',
        width: '50%',
      })
      handleEvent.ok = () => {
        execute()
      }
    }
    else {
      setTitle('从数据表生成')
    }
  }
}
</script>

<template>
  <div class="relative z-5 grid grid-cols-3 mx-auto mt-15 w-[80%] gap-x-3">
    <el-card
      v-for="(item, i) in cardItems" :key="i" class="cursor-pointer" shadow="hover"
      @click="async () => handleClick(item.type)"
    >
      <div class="text-2xl">
        <ma-svg-icon :name="item.icon" class="relative top-1" /> {{ item.title }}
      </div>
      <div class="mt-5 text-base text-gray-500">
        {{ item.desc }}
      </div>
    </el-card>

    <Component :is="Dialog">
      <template #default="{ type }">
        <ma-pro-table
          v-show="type === 'db'"
          ref="dbRef"
          :schema="{
            tableColumns: [
              { label: '数据表', prop: 'name' },
              { label: '表描述', prop: 'comment' },
              { label: '表引擎', prop: 'engine' },
              { label: '字符集', prop: 'collation' },
            ],
            searchItems: [
              { label: '数据表', prop: 'name', render: 'input', renderProps: { placeholder: '按名称搜索数据表' } },
            ],
          }"
          :options="{
            header: {
              show: false,
            },
            toolbar: false,
            tableOptions: {
              adaption: false,
            },
            requestOptions: {
              api: getTableList,
            },
          }"
        />
      </template>
    </Component>
  </div>
</template>
