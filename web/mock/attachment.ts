import { defineFakeRoute } from 'vite-plugin-fake-server/client'
import { attachments } from './data/attachment.ts'

export default defineFakeRoute([
  {
    url: '/mock/attachment/list',
    method: 'get',
    response: ({ query }) => {
      console.log(1111)
      const page = Number(query.page) || 1
      const pageSize = Number(query.pageSize) || 15
      const start = (page - 1) * pageSize
      const end = page * pageSize

      // @ts-expect-error
      const originName: string | null = query.origin_name
      // @ts-expect-error
      const mimeType: string | null = query.mime_type
      // 先把文件名中包含 originName 的文件筛选出来 要考虑 query.origin_name 为空的则不过滤了
      const filteredData = attachments.filter((item) => {
        let isMatch = true
        if (originName) {
          isMatch = item.origin_name.includes(originName)
        }
        if (mimeType) {
          isMatch = isMatch && item.mime_type.includes(mimeType)
        }
        return isMatch
      })

      // 根据分页信息从预定义数据中选取一部分返回
      const pagedData = filteredData.slice(start, end)
      return {
        success: true,
        message: '请求成功',
        code: 200,
        data: {
          total: pagedData.length, // 总数据量
          items: pagedData, // 当前页的数据
        },
      }
    },
  },
])
