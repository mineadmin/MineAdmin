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

export default function useDialog(dialogProps: Record<string, any> | null) {
  const isOpen = ref<boolean>(false)
  const openArgs = ref<any[]>([])
  const open = (...args: any[]) => {
    openArgs.value = args
    isOpen.value = true
  }
  const close = () => isOpen.value = false
  const title = ref<string>('unknown')

  const setTitle = (string: string) => title.value = string

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
        'onOk': async (e: Event) => await props?.ok?.(e),
        'onCancel': async (e: Event) => await props?.cancel?.(e),
        ...args,
      },
      {
        ...slots,
        default: () => slots.default?.(...openArgs.value),
      },
    )
  }

  return {
    Dialog,
    open,
    close,
    setTitle,
  }
}
