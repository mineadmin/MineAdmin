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

export interface TransType {
  globalTrans: ComposerTranslation
  localTrans: ComposerTranslation
}

export function useTrans(key: any | null = null): TransType | string | any {
  const global = useI18n()
  const local = useI18n({
    inheritLocale: true,
    useScope: 'local',
  })

  if (key === null) {
    return {
      localTrans: local.t,
      globalTrans: global.t,
    }
  }
  else {
    return global.te(key) ? global.t(key) : local.te(key) ? local.t(key) : key
  }
}
