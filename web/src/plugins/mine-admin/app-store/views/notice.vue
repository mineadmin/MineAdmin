<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<i18n lang="yaml">
zh_CN:
  title: 未设置 MINE_ACCESS_TOKEN
  stepTip: 请按照以下步骤设置：
  oneStep:
    one: 进入
    two: MineAdmin官网
    three: 设置页面，获取
  twoStep:
    one: 打开后端
    two: 配置文件
  threeStep:
    one: 添加此行配置：
    two: 获取到的
  fourStep:
    one: 重启后端服务，完成配置
zh_TW:
  title: 未設置 MINE_ACCESS_TOKEN
  stepTip: 請按照以下步驟設置：
  oneStep:
    one: 進入
    two: MineAdmin官網
    three: 在設置頁面，獲取
  twoStep:
    one: 打開後端
    two: 配置文件
  threeStep:
    one: 添加此行配置：
    two: 獲取到的
  fourStep:
    one: 重啟後端服務，完成設置
en:
  title: MINE_ACCESS_TOKEN Not Set
  stepTip: Please follow these steps to set it up：
  oneStep:
    one: Enter
    two: MineAdmin official website
    three: On the settings page, obtain
  twoStep:
    one: Open the backend
    two: Configuration file
  threeStep:
    one: Add this configuration line：
    two: The obtained
  fourStep:
    one: Restart the backend service to complete the configuration
</i18n>

<script setup lang="ts">
import useDialog from '@/hooks/useDialog.ts'
import { useLocalTrans } from '@/hooks/useLocalTrans.ts'

const t = useLocalTrans()
const { Dialog, open, close } = useDialog({
  title: t('title'),
  ok: () => close(),
})

defineExpose({ open })
</script>

<template>
  <Dialog>
    <div>
      <div class="text-base text-red-600">
        {{ t('stepTip') }}
      </div>
      <div class="ml-4">
        <ol class="mt-3 leading-7">
          <li>
            {{ t('oneStep.one') }}
            <el-link
              target="_blank"
              type="primary"
              class="-top-[1px]"
              underline="never"
              href="https://www.mineadmin.com/login?redirect=/member/setting"
            >
              {{ t('oneStep.two') }}
            </el-link>
            {{ t('oneStep.three') }} <span class="text-green-500">AccessToken</span>
          </li>
          <li>{{ t('twoStep.one') }} <span class="text-green-500">.env</span> {{ t('twoStep.two') }}</li>
          <li>
            {{ t('threeStep.one') }}
            <el-tag type="danger">
              MINE_ACCESS_TOKEN = {{ t('threeStep.two') }} AccessToken
            </el-tag>
          </li>
          <li>{{ t('fourStep.one') }}</li>
        </ol>
      </div>
    </div>
  </Dialog>
</template>

<style scoped lang="scss">

</style>
