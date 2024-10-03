/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */

import process from 'node:process'
import fs from 'node:fs'
import picocolors from 'picocolors'

const pluginName = process.argv.slice(2)[0]

function copyFile(source: string, destination: string, callback: (err?: Error) => void) {
  if (fs.existsSync(source)) {
    fs.readFile(source, (err, data) => {
      if (err) {
        callback(err)
      }
      else {
        fs.writeFile(destination, data, (err) => {
          callback(err)
        })
      }
    })
  }
  else {
    callback(`${pluginName} 插件没有配置文件可发布，已跳过`)
  }
}

if (pluginName === undefined) {
  console.error('发布插件配置文件缺少指定插件参数，例：pnpm plugin:publish mine-admin/ui')
}
else {
  const source = `src/plugins/${pluginName}/config.ts`
  const target = `src/provider/plugins/config/${pluginName.replace('/', '.')}.config.ts`
  copyFile(source, target, (err) => {
    if (err) {
      console.error(err)
    }
    else {
      console.log(picocolors.green(`插件 ${pluginName} 配置文件发布成功`))
    }
  })
}
