/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
export default defineComponent({
  name: 'MineIframe',
  setup() {
    const route = useRoute()
    return () => (
      <div class="mine-layout h-full w-full">
        {(route.meta?.type === 'I' && route.meta?.link) && <iframe class="h-full w-full" frameborder="0" src={route.meta?.link}></iframe>}
      </div>
    )
  },
})
