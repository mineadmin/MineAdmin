/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import MaDialog from '@/components/ma-dialog/index.vue'
import type { Component } from 'vue'

export interface UseDialogExpose {
  on: {
    ok?: (...args: any[]) => void
    cancel?: () => void
  }
  Dialog: Component
  open: (...args: any[]) => void
  close: () => void
  setTitle: (title: string) => void
}

export default function useDialog(dialogProps: Record<string, any> | null = null): UseDialogExpose {
  const isOpen = ref<boolean>(false)
  const title = ref<string>('unknown')

  const openArgs = ref<any[]>([])
  const open = (...args: any[]) => {
    openArgs.value = args
    isOpen.value = true
  }
  const close = () => isOpen.value = false

  const setTitle = (string: string) => title.value = string

  const on = ref<{
    ok: (...args: any[]) => any
    cancel: () => any
  }>({ ok: () => {}, cancel: () => {} })

  const Dialog = (props: Record<string, any> = {}) => {
    const slots = useSlots()
    const args = Object.assign(dialogProps ?? {}, props)

    return h(
      MaDialog,
      {
        'modelValue': isOpen.value,
        'onUpdate:modelValue': (v: boolean) => isOpen.value = v,
        'title': props?.title ?? title.value,
        'footer': true,
        'destroyOnClose': true,
        'onOk': () => args?.ok?.(...openArgs.value) ?? on.value?.ok?.(...openArgs.value),
        'onCancel': () => args?.close?.() ?? on.value?.cancel?.() ?? close(),
        ...args,
      },
      {
        ...slots,
        default: () => slots.default?.(...openArgs.value),
      },
    )
  }

  return {
    on: on.value,
    Dialog,
    open,
    close,
    setTitle,
  }
}
