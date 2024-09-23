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
defineOptions({ name: 'MaDialog' })

const fullscreen = ref<boolean>(false)
const fsIcon = reactive({
  todo: 'mingcute:fullscreen-line',
  exit: 'mingcute:fullscreen-exit-line',
})
</script>

<template>
  <el-dialog
    v-bind="$attrs"
    :close-on-click-modal="false"
    :fullscreen="fullscreen"
    draggable
    append-to-body
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
  </el-dialog>
</template>

<style scoped lang="scss">

</style>
