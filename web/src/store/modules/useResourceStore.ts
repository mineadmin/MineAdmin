/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { Resources } from '#/global'

const resourceDefaultButtons: Resources.Button[] = [
  {
    name: 'local-image-upload',
    label: '图片上传',
    icon: 'solar:upload-square-broken',
    click: (btn: Resources.Button, selected: any) => {
      console.log(btn, selected)
    },
    upload: () => {

    },
    order: 0,
  },
  {
    name: 'local-file-upload',
    label: '文件上传',
    icon: 'hugeicons:file-upload',
    click: (args: any) => {

    },
    order: 1,
  },
  {
    name: 'local-file-upload2',
    label: '文件上传',
    icon: 'hugeicons:file-upload',
    click: (args: any) => {

    },
    order: 1,
  },
]

const useResourceStore = defineStore(
  'useResourceStore',
  () => {
    const resourceButtons = ref<Resources.Button[]>([])

    const getButton = (name: string): Resources.Button | undefined => {
      return resourceButtons.value.find(item => item.name === name)
    }

    const addButton = (button: Resources.Button): boolean => {
      if (getButton(button.name)) {
        return false
      }
      else {
        resourceButtons.value.push(button)
        return true
      }
    }

    const removeButton = (name: string) => {
      resourceButtons.value = resourceButtons.value.filter(item => item.name !== name)
    }

    const getAllButton = () => {
      return resourceButtons.value
    }

    resourceDefaultButtons.forEach(item => addButton(item))

    return {
      addButton,
      removeButton,
      getButton,
      getAllButton,
    }
  },
)

export default useResourceStore
