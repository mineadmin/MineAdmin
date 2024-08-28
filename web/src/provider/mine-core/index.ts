/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { App } from 'vue'
import MaTable from '@mineadmin/table'
import MaForm from '@mineadmin/form'

import type { MaTableColumns, MaTableOptions, PaginationProps } from '@mineadmin/table'
import type { MaFormItem, MaFormOptions } from '@mineadmin/form'
import type { ProviderService } from '#/global'

// maTable样式
import '@mineadmin/table/dist/style.css'

interface MineCoreCommonConfig {
  table?: {
    commonOptions: MaTableOptions
    commonColumns: MaTableColumns[]
    commonPagination: PaginationProps
  }
  form?: {
    commonOptions: MaFormOptions
    commonItems: MaFormItem[]
  }
}

function commonConfig(): MineCoreCommonConfig {
  return {
    table: {
      commonOptions: {},
      commonColumns: [],
      commonPagination: {},
    },
    form: {
      commonOptions: {},
      commonItems: [],
    },
  }
}

const provider: ProviderService.Provider = {
  name: 'mine-core',
  setProvider(app: App) {
    app.use(MaTable)
    app.use(MaForm)
    app.config.globalProperties.$mineCore = commonConfig()
  },
  getProvider(): any {
    return useGlobal().$mineCore
  },
}

export default provider as ProviderService.Provider