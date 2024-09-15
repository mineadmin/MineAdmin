<script setup lang="tsx">
defineOptions({ name: 'welcome' })
const userinfo = useUserStore().getUserInfo()
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
  </div>
</template>
