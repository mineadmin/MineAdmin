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
en:
  showInputPlaceholder: Click the back button to select the icon
  selectedIcon: Select icon
  clear: Clear
zh_CN:
  showInputPlaceholder: 点击后面按钮选择图标
  selectedIcon: 选择图标
  clear: 清除
zh_TW:
  showInputPlaceholder: 點擊後面按鈕選擇圖示
  selectedIcon: 選擇圖示
  clear: 清除
</i18n>

<script setup lang="ts">
import MaIconPanel from './ma-icon-panel.vue'
import { useLocalTrans } from '@/hooks/useLocalTrans.ts'

defineOptions({ name: 'MaIconPicker' })

const model = defineModel<string>()
const iconPanelRef = ref()

const dialogVisible = ref<boolean>(false)
</script>

<template>
  <div>
    <div>
      <el-input
        v-model="model"
        class="relative w-full"
        readonly
        :placeholder="useLocalTrans('showInputPlaceholder')"
      >
        <template v-if="model" #prefix>
          <ma-svg-icon :name="model" :size="20" />
        </template>
        <template #suffix>
          <el-button type="primary" class="absolute right-0 rounded-none" @click.prevent="dialogVisible = true">
            {{ useLocalTrans('selectedIcon') }}
          </el-button>
        </template>
        <template #append>
          <el-button
            @click="() => {
              model = ''
              iconPanelRef?.clear()
            }"
          >
            {{ useLocalTrans('clear') }}
          </el-button>
        </template>
      </el-input>
    </div>

    <el-dialog v-model="dialogVisible" :title="useLocalTrans('selectedIcon')" width="800" append-to-body draggable destroy-on-close align-center>
      <MaIconPanel
        ref="iconPanelRef"
        @select="(icon: string) => {
          model = icon
          dialogVisible = false
        }"
      />
    </el-dialog>
  </div>
</template>

<style scoped lang="scss">

</style>
