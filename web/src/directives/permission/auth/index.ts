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
import hasAuth from '@/utils/permission/hasAuth'

export const auth = {
  mounted(el: HTMLElement, binding: DirectiveBinding<string | string[]>) {
    const { value } = binding
    if (value) {
      hasAuth(value) || el.parentNode?.removeChild(el)
    }
    else {
      throw new Error(
        '[Directive: auth]: please provide a value, like v-auth="[\'user:add\',\'user:edit\']"',
      )
    }
  },
} as Directive
