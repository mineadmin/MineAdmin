/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */

import { useI18n } from 'vue-i18n'
import type { ComposerTranslation } from 'vue-i18n'

export function useTrans(key: any | null = null): ComposerTranslation | string | any {
  return key === null ? useI18n().t as ComposerTranslation : useI18n().t(key) as string
}
