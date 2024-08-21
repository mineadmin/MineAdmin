/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
export default function useParentNode(e: PointerEvent | MouseEvent, labelName: string) {
  let node: any = e.target
  while (node.tagName !== labelName.toUpperCase()) {
    node = node?.parentNode
  }
  return node
}
