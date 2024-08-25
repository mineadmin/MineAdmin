/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import pkg from '../../package.json'

const copyright_common_style = 'background:#35495E; padding: 1px; border-radius: 3px 0 0 3px; color: #fff;'
const copyright_main_style = `background: #3488ff; padding: 1px; border-radius: 0 3px 3px 0;  color: #fff;`
const copyright_sub_style = `background:transparent`
console.info(`%c MineAdmin %c ${pkg.version} release %c`, copyright_common_style, copyright_main_style, copyright_sub_style, '\nhttps://github.com/mineadmin')
