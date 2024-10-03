import { defineFakeRoute } from 'vite-plugin-fake-server/client'
import Mock from 'mockjs'
import userInfoData from './data/userinfo'

export default defineFakeRoute([
  {
    url: '/mock/system/login',
    method: 'post',
    response: ({ body }) => {
      return {
        success: true,
        message: '请求成功',
        code: 200,
        data: Mock.mock({
          token: `${body.username}_@string`,
          expire: 7200,
          refreshToken: `${body.username}_@RefreshString`,
        }),
      }
    },
  },
  {
    url: '/mock/system/getInfo',
    method: 'get',
    response: () => {
      return {
        success: true,
        message: '请求成功',
        code: 200,
        data: userInfoData,
      }
    },
  },
  {
    url: '/mock/system/saveSetting',
    method: 'post',
    response: () => {
      return {
        success: true,
        message: '请求成功',
        code: 200,
        data: {},
      }
    },
  }, {
    url: '/mock/system/clearCache',
    method: 'post',
    response: () => {
      return {
        success: true,
        message: '请求成功',
        code: 200,
        data: {},
      }
    },
  },
])
