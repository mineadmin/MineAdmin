import type { MaFormItem, MaFormOptions } from '@mineadmin/form'
import type { MaProTableColumns } from '@mineadmin/pro-table'

const dictAll = useDictStore().all()
const dictCategoryList: any[] = []

dictAll.forEach((item: any, key: string) => {
  dictCategoryList.push({
    label: key,
    value: key,
  })
})

export interface FieldAttrs {
  type: string // 字段类型
  comment: string // 注释
  len?: number // 字段长度
  decimal?: number // 字段小数长度
  defaultType?: 'none' | 'nullable' | 'empty' | 'value' // 字段默认值类型
  defaultValue?: any // 字段默认值
  allowNull?: boolean // 是否允许空
  primaryKey?: boolean // 是否为主键
  autoIncrement?: boolean // 是否自增
  isIndex?: boolean // 是否加索引
  isUnique?: boolean // 是否唯一
  unsigned?: boolean // 无符号
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
  componentConfig?: {
    model: Record<string, any>
    items: MaFormItem[]
    options?: MaFormOptions
  }
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
      title: '基础常用组件',
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
        {
          name: 'status',
          title: '状态-开关',
          description: '用于数据表状态字段',
          fieldAttrs: {
            type: 'tinyint',
            len: 4,
            comment: '状态:1=正常,2=禁用',
            defaultType: 'value',
            defaultValue: 1,
            isIndex: true,
          },
          formConfig: {
            render: () => <el-switch />,
            prop: 'status',
            renderProps: {
              trueValue: 1,
              falseValue: 2,
            },
          },
        },
        {
          name: 'created-by',
          title: '创建人',
          description: '用于数据表创建人字段',
          fieldAttrs: {
            type: 'bigint',
            len: 20,
            comment: '创建人',
            isIndex: true,
          },
          formConfig: {
            prop: 'created_by',
          },
        },
        {
          name: 'updated-by',
          title: '更新人',
          description: '用于数据表更新人字段',
          fieldAttrs: {
            type: 'bigint',
            len: 20,
            comment: '更新人',
            isIndex: true,
          },
          formConfig: {
            prop: 'updated_by',
          },
        },
        {
          name: 'created-at',
          title: '创建时间',
          description: '用于数据表创建时间字段',
          fieldAttrs: {
            type: 'datetime',
            comment: '创建时间',
          },
          formConfig: {
            prop: 'created_at',
          },
        },
        {
          name: 'updated-at',
          title: '更新时间',
          description: '用于数据表更新时间字段',
          fieldAttrs: {
            type: 'datetime',
            comment: '更新时间',
          },
          formConfig: {
            prop: 'updated-at',
          },
        },
        {
          name: 'remark',
          title: '备注',
          description: '用于数据表的备注字段',
          fieldAttrs: {
            type: 'string',
            len: 255,
            comment: '备注',
            allowNull: true,
          },
          formConfig: {
            prop: 'remark',
            render: () => <el-input type="textarea" />,
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
            prop: 'icon',
            render: () => <ma-icon-picker />,
          },
          fieldAttrs: {
            type: 'string',
            comment: '图标',
          },
        },
        {
          name: 'ma-upload-image',
          title: '图片上传',
          description: '图片上传',
          formConfig: {
            prop: 'image',
            render: () => <ma-upload-image />,
          },
          fieldAttrs: {
            type: 'string',
            comment: '图片上传',
          },
          componentConfig: {
            model: {
              limit: 5,
              fileSize: 10,
            },
            items: [
              { prop: 'multiple', label: '多上传', render: 'switch' },
              { prop: 'limit', label: '多上传限制', render: () => <el-input-number />, show: (item, model) => model?.multiple },
              { prop: 'fileSize', label: '文件大小', render: () => <el-input />, renderSlots: { suffix: () => 'MB' } },
            ],
          },
        },
        {
          name: 'ma-file-file',
          title: '文件上传',
          description: '文件上传',
          formConfig: {
            prop: 'file',
            render: () => <ma-upload-file />,
          },
          fieldAttrs: {
            type: 'string',
            comment: '文件上传',
          },
          componentConfig: {
            model: {
              limit: 1,
              fileSize: 10,
              fileType: 'doc,xls,ppt,txt,pdf',
            },
            items: [
              { prop: 'multiple', label: '多上传', render: 'switch' },
              { prop: 'limit', label: '多上传限制', render: () => <el-input-number />, show: (item, model) => model?.multiple },
              { prop: 'fileSize', label: '文件大小', render: () => <el-input />, renderSlots: { suffix: () => 'MB' } },
              { prop: 'fileType', label: '文件类型', render: () => <el-input /> },
            ],
          },
        },
        {
          name: 'ma-dict-select',
          title: '字典下拉选择',
          description: '字典下拉选择',
          formConfig: {
            prop: 'dict_select',
            render: () => <ma-dict-select />,
          },
          fieldAttrs: {
            type: 'string',
            comment: '字典下拉选择',
          },
          componentConfig: {
            model: {
              dictName: '',
            },
            items: [
              {
                prop: 'dictName',
                label: '选择字典',
                render: () => <el-selectV2 />,
                renderProps: {
                  options: dictCategoryList,
                },
              },
            ],
          },
        },
        {
          name: 'ma-dict-radio',
          title: '字典单选选择',
          description: '字典单选选择',
          formConfig: {
            prop: 'dict_radio',
            render: () => <ma-dict-radio />,
          },
          fieldAttrs: {
            type: 'string',
            comment: '字典单选选择',
          },
          componentConfig: {
            model: {
              dictName: '',
            },
            items: [
              {
                prop: 'dictName',
                label: '选择字典',
                render: () => <el-selectV2 />,
                renderProps: {
                  options: dictCategoryList,
                },
              },
            ],
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
  }
}
