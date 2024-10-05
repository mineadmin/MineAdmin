/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { Dictionary } from '#/global'

const useDictStore = defineStore(
  'useDictStore',
  () => {
    const dictMap = ref(new Map<string, Dictionary[]>())

    const find = (name: string): Dictionary[] | null => {
      return dictMap.value.get(name) ?? null
    }

    const push = (name: string, data: Dictionary[], replace: boolean = false): boolean => {
      if (!dictMap.value.has(name)) {
        dictMap.value.set(name, data)
        return true
      }
      else if (dictMap.value.has(name) && replace) {
        dictMap.value.set(name, data)
        return true
      }
      else {
        return false
      }
    }

    const append = (name: string, item: Dictionary): boolean => {
      if (!dictMap.value.has(name)) {
        return false
      }
      else {
        const data = dictMap.value.get(name) as Dictionary[]
        data.push(item)
        dictMap.value.set(name, data)
        return true
      }
    }

    const remove = (name: string): boolean => {
      dictMap.value.has(name) && dictMap.value.delete(name)
      return true
    }

    const all = (): Map<string, Dictionary[]> => dictMap.value

    const clear = () => dictMap.value.clear()

    const t = (name: string, value: any, attrName: string = 'label'): any | null => {
      const data = find(name)
      if (!data) {
        return null
      }

      // eslint-disable-next-line eqeqeq
      const dictItem = data?.find((item: Dictionary) => item.value == value) ?? null

      if (!dictItem || dictItem[attrName] === undefined) {
        return null
      }

      return dictItem[attrName]
    }

    return {
      find,
      push,
      remove,
      append,
      all,
      clear,
      t,
    }
  },
)

export default useDictStore
