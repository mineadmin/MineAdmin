<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<script setup lang="tsx">
import type { MaFormExpose, MaFormItem, MaFormOptions } from '@mineadmin/form'
import type { Ref } from 'vue'
import { ElOption, ElSelect } from 'element-plus'
import MaIconPicker from '@/components/ma-icon-picker/index.vue'
import type { MenuVo } from '~/base/api/menu.ts'
import ButtonPermission from '~/base/views/permission/menu/button-permission.vue'
import { useI18n } from 'vue-i18n'
import { cloneDeep } from 'lodash-es'

const { locale } = useI18n()
const t = useTrans().globalTrans
const state = ref<boolean>(true)
const menuList = inject('menuList') as Ref<MenuVo[]>
const newMenu = inject('newMenu') as Ref<MenuVo>
const menuForm = ref<MaFormExpose>()
const btnPermissionRef = ref()
const treeSelectRef = ref()
const form = ref<Record<string, any>>({
  dataType: 'add',
  ...newMenu.value,
})

function setData(data: Record<string, any>) {
  form.value.btnPermission = []
  Object.keys(data).map((name: string) => {
    if (name === 'parent_id' && data[name] === 0) {
      form.value[name] = undefined
    }
    else if (name === 'children' && data[name]?.length > 0) {
      form.value.btnPermission = []
      data[name].filter((v: any) => v.meta?.type === 'B').map((item: any) => {
        form.value.btnPermission.push({
          id: item?.id ?? undefined,
          code: item.name,
          title: item.meta?.title ?? '',
          i18n: item.meta?.i18n ?? '',
          type: item.meta?.type ?? '',
        })
      })
    }
    else {
      form.value[name] = data[name]
    }
  })

  if (data.id) {
    form.value.dataType = 'edit'
  }
}

const inputVisible = ref <Record<string, boolean>>({
  auth: false,
  role: false,
  user: false,
})

function addTagRender(key: string, type: 'primary' | 'success' | 'info' | 'warning' | 'danger') {
  return form.value.meta?.[key].map((item: string) => (
    <>
      <el-tag
        closable={true}
        type={type}
        disable-transitions={false}
        onClose={() => {
          form.value.meta[key] = form.value.meta?.[key].filter((name: string) => name !== item)
        }}
      >
        {item}
      </el-tag>
      {inputVisible.value[key] && (
        <el-input />
      )}
    </>
  ))
}

const formOptions = ref<MaFormOptions>({
  labelWidth: '110px',
})

function filterNode(_: string, data: Record<string, any>) {
  return data.meta?.type === 'M'
}

