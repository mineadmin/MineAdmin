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
import type { Ref } from 'vue'
import { useMagicKeys } from '@vueuse/core'
import type { ElDrawer } from 'element-plus'

defineOptions({ name: 'MaDrawer' })

const emit = defineEmits<{
  (e: 'ok', value: any): void
  (e: 'cancel', value: any): void
}>()

const drawerRef = ref<typeof ElDrawer>() as Ref<typeof ElDrawer>
const okLoading = ref<boolean>(false)
const cancelLoading = ref<boolean>(false)

function okLoadingState(state: boolean) {
  okLoading.value = state
}

function cancelLoadingState(state: boolean) {
  cancelLoading.value = state
}

const attrs = useAttrs()
const t = useTrans().globalTrans

const isOpen = defineModel<boolean>({ default: false })

function ok() {
  emit('ok', { okLoadingState, attrs })
}

function cancel() {
  emit('cancel', { cancelLoadingState, attrs })
}

const { current } = useMagicKeys()
const keys = computed(() => Array.from(current))

watch(() => keys.value, async () => {
  const [one, two] = keys.value
  if (isOpen.value && one === 'control' && two === 'enter') {
    ok()
  }
})
</script>

<template>
  <ElDrawer
    ref="drawerRef"
    v-model="isOpen"
    v-bind="$attrs"
  >
    <template #default>
      <div v-loading="$attrs.loading ?? false">
        <slot name="default" />
      </div>
    </template>
    <template #header>
      <div class="relative flex items-center justify-between">
        <div>
          <slot name="header">
            {{ $attrs.title ?? '' }}
          </slot>
        </div>
      </div>
    </template>
    <template #footer>
      <slot name="footerBefore" />
      <slot v-if="$attrs.footer" name="footer">
        <el-button type="primary" :loading="okLoading" @click="ok">
          {{ $attrs?.okText ?? `${t('crud.ok')} Ctrl + Enter` }}
        </el-button>
        <el-button :loading="cancelLoading" @click="cancel">
          {{ $attrs?.cancelText ?? `${t('crud.cancel')} Esc` }}
        </el-button>
      </slot>
      <slot name="footerAfter" />
    </template>
  </ElDrawer>
</template>

<style scoped lang="scss">

</style>
