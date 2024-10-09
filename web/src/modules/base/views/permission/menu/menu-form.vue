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

const menuList = inject('menuList') as Ref<MenuVo[]>
const menuForm = ref<MaFormExpose>()
const treeSelectRef = ref()
const form = ref<Record<string, any>>({
  meta: {
    type: 'M',
    componentSuffix: '.vue',
    componentPath: 'modules/',
    breadcrumbEnable: true,
    copyright: true,
    hidden: false,
    affix: false,
    cache: true,
  },
  component: '',
  sort: 0,
  status: 1,
  btnPermission: [],
})

function setData(data: Record<string, any>) {
  Object.keys(data).map((name: string) => {
    if (name === 'parent_id' && data[name] === 0) {
      form.value[name] = undefined
    }
    else if (name === 'children' && data[name]?.length > 0) {
      form.value.btnPermission = []
      data[name].map((item: any) => {
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
}

const formOptions = ref<MaFormOptions>({
  labelWidth: '85px',
})

function filterNode(_: string, data: Record<string, any>) {
  return data.meta?.type === 'M'
}

const formItems = ref<MaFormItem[]>([
  {
    label: '菜单名称', prop: 'meta.title', render: 'input',
    renderProps: {
      placeholder: '请输入菜单名称',
    },
    itemProps: {
      rules: [{ required: true, message: '请输入菜单名称' }],
    },
    cols: { lg: 12, md: 24 },
  },
  {
    label: '菜单标识', prop: 'name', render: 'input',
    renderProps: {
      placeholder: '请输入菜单标识，此项全局唯一',
    },
    itemProps: {
      rules: [{ required: true, message: '请输入菜单标识' }],
    },
    cols: { lg: 12, md: 24 },
  },
  {
    label: '上级菜单', prop: 'parent_id', render: () => (
      <el-tree-select
        ref={treeSelectRef}
        data={menuList.value}
        props={{ value: 'id', label: 'title' }}
        check-strictly={true}
        default-expand-all={true}
        clearable={true}
        filter-node-method={filterNode}
      >
        {{
          default: ({ node }) => {
            const { meta } = node.data
            node.data.title = meta?.i18n ? useTrans(meta.i18n) : meta?.title ?? 'unknown'
          },
        }}
      </el-tree-select>
    ),
    renderProps: {
      class: 'w-full',
      placeholder: '请选择上级菜单，不选为顶级菜单',
    },
    cols: { lg: 12, md: 24 },
  },
  {
    label: '菜单类型', prop: 'meta.type', render: () => (
      <el-radio-group>
        <el-radio-button label="菜单" value="M"></el-radio-button>
        <el-radio-button label="按钮" value="B"></el-radio-button>
        <el-radio-button label="外链" value="L"></el-radio-button>
        <el-radio-button label="iFrame" value="I"></el-radio-button>
      </el-radio-group>
    ),
    cols: { lg: 12, md: 24 },
  },
  {
    label: '菜单图标', prop: 'meta.icon', render: () => MaIconPicker,
    hide: (_, model) => model.meta.type === 'B',
    renderProps: {
      class: 'w-full',
    },
  },
  {
    label: '路由地址', prop: 'path', render: 'input',
    show: (_, model) => model.meta.type === 'M',
    itemProps: {
      rules: [{ required: true, message: '请输入路由地址' }],
    },
    renderProps: {
      placeholder: '页面访问地址，以 "/" 开头',
    },
  },
  {
    label: '视图地址', prop: 'component', render: 'input',
    show: (_, model) => model.meta.type === 'M',
    renderProps: {
      class: 'w-full',
      placeholder: '视图地址，请指向模块或插件下的views目录文件',
    },
    renderSlots: {
      prepend: () => (
        <ElSelect v-model={form.value.meta.componentPath} placeholder="视图前缀地址" class="w-220px">
          <ElOption label="模块目录：src/modules/" value="modules/" />
          <ElOption label="插件目录：src/plugins/" value="plugins/" />
        </ElSelect>
      ),
      append: () => (
        <ElSelect v-model={form.value.meta.componentSuffix} placeholder="视图后缀" class="w-120px">
          <ElOption label=".vue" value=".vue" />
          <ElOption label=".jsx" value=".jsx" />
          <ElOption label=".tsx" value=".tsx" />
        </ElSelect>
      ),
    },
  },
  {
    label: '路由重定向', prop: 'redirect', render: 'input',
    show: (_, model) => model.meta.type === 'M',
    renderProps: {
      placeholder: '利用 vue-router 跳转其他路由地址，以 "/" 开头',
    },
  },
  {
    label: '外链/内嵌', prop: 'meta.link', render: 'input',
    show: (_, model) => ['L', 'I'].includes(model.meta.type),
    renderProps: {
      placeholder: '第三方地址URL',
    },
    itemProps: {
      rules: [{ required: true, message: '请输入第三方URL' }],
    },
  },
  {
    label: '国际化', prop: 'meta.i18n', render: 'input',
    renderProps: {
      placeholder: '菜单标题国际化',
    },
    cols: { lg: 12, md: 12, sm: 24 },
  },
  {
    label: '排序', prop: 'sort', render: 'inputNumber',
    renderProps: {
      min: 0, max: 99999,
      class: 'w-full',
    },
    cols: { lg: 12, md: 12, sm: 24 },
  },
  {
    label: '是否启用', prop: 'status', render: 'switch',
    renderProps: {
      activeValue: 1,
      inactiveValue: 2,
    },
    cols: { lg: 8, md: 8, sm: 8, xs: 12 },
  },
  {
    label: '是否隐藏', prop: 'meta.hidden', render: 'switch',
    show: (_, model) => model.meta.type === 'M',
    cols: { lg: 8, md: 8, sm: 8, xs: 12 },
  },
  {
    label: '是否缓存', prop: 'meta.cache', render: 'switch',
    show: (_, model) => model.meta.type === 'M',
    cols: { lg: 8, md: 8, sm: 8, xs: 12 },
  },
  {
    label: '版权显示', prop: 'meta.copyright', render: 'switch',
    show: (_, model) => model.meta.type === 'M',
    cols: { lg: 8, md: 8, sm: 8, xs: 12 },
  },
  {
    label: '面包屑显示', prop: 'meta.breadcrumbEnable', render: 'switch',
    show: (_, model) => model.meta.type === 'M',
    cols: { lg: 8, md: 8, sm: 8, xs: 12 },
  },
  {
    label: 'Tab页固定', prop: 'meta.affix', render: 'switch',
    show: (_, model) => model.meta.type === 'M',
    cols: { lg: 8, md: 8, sm: 8, xs: 12 },
  },
  {
    label: '备注', prop: 'remark', render: 'input',
    renderProps: {
      type: 'textarea', placeholder: '备注信息', maxlength: '255', showWordLimit: true,
    },
  },
  {
    label: '按钮权限',
    prop: 'btnPermission',
    show: (_, model) => model.meta.type === 'M',
    render: (_, model: Record<string, any>) => <ButtonPermission model={model} />,
  },
])

watch(
  () => menuList.value,
  val => treeSelectRef.value.filter(val),
  { deep: true },
)

defineExpose({
  setData,
  menuForm,
  model: form.value,
})
</script>

<template>
  <ma-form
    ref="menuForm"
    v-model="form"
    class="mt-5"
    :options="formOptions"
    :items="formItems"
  />
</template>

<style scoped lang="scss">

</style>
