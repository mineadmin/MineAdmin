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

export function useLocalTrans(key: any | null = null): string | ComposerTranslation | any {
  const { t } = useI18n({
    inheritLocale: true,
    useScope: 'local',
  })
  return key === null ? t as ComposerTranslation : t(key) as string
}
