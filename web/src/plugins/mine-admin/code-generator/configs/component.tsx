import type { MaFormItem } from '@mineadmin/form'
import type { MaProTableColumns } from '@mineadmin/pro-table'

export interface FieldAttrs {
  type: string // 字段类型
  comment: string // 注释
  len?: number // 字段长度
  decimal?: number // 字段小数长度
  defaultValue?: string // 字段默认值
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
  initHandle?: () => void
}

export interface DesignCategory {
  [key: string]: {
    title: string
    list: DesignComponent[]
  }
}

export function getComponentList(model: Record<string, any>): DesignCategory {
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
            render: () => <el-input />,
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
      ],
    },
    mineadmin: {
      title: 'MineAdmin 封装组件',
      list: [
        {
          name: 'ma-city-select',
          title: '省市区联动',
          description: '用于省市区选择功能',
          initHandle: (key = 'city') => {
            model[key] = {}
          },
          formConfig: {
            prop: 'city',
            render: () => <ma-city-select />,
          },
          fieldAttrs: {
            type: 'json',
            comment: '省市区',
          },
        },
      ],
    },
  }
}
