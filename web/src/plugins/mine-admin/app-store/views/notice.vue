<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<script setup lang="ts">
import useDialog from '@/hooks/useDialog.ts'

const { Dialog, open, close } = useDialog({
  title: '未设置 MINE_ACCESS_TOKEN',
  ok: () => close(),
})

defineExpose({ open })
</script>

<template>
  <Dialog>
    <div>
      <div class="text-base text-red-600">
        请按照以下步骤设置：
      </div>
      <div class="ml-4">
        <ol class="mt-3 leading-7">
          <li>
            进入
            <el-link
              target="_blank"
              type="primary"
              class="-top-[1px]"
              :underline="false"
              href="https://www.mineadmin.com/login?redirect=/member/setting"
            >
              MineAdmin官网
            </el-link>
            设置页面，获取 <span class="text-green-500">AccessToken</span>
          </li>
          <li>打开后端 <span class="text-green-500">.env</span> 配置文件</li>
          <li>
            添加此行配置：<el-tag type="danger">
              MINE_ACCESS_TOKEN = 获取到的AccessToken
            </el-tag>
          </li>
          <li>重启后端服务，完成配置</li>
        </ol>
      </div>
    </div>
  </Dialog>
</template>

<style scoped lang="scss">

</style>
