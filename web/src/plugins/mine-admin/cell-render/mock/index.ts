import { defineFakeRoute } from 'vite-plugin-fake-server/client'

export default defineFakeRoute([
  {
    url: '/mock/switch/changeStatus',
    method: 'post',
    response: ({ body }) => {
      return {
        success: true,
        message: '请求成功',
        code: 200,
        data: [],
      }
    },
  },
])
