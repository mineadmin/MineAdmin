/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */

import { getCurrentInstance } from 'vue'
import { globalComposer } from '@/i18n'
import { useI18n } from 'vue-i18n'
import type { ComposerTranslation } from 'vue-i18n'

export interface TransType {
  globalTrans: ComposerTranslation
  localTrans: ComposerTranslation
}

const localComposerMap = new WeakMap<object, any>()
const localTransMap = new WeakMap<object, ComposerTranslation>()

function translate(composer: any, key: any, args: any[]) {
  if (typeof key !== 'string' || !composer.te(key)) {
    return undefined
  }

  return composer.t(key, ...args)
}

const globalTrans = ((key: any, ...args: any[]) => {
  return translate(globalComposer, key, args) ?? key
}) as ComposerTranslation

function getLocalComposer(instance: object) {
  let composer = localComposerMap.get(instance)
  if (!composer) {
    composer = useI18n({
      inheritLocale: true,
      useScope: 'local',
    })
    localComposerMap.set(instance, composer)
  }

  return composer
}

function getLocalTrans(): ComposerTranslation {
  const instance = getCurrentInstance()
  if (!instance) {
    return globalTrans
  }

  let translator = localTransMap.get(instance)
  if (!translator) {
    const localComposer = getLocalComposer(instance)
    translator = ((key: any, ...args: any[]) => {
      return translate(localComposer, key, args) ?? translate(globalComposer, key, args) ?? key
    }) as ComposerTranslation
    localTransMap.set(instance, translator)
  }

  return translator
}

export function useTrans(key: any | null = null): TransType | string | any {
  if (key === null) {
    return {
      globalTrans,
      get localTrans() {
        return getLocalTrans()
      },
    }
  }
  else {
    return globalTrans(key)
  }
}
