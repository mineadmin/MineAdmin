/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import MaDrawer from '@/components/ma-drawer/index.vue'
import type { Component } from 'vue'

export interface UseDrawerExpose {
  on: {
    ok?: (...args: any[]) => void
    cancel?: (...args: any[]) => void
  }
  Drawer: Component
  open: (...args: any[]) => void
  close: () => void
  setTitle: (title: string) => void
  setAttr: (attr: Record<string, any>) => void
}

export default function useDrawer(drawerProps: Record<string, any> | null = null): UseDrawerExpose {
  const isOpen = ref<boolean>(false)
  const title = ref<string>('unknown')

  const openArgs = ref<any[]>([])
  const closeArgs = ref<any[]>([])
  const open = (...args: any[]) => {
    openArgs.value = args
    closeArgs.value = args
    isOpen.value = true
  }
  const close = () => {
    isOpen.value = false
  }

  const setTitle = (string: string) => title.value = string
  const setAttr = (attr: Record<string, any>) => Object.assign(drawerProps ?? {}, attr)

  const on = ref<{
    ok: (...args: any[]) => any
    cancel: (...args: any[]) => any
  }>({ ok: () => {}, cancel: () => {} })

  const Drawer = (props: Record<string, any> = {}) => {
    const slots = useSlots()
    const args = Object.assign(drawerProps ?? {}, props)

    if (args?.appendToBody === undefined) {
      args.appendToBody = true
    }

    if (args?.closeOnClickModal === undefined) {
      args.closeOnClickModal = false
    }

    return h(
      MaDrawer,
      {
        'modelValue': isOpen.value,
        'onUpdate:modelValue': (v: boolean) => isOpen.value = v,
        'title': props?.title ?? title.value,
        'footer': true,
        'destroyOnClose': true,
        ...args,
        'onOk': ({ okLoadingState }) => {
          openArgs.value.push(okLoadingState)
          return args?.ok?.(...openArgs.value) ?? on.value?.ok?.(...openArgs.value)
        },
        'onCancel': ({ cancelLoadingState }) => {
          closeArgs.value.push(cancelLoadingState)
          return (args?.cancel?.(...closeArgs.value) ?? on.value?.cancel?.(...closeArgs.value) ?? true)
            ? (cancelLoadingState(false) || close())
            : null
        },
      },
      {
        ...slots,
        default: () => slots.default?.(...openArgs.value),
      },
    )
  }

  return {
    on: on.value,
    Drawer,
    open,
    close,
    setTitle,
    setAttr,
  }
}
