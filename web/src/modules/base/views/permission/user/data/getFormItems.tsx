/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { MaFormItem } from '@mineadmin/form'
import MaUploadImage from '@/components/ma-upload-image/index.vue'
import MaUploadFile from '@/components/ma-upload-file/index.vue'

export default function getFormItems(formType: 'add' | 'edit' = 'add', t: any): MaFormItem[] {
  if (formType === 'add') {
    // todo...
  }

  return [
    {
      label: () => t('baseUser.avatar'),
      prop: 'avatar',
      render: () => MaUploadImage,
    },
    {
      label: () => t('baseUser.username'),
      prop: 'username',
      render: () => MaUploadFile,
    },
  ]
}
