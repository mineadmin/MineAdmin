/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { Directive, DirectiveBinding } from 'vue'
import hasDept from '@/utils/permission/hasDept'

export const dept = {
  mounted(el: HTMLElement, binding: DirectiveBinding<string | string[]>) {
    const { value } = binding
    if (value) {
      hasDept(value) || el.parentNode?.removeChild(el)
    }
    else {
      throw new Error(
        '[Directive: dept]: please provide a value, like v-dept="[\'sale\',\'accounting\']"',
      )
    }
  },
} as Directive
