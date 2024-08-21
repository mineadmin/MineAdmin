/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import { useClipboard, useEventListener } from '@vueuse/core'
import type { Directive, DirectiveBinding } from 'vue'
import Message from 'vue-m-message'

export interface CopyElement extends HTMLElement {
  copyValue: string
}

export const copy = {
  mounted(el: CopyElement, binding: DirectiveBinding<string>) {
    const { isSupported, copy } = useClipboard()
    if (!isSupported.value) {
      throw new Error('[Directive: copy]: Your browser does not support Clipboard API')
    }
    const { value } = binding
    if (value) {
      el.copyValue = value
      const arg = binding.arg ?? 'dblclick'
      // Register using addEventListener on mounted, and removeEventListener automatically on unmounted
      useEventListener(el, arg, async () => {
        copy(el.copyValue).then(() => {
          Message.success('复制成功')
        }).catch((err) => {
          console.error(err)
        })
      })
    }
    else {
      throw new Error(
        '[Directive: copy]: please provide a value, like v-copy="modelValue"',
      )
    }
  },
  updated(el: CopyElement, binding: DirectiveBinding) {
    el.copyValue = binding.value
  },
} as Directive
