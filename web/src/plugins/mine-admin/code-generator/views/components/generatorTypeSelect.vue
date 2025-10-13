<script setup>
const options = inject('options')
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

function open(type) {
  options.value.createType = type
  options.value.isHomePage = false
  options.value.typeInfo = cardItems.find(item => item.type === type)
}
</script>

<template>
  <div class="relative z-5 grid grid-cols-3 mx-auto mt-15 w-[80%] gap-x-3">
    <el-card
      v-for="(item, i) in cardItems" :key="i" class="h-30 cursor-pointer" shadow="hover"
      @click="open(item.type)"
    >
      <div class="text-2xl">
        <ma-svg-icon :name="item.icon" class="relative top-1" /> {{ item.title }}
      </div>
      <div class="mt-5 text-base text-gray-500">
        {{ item.desc }}
      </div>
    </el-card>
  </div>
</template>
