/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import Message from 'vue-m-message'
import { useI18n } from 'vue-i18n'
import type { MineTabbar } from '#/global'
import useTabStore from '@/store/modules/useTabStore.ts'
import useCache from '@/hooks/useCache.ts'

export default function useTabCollection() {
  const { set, get } = useCache()
  const tabCollection = ref<MineTabbar[]>([])
  const { getCurrentTab } = useTabStore()
  const { t } = useI18n()

  function addToCollection(tab: MineTabbar | null = null) {
    const toTab = tab ?? getCurrentTab() as MineTabbar
    if (!tabCollection.value.find(item => item.fullPath === toTab.fullPath)) {
      tabCollection.value.push(toTab)
    }
    else {
      Message.info(t('mineAdmin.tab.has'))
    }
  }

  function removeCollection(tab: MineTabbar) {
    tabCollection.value = tabCollection.value.filter(item => item.fullPath !== tab.fullPath)
  }

  tabCollection.value = get('tab_collection', [])
  watch(() => tabCollection.value, () => set('tab_collection', tabCollection.value), { immediate: true, deep: true })

  return {
    tabCollection,
    addToCollection,
    removeCollection,
  }
}
