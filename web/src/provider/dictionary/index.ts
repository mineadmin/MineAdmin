/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */

import type { Dictionary, ProviderService } from '#/global'
import type { App } from 'vue'

const dictionary: Record<string, Dictionary[]> = {}
async function getDictionary() {
  const data = import.meta.glob('./data/**.{ts,js}')
  for (const dic in data) {
    const d: any = await data[dic]()
    const name: string | undefined = dic.match('/data/(.*).(ts|js)')?.[1] ?? undefined
    if (name) {
      dictionary[name] = d.default
    }
  }
}

const provider: ProviderService.Provider = {
  name: 'dictionary',
  async init() {
    await getDictionary()
  },
  setProvider(app: App) {
    app.config.globalProperties.$dictionary = dictionary
  },
  getProvider(): any {
    return useGlobal().$dictionary
  },
}

export default provider as ProviderService.Provider
