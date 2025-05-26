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
import type { UserVo } from '~/base/api/user.ts'
import MaUploadImage from '@/components/ma-upload-image/index.vue'
import MaDictRadio from '@/components/ma-dict-picker/ma-dict-radio.vue'
import type { UseDialogExpose } from '@/hooks/useDialog.ts'

export default function getFormItems(
  formType: 'add' | 'edit' = 'add',
  t: any,
  model: UserVo,
  deptData: any,
  dialog: UseDialogExpose,
  scopeRef: any,
): MaFormItem[] {
  if (formType === 'add') {
    model.password = '123456'
    model.status = 1
    model.user_type = 100
    model.department = []
    model.position = []
  }

  const departmentList = deptData.value.filter((_, index) => index > 0)
  const deptIds = ref<number[]>([])
  const postList = ref<any[]>([])

  model.backend_setting = []

  if (formType === 'edit') {
    const findNode = (nodes: any[], id: number) => {
      for (let i = 0; i < nodes.length; i++) {
        if (nodes[i].id === id) {
          return nodes[i]
        }
        if (nodes[i].children) {
          const node = findNode(nodes[i].children, id)
          if (node) {
            return node
          }
        }
      }
      return null
    }

    model.department = model.department?.map((item: any) => {
      // 添加
      deptIds.value.push(item.id)
      const post = JSON.parse(JSON.stringify(findNode(departmentList, item.id) ?? null))
      if (post) {
        post.disabled = true
        postList.value.push(post)
      }
      return item.id
    })
    model.position = model.position?.map((item: any) => item.id)
  }

  return [
    {
      label: () => t('baseUserManage.avatar'),
      prop: 'avatar',
      render: () => MaUploadImage,
    },
    {
      label: () => t('baseUserManage.username'),
      prop: 'username',
      render: 'input',
      cols: { md: 12, xs: 24 },
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: t('baseUserManage.username') }),
      },
      itemProps: {
        rules: [{ required: true, message: t('form.requiredInput', { msg: t('baseUserManage.username') }) }],
      },
    },
    {
      label: () => t('baseUserManage.nickname'),
      prop: 'nickname',
      render: 'input',
      cols: { md: 12, xs: 24 },
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: t('baseUserManage.nickname') }),
      },
      itemProps: {
        rules: [{ required: true, message: t('form.requiredInput', { msg: t('baseUserManage.nickname') }) }],
      },
    },
    {
      label: () => t('baseUserManage.password'),
      prop: 'password',
      render: 'input',
      cols: { md: 12, xs: 24 },
      renderProps: {
        disabled: formType === 'edit',
        placeholder: t('form.pleaseInput', { msg: t('baseUserManage.password') }),
      },
      itemProps: {
        rules: formType === 'add' ? [{ required: true, message: t('form.requiredInput', { msg: t('baseUserManage.password') }) }] : [],
      },
    },
    {
      label: () => t('baseUserManage.phone'),
      prop: 'phone',
      render: 'input',
      cols: { md: 12, xs: 24 },
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: t('baseUserManage.phone') }),
      },
    },
    {
      label: () => t('baseUserManage.dept'),
      prop: 'department',
      render: () => <el-tree-select />,
      renderProps: {
        data: departmentList,
        multiple: true,
        filterable: true,
        clearable: true,
        props: { label: 'name' },
        checkStrictly: true,
        nodeKey: 'id',
        placeholder: t('form.pleaseInput', { msg: t('baseUserManage.dept') }),
        onNodeClick: (row: any) => {
          if (deptIds.value.includes(row.id)) {
            // 移除
            deptIds.value = deptIds.value.filter((id: number) => id !== row.id)
            postList.value = postList.value.filter((item: any) => item.id !== row.id)
          }
          else {
            // 添加
            deptIds.value.push(row.id)
            const post = JSON.parse(JSON.stringify(row))
            post.disabled = true
            postList.value.push(post)
          }
        },
        onRemoveTag: (value: number) => {
          const current = postList.value.find((item: any) => item.id === value)?.positions ?? []
          if (current.length > 0) {
            current?.map((item: any) => {
              if (model.position?.includes(item.id)) {
                model.position?.splice(model.position?.indexOf(item.id), 1)
              }
            })
          }
          postList.value = postList.value.filter((item: any) => item.id !== value)
        },
        onClear: () => {
          postList.value = []
          model.position = []
        },
      },
    },
    {
      label: () => t('baseUserManage.post'),
      prop: 'position',
      render: () => <el-tree-select />,
      cols: { md: 12, xs: 24 },
      renderProps: {
        data: postList,
        defaultExpandAll: true,
        multiple: true,
        filterable: true,
        clearable: true,
        props: { label: 'name', children: 'positions' },
        checkStrictly: true,
        nodeKey: 'id',
        placeholder: t('form.pleaseInput', { msg: t('baseUserManage.post') }),
      },
    },
    {
      render: () => <el-button />,
      cols: { md: 12, xs: 24 },
      renderProps: {
        type: 'primary',
        plain: true,
        onClick: async () => {
          dialog.setTitle(t('baseUserManage.setDataScope'))
          dialog.open()
          if (formType === 'add') {
            model.policy = {
              value: [],
              name: model.username,
              policy_type: '',
            }
          }
          if (formType === 'edit') {
            if (model.policy) {
              model.policy.name = model.username
              if (model.policy.policy_type === 'CUSTOM_FUNC') {
                model.policy.func_name = model.policy.value
              }
              if (model.policy.policy_type === 'CUSTOM_DEPT') {
                await nextTick(() => {
                  scopeRef.value.deptRef.elTree?.setCheckedKeys(model.policy.value, true)
                })
              }
            }
            else {
              model.policy = { name: model.username }
            }
          }
        },
      },
      itemSlots: {
        label: () => (
          <div class="flex items-center gap-x-1">
            {t('baseUserManage.dataScope')}
            <el-tooltip content="设置后将覆盖岗位的数据权限" placement="top">
              <ma-svg-icon name="material-symbols:help-outline" />
            </el-tooltip>
          </div>
        ),
      },
      renderSlots: {
        default: () => t('baseUserManage.setDataScope'),
      },
    },
    {
      label: () => t('baseUserManage.email'),
      prop: 'email',
      render: 'input',
      cols: { md: 12, xs: 24 },
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: t('baseUserManage.email') }),
      },
    },
    {
      label: () => t('baseUserManage.userType'),
      prop: 'user_type',
      cols: { md: 12, xs: 24 },
      render: () => MaDictRadio,
      renderProps: {
        renderMode: 'button',
        placeholder: t('form.pleaseInput', { msg: t('baseUserManage.userType') }),
        dictName: 'base-userType',
      },
    },
    {
      label: () => t('crud.remark'),
      prop: 'remark',
      render: 'input',
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: t('crud.remark') }),
        type: 'textarea',
      },
    },
    {
      label: () => t('crud.status'),
      prop: 'status',
      render: () => MaDictRadio,
      renderProps: {
        placeholder: t('form.pleaseInput', { msg: t('crud.status') }),
        dictName: 'system-status',
      },
    },
  ]
}
