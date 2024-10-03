/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import * as echarts from 'echarts/core'
import { BarChart, LineChart, PieChart, RadarChart } from 'echarts/charts'
import { CanvasRenderer, SVGRenderer } from 'echarts/renderers'
import {
  DataZoomComponent,
  GraphicComponent,
  GridComponent,
  LegendComponent,
  PolarComponent,
  TitleComponent,
  ToolboxComponent,
  TooltipComponent,
  VisualMapComponent,
} from 'echarts/components'
import type { App } from 'vue'
import mineDarkJson from './themes/mineDark.project.json'
import type { ProviderService } from '#/global'

const { use } = echarts

use([
  PieChart,
  BarChart,
  LineChart,
  RadarChart,
  CanvasRenderer,
  SVGRenderer,
  GridComponent,
  TitleComponent,
  PolarComponent,
  LegendComponent,
  GraphicComponent,
  ToolboxComponent,
  TooltipComponent,
  DataZoomComponent,
  VisualMapComponent,
])

const provider: ProviderService.Provider = {
  name: 'echarts',
  setProvider(app: App): void {
    echarts.registerTheme('mineDark', mineDarkJson.theme as any)
    app.config.globalProperties.$echarts = echarts
  },
  getProvider() {
    return useGlobal().$echarts
  },
}

export default provider as ProviderService.Provider
