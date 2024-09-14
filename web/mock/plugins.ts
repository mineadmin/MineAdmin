import { defineFakeRoute } from 'vite-plugin-fake-server/client'

// 加载插件mock
const pluginMocks = import.meta.glob('$/**/mock/*')
const defaultExports = []
for (const key in pluginMocks) {
  const module = await pluginMocks[key]()
  defaultExports.push(module.default)
}
export default defineFakeRoute(defaultExports)
