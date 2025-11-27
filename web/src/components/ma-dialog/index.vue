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
import { useMagicKeys, useResizeObserver } from '@vueuse/core'
import { ElDialog } from 'element-plus'

defineOptions({ name: 'MaDialog' })

const emit = defineEmits<{
  (e: 'ok', value: any): void
  (e: 'cancel', value: any): void
}>()

const dialogRef = ref<typeof ElDialog>() as Ref<typeof ElDialog>
const dialogWidth = ref<string>('55%')
const fullscreen = defineModel<boolean>('fullscreen', { default: false })
const okLoading = ref<boolean>(false)
const cancelLoading = ref<boolean>(false)
const fsIcon = reactive({
  todo: 'mingcute:fullscreen-line',
  exit: 'mingcute:fullscreen-exit-line',
})

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

onMounted(() => {
  useResizeObserver(document.body, (entries) => {
    const [entry] = entries
    const { width } = entry.contentRect
    // xs
    if (width < 768) {
      dialogWidth.value = (attrs?.xsWidth as string) ?? '90%'
    }
    // sm
    if (width >= 768 && width < 992) {
      dialogWidth.value = (attrs?.smWidth as string) ?? '75%'
    }
    // md
    if (width >= 992 && width < 1200) {
      dialogWidth.value = (attrs?.mdWidth as string) ?? '65%'
    }
    // lg
    if (width >= 1200 && width < 1920) {
      dialogWidth.value = (attrs?.lgWidth as string) ?? '55%'
    }
  })
})
</script>

<template>
  <ElDialog
    ref="dialogRef"
    v-model="isOpen"
    :fullscreen="fullscreen"
    :width="dialogWidth"
    draggable
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
        <el-link class="el-dialog__headerbtn relative !right-[2px] !-top-[6px]" underline="never" @click="() => fullscreen = !fullscreen">
          <ma-svg-icon
            :name="fullscreen ? fsIcon.exit : fsIcon.todo"
            :size="15"
          />
        </el-link>
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
  </ElDialog>
</template>

<style scoped lang="scss">

</style>
