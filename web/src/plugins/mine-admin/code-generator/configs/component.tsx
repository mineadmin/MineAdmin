import type { MaFormItem } from '@mineadmin/form'
import type { MaProTableColumns } from '@mineadmin/pro-table'

export interface FieldAttrs {
  type: string // 字段类型
  comment: string // 注释
  len?: number // 字段长度
  decimal?: number // 字段小数长度
  defaultValue?: any // 字段默认值
  allowNull?: boolean // 是否允许空
  primaryKey?: boolean // 是否为主键
  autoIncrement?: boolean // 是否自增
  isIndex?: boolean // 是否加索引
  isUnique?: boolean // 是否唯一
  [key: string]: any // 其他配置
}

export interface DesignComponent {
  id?: string
  name: string
  title: string
  description?: string
  fieldAttrs?: FieldAttrs
  formConfig?: MaFormItem
  columnConfig?: MaProTableColumns
}

export interface DesignCategory {
  [key: string]: {
    title: string
    list: DesignComponent[]
  }
}

export function getComponentList(): DesignCategory {
  return {
    base: {
      title: '基础组件',
      list: [
        {
          name: 'primary-key',
          title: '主键',
          description: '用于数据表主键字段',
          fieldAttrs: {
            type: 'bigint',
            len: 20,
            comment: '主键',
            primaryKey: true,
            autoIncrement: true,
          },
          formConfig: {
            prop: 'id',
          },
        },
      ],
    },
    element: {
      title: 'Element Plus 组件',
      list: [
        {
          name: 'input',
          title: '文本输入框',
          description: '用于输入文本信息',
          formConfig: {
            render: () => <el-input />,
          },
          fieldAttrs: {
            type: 'string',
            len: 32,
            comment: '输入框',
          },
        },
        {
          name: 'input-number',
          title: '数字输入框',
          description: '用于输入数字信息',
          formConfig: {
            render: () => <el-input-number />,
          },
          fieldAttrs: {
            type: 'int',
            len: 10,
            decimal: 0,
            comment: '数字输入框',
          },
        },
        {
          name: 'select',
          title: '选择器',
          description: '用于从多个选项中选择一个',
          formConfig: {
            render: () => <el-select />,
          },
          fieldAttrs: {
            type: 'string',
            len: 32,
            comment: '选择器',
          },
        },
        {
          name: 'switch',
          title: '开关',
          description: '用于切换开关状态',
          formConfig: {
            render: () => <el-switch />,
          },
          fieldAttrs: {
            type: 'boolean',
            comment: '开关',
          },
        },
        {
          name: 'slider',
          title: '滑块',
          description: '用于数值选择',
          formConfig: {
            render: () => <el-slider />,
          },
          fieldAttrs: {
            type: 'int',
            comment: '滑块',
          },
        },
        {
          name: 'time-picker',
          title: '时间选择器',
          description: '用于选择时间',
          formConfig: {
            render: () => <el-time-picker />,
          },
          fieldAttrs: {
            type: 'time',
            comment: '时间选择器',
          },
        },
        {
          name: 'date-picker',
          title: '日期选择器',
          description: '用于选择日期',
          formConfig: {
            render: () => <el-date-picker />,
          },
          fieldAttrs: {
            type: 'date',
            comment: '日期选择器',
          },
        },
        {
          name: 'datetime-picker',
          title: '日期时间选择器',
          description: '用于选择日期和时间',
          formConfig: {
            render: () => <el-date-picker type="datetime" />,
          },
          fieldAttrs: {
            type: 'datetime',
            comment: '日期时间选择器',
          },
        },
        {
          name: 'rate',
          title: '评分',
          description: '用于评分操作',
          formConfig: {
            render: () => <el-rate />,
          },
          fieldAttrs: {
            type: 'int',
            comment: '评分',
          },
        },
        {
          name: 'color-picker',
          title: '颜色选择器',
          description: '用于选择颜色',
          formConfig: {
            render: () => <el-color-picker />,
          },
          fieldAttrs: {
            type: 'string',
            len: 7,
            comment: '颜色选择器',
          },
        },
        {
          name: 'transfer',
          title: '穿梭框',
          description: '用于数据迁移',
          formConfig: {
            render: () => <el-transfer />,
          },
          fieldAttrs: {
            type: 'json',
            comment: '穿梭框',
          },
        },
        {
          name: 'cascader',
          title: '级联选择器',
          description: '用于多级数据选择',
          formConfig: {
            render: () => <el-cascader />,
          },
          fieldAttrs: {
            type: 'json',
            comment: '级联选择器',
          },
        },
        {
          name: 'tree-select',
          title: '树形选择器',
          description: '用于树形结构数据选择',
          formConfig: {
            render: () => <el-tree-select />,
          },
          fieldAttrs: {
            type: 'json',
            comment: '树形选择器',
          },
        },
      ],
    },
    mineadmin: {
      title: 'MineAdmin 封装组件',
      list: [
        {
          name: 'ma-city-select',
          title: '省市区联动',
          description: '用于省市区选择功能',
          formConfig: {
            prop: 'city',
            render: () => <ma-city-select />,
          },
          fieldAttrs: {
            type: 'json',
            comment: '省市区',
            defaultValue: {},
          },
        },
        {
          name: 'ma-icon-picker',
          title: '图标选择器',
          description: '选择系统图标',
          formConfig: {
            prop: 'city',
            render: () => <ma-icon-picker />,
          },
          fieldAttrs: {
            type: 'string',
            comment: '图标',
          },
        },
      ],
    },
  }
}
