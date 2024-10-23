/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */

export default function discount(discount: string | number, price: number): string {
  return (price * ((discount === '0.00' || discount === 0) ? 10 : Number(discount)) / 10).toFixed(2)
}
