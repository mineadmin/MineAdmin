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
import hasPost from '@/utils/permission/hasPost'

export const post = {
  mounted(el: HTMLElement, binding: DirectiveBinding<string | string[]>) {
    const { value } = binding
    if (value) {
      hasPost(value) || el.parentNode?.removeChild(el)
    }
    else {
      throw new Error(
        '[Directive: post]: please provide a value, like v-post="[\'vp\',\'manger\']"',
      )
    }
  },
} as Directive
