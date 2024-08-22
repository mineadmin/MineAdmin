/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import boxen from 'boxen'
import picocolors from 'picocolors'
import pkg from '../package.json'

export default function startInfo(): any {
  return {
    name: 'startInfo',
    apply: 'serve',
    async buildStart() {
      const { bold, cyan, underline } = picocolors

      console.log(
        boxen(
          `${bold(cyan(`MineAdmin-UI Engine v${pkg.version}`))}\n\n${underline('https://github.com/mineadmin/ui')}`,
          {
            padding: 1,
            margin: 1,
            borderStyle: 'double',
            title: 'Welcome use',
            titleAlignment: 'center',
            textAlignment: 'center',
          },
        ),
      )
    },
  }
}