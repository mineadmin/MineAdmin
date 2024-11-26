/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import '@/layouts/style/footer.scss'

export default defineComponent({
  name: 'Footer',
  setup() {
    const settingStore = useSettingStore()
    const footerSetting = settingStore.getSettings('copyright')
    const route = useRoute()
    return () => (
      <footer v-show={route.meta?.type !== 'I'}>
        {
          ((footerSetting.enable && route.meta?.copyright === true) && route.meta?.type !== 'I')
          && (
            <div class="mine-footer">
              <span>Copyright</span>
              <ma-svg-icon name="lucide:copyright" />
              <span>{footerSetting.dates}</span>
              <span><a href={footerSetting.website} target="_blank">{footerSetting.company}</a></span>
              <span><a href="https://beian.miit.gov.cn/" target="_blank">{footerSetting.putOnRecord}</a></span>
            </div>
          )
        }
      </footer>
    )
  },
})