const formItems = ref<MaFormItem[]>([
  {
    label: () => t('baseMenuManage.name'), prop: 'meta.title', render: 'input',
    renderProps: {
      placeholder: 'baseMenuManage.placeholder.name',
    },
    itemProps: {
      rules: [{ required: true, message: 'baseMenuManage.placeholder.name' }],
    },
    cols: { lg: 12, md: 24 },
  },
  {
    label: () => t('baseMenuManage.code'), prop: 'name', render: 'input',
    renderProps: {
      placeholder: 'baseMenuManage.placeholder.code',
    },
    itemProps: {
      rules: [{ required: true, message: 'baseMenuManage.placeholder.code' }],
    },
    cols: { lg: 12, md: 24 },
  },
  {
    label: () => t('baseMenuManage.parentMenu'), prop: 'parent_id', render: () => (
      <el-tree-select
        ref={treeSelectRef}
        data={menuList.value}
        props={{
          value: 'id',
          label: (data: MenuVo) => data.meta?.i18n ? t(data.meta.i18n) : data.meta?.title ?? 'unknown',
        }}
        check-strictly={true}
        default-expand-all={true}
        clearable={true}
        filter-node-method={filterNode}
      />
    ),
    renderProps: {
      class: 'w-full',
      placeholder: 'baseMenuManage.placeholder.parentMenu',
    },
    cols: { lg: 12, md: 24 },
  },
  {
    label: () => t('baseMenuManage.type'), prop: 'meta.type', render: () => (
      <el-radio-group>
        <el-radio-button label={t('baseMenuManage.typeItem.M')} value="M"></el-radio-button>
        <el-radio-button label={t('baseMenuManage.typeItem.B')} value="B"></el-radio-button>
        <el-radio-button label={t('baseMenuManage.typeItem.L')} value="L"></el-radio-button>
        <el-radio-button label={t('baseMenuManage.typeItem.I')} value="I"></el-radio-button>
      </el-radio-group>
    ),
    cols: { lg: 12, md: 24 },
  },
  {
    label: () => t('baseMenuManage.route'), prop: 'path', render: 'input',
    show: (_, model) => model.meta.type === 'M',
    itemProps: {
      rules: [{ required: true, message: 'baseMenuManage.placeholder.route' }],
    },
    renderProps: {
      placeholder: 'baseMenuManage.placeholder.route',
    },
    cols: { lg: 12, md: 24 },
  },
  {
    label: () => t('baseMenuManage.link'), prop: 'meta.link', render: 'input',
    show: (_, model) => ['L', 'I'].includes(model.meta.type),
    cols: { lg: 12, md: 24 },
    renderProps: {
      placeholder: 'baseMenuManage.placeholder.link',
    },
    itemProps: {
      rules: [{ required: true, message: 'baseMenuManage.placeholder.link' }],
    },
  },
  {
    label: () => t('baseMenuManage.activeName'), prop: 'meta.activeName', render: 'input',
    renderProps: {
      class: 'w-full',
      placeholder: 'baseMenuManage.placeholder.activeName',
    },
    cols: { lg: 12, md: 24 },
  },
  {
    label: () => t('baseMenuManage.view'), prop: 'component', render: 'input',
    show: (_, model) => model.meta.type === 'M',
    renderProps: {
      class: 'w-full',
      placeholder: 'baseMenuManage.placeholder.view',
    },
    renderSlots: {
      prepend: () => (
        <ElSelect v-model={form.value.meta.componentPath} class="w-150px">
          <ElOption label="src/modules/" value="modules/" />
          <ElOption label="src/plugins/" value="plugins/" />
        </ElSelect>
      ),
      append: () => (
        <ElSelect v-model={form.value.meta.componentSuffix} class="w-120px">
          <ElOption label=".vue" value=".vue" />
          <ElOption label=".jsx" value=".jsx" />
          <ElOption label=".tsx" value=".tsx" />
        </ElSelect>
      ),
    },
  },
  {
    label: () => t('baseMenuManage.icon'), prop: 'meta.icon', render: () => MaIconPicker,
    show: (_, model) => model.meta.type !== 'B',
    renderProps: {
      class: 'w-full',
    },
    cols: { lg: 12, md: 24 },
  },
  {
    label: () => t('baseMenuManage.redirect'), prop: 'redirect', render: 'input',
    show: (_, model) => model.meta.type === 'M',
    cols: { lg: 12, md: 24 },
    renderProps: {
      placeholder: 'baseMenuManage.placeholder.redirect',
    },
  },
  {
    label: () => t('baseMenuManage.i18n'), prop: 'meta.i18n', render: 'input',
    renderProps: {
      placeholder: 'baseMenuManage.placeholder.i18n',
    },
    cols: { lg: 12, md: 12, sm: 24 },
  },
  {
    label: () => t('baseMenuManage.sort'), prop: 'sort', render: 'inputNumber',
    renderProps: {
      min: 0, max: 99999,
      class: 'w-full',
    },
    cols: { lg: 12, md: 12, sm: 24 },
  },
  {
    label: () => t('baseMenuManage.isEnabled'), prop: 'status', render: 'switch',
    renderProps: {
      activeValue: 1,
      inactiveValue: 2,
    },
    cols: { lg: 8, md: 8, sm: 8, xs: 12 },
  },
  {
    label: () => t('baseMenuManage.isHidden'), prop: 'meta.hidden', render: 'switch',
    show: (_, model) => model.meta.type === 'M',
    cols: { lg: 8, md: 8, sm: 8, xs: 12 },
  },
  {
    label: () => t('baseMenuManage.isCache'), prop: 'meta.cache', render: 'switch',
    show: (_, model) => model.meta.type === 'M',
    cols: { lg: 8, md: 8, sm: 8, xs: 12 },
  },
  {
    label: () => t('baseMenuManage.isCopyright'), prop: 'meta.copyright', render: 'switch',
    show: (_, model) => model.meta.type === 'M',
    cols: { lg: 8, md: 8, sm: 8, xs: 12 },
  },
  {
    label: () => t('baseMenuManage.isBreadcrumb'), prop: 'meta.breadcrumbEnable', render: 'switch',
    show: (_, model) => model.meta.type === 'M',
    cols: { lg: 8, md: 8, sm: 8, xs: 12 },
  },
  {
    label: () => t('baseMenuManage.isAffix'), prop: 'meta.affix', render: 'switch',
    show: (_, model) => model.meta.type === 'M',
    cols: { lg: 8, md: 8, sm: 8, xs: 12 },
  },
  {
    label: () => t('baseMenuManage.remark'), prop: 'remark', render: 'input',
    renderProps: {
      type: 'textarea', placeholder: 'baseMenuManage.placeholder.remark', maxlength: '255', showWordLimit: true,
    },
  },
  {
    label: () => t('baseMenuManage.BtnPermission.label'),
    prop: 'btnPermission',
    show: (_, model) => model.meta.type === 'M',
    render: () => <ButtonPermission model={form.value.btnPermission} />,
    renderProps: {
      ref: (el: any) => {
        btnPermissionRef.value = el
        el?.setBtnData?.(form.value.btnPermission)
      },
      onAddBtn: (btn: MenuVo) => {
        form.value.btnPermission.push(btn)
        btnPermissionRef.value?.setBtnData?.(form.value.btnPermission)
      },
    },
  },
])

function setInfo() {
  formItems.value.map((item) => {
    const formItem = menuForm.value?.getItemByProp(item.prop as string)
    if (formItem?.renderProps?.placeholder && item.renderProps?.placeholder) {
      formItem.renderProps.placeholder = t(`${item.renderProps?.placeholder}`)
    }
    if (formItem?.itemProps?.rules && item?.itemProps?.rules) {
      formItem.itemProps.rules[0].message = t(`${item?.itemProps?.rules[0].message}`)
    }
  })
}

watch(
  () => menuList.value,
  val => treeSelectRef.value.filter(val),
  { deep: true },
)

watch(
  locale,
  () => setInfo(),
  { immediate: true },
)

onMounted(() => {
  menuForm.value?.setItems(cloneDeep(formItems.value))
  setInfo()
})

defineExpose({
  setData,
  menuForm,
  model: form.value,
})
</script>

<template>
  <ma-form
    v-if="state"
    ref="menuForm"
    v-model="form"
    class="mt-5"
    :options="formOptions"
  />
</template>

<style scoped lang="scss">

</style>
