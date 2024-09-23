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
import { useResizeObserver } from '@vueuse/core'
import { ElDialog } from 'element-plus'
import type { Ref } from 'vue'

defineOptions({ name: 'MaDialog' })

const dialogRef = ref<typeof ElDialog>() as Ref<typeof ElDialog>
const dialogWidth = ref<string>('55%')
const fullscreen = ref<boolean>(false)
const fsIcon = reactive({
  todo: 'mingcute:fullscreen-line',
  exit: 'mingcute:fullscreen-exit-line',
})

onMounted(() => {
  useResizeObserver(document.body, (entries) => {
    const [entry] = entries
    const { width } = entry.contentRect
    // xs
    if (width < 768) {
      dialogWidth.value = '90%'
    }
    // sm
    if (width >= 768 && width < 992) {
      dialogWidth.value = '75%'
    }
    // md
    if (width >= 992 && width < 1200) {
      dialogWidth.value = '65%'
    }
    // md
    if (width >= 1200 && width < 1920) {
      dialogWidth.value = '55%'
    }
  })
})
</script>

<template>
  <ElDialog
    ref="dialogRef"
    :fullscreen="fullscreen"
    :width="dialogWidth"
    :close-on-click-modal="false"
    draggable
    append-to-body
    v-bind="$attrs"
  >
    <template #default>
      <slot name="default" />
    </template>
    <template #header>
      <div class="relative flex items-center justify-between">
        <div>
          <slot name="header">
            {{ $attrs.title ?? '' }}
          </slot>
        </div>
        <el-link class="relative -top-4px" :underline="false">
          <ma-svg-icon
            :name="fullscreen ? fsIcon.exit : fsIcon.todo"
            :size="15"
            @click="() => fullscreen = !fullscreen"
          />
        </el-link>
      </div>
    </template>
    <template #footer>
      <slot name="footer" />
    </template>
  </ElDialog>
</template>

<style scoped lang="scss">

</style>
