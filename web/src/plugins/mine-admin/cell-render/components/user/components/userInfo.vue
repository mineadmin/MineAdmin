<script setup lang="ts">
import defaultAvatar from '@/assets/images/defaultAvatar.jpg'

const props = defineProps({
  user: {
    type: Object,
    default: () => ({}),
  },
})

</script>

<template>
  <div class="p-1">
    <div class="flex">
      <el-avatar :src="(user.avatar === '' || !user.avatar) ? defaultAvatar : user.avatar" :size="65" />
      <div class="ml-16px flex flex-col justify-center">
        <div class="text-16px">{{ user.nickname }}</div>
        <div class="mt-4px text-12px text-#aaa">账号: {{ user.username }}</div>
        <div class="mt-4px text-12px text-#aaa">手机号: {{ user.phone  }}</div>
      </div>
    </div>
<!--    <el-descriptions class="mt-16px" :column="1">-->
<!--      <el-descriptions-item label="手机号">{{ user.phone || '-' }}</el-descriptions-item>-->
<!--    </el-descriptions>-->
<!--    <el-row :gutter="12">-->
<!--      <el-col :span="8"><el-button class="w-full">测试</el-button></el-col>-->
<!--      <el-col :span="8"><el-button class="w-full">测试</el-button></el-col>-->
<!--      <el-col :span="8"><el-button class="w-full">测试</el-button></el-col>-->
<!--    </el-row>-->
  </div>
</template>

<style scoped lang="scss">

</style>
