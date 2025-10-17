import type { Component } from 'vue'

interface CodeGeneratorComponent {
  name: string
  title: string
  description?: string
  component: string | Component
}

interface CodeGeneratorComponentList {
  [key: string]: {
    title: string
    list: CodeGeneratorComponent[]
  }
}

export type {
  CodeGeneratorComponent,
  CodeGeneratorComponentList,
}

export const componentList: CodeGeneratorComponentList = {
  element: {
    title: 'Element Plus 组件',
    list: [
      {
        name: 'input',
        component: '<el-input />',
        title: '文本输入框',
        description: '用于输入文本信息',
      },
      {
        name: 'input-number',
        component: '<el-input-number />',
        title: '数字输入框',
        description: '用于输入数字信息',
      },
    ],
  },
  mineadmin: {
    title: 'MineAdmin 封装组件',
    list: [
      {
        name: 'ma-auth',
        component: '<ma-auth />',
        title: '权限组件',
        description: '用于控制内容是否显示',
      },
      {
        name: 'ma-city-select',
        component: '<ma-city-select />',
        title: '省市区联动',
        description: '用于省市区选择功能',
      },
    ],
  },
}
