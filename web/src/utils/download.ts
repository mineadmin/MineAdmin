import request from '@/utils/http'
import type { AxiosResponse } from 'axios'

interface DownloadOptions {
  url: string
  method?: 'GET' | 'POST'
  data?: any
  fileName?: string
  mimeType?: string
  onProgress?: (progress: number) => void
}

declare global {
  interface Navigator {
    msSaveOrOpenBlob?: (blob: Blob, defaultName?: string) => boolean
  }
}

interface DownloadResponse extends AxiosResponse {
  fileName?: string
}

/**
 * 文件下载工具函数
 * @param options 下载配置项
 */
export async function downloadFile(options: DownloadOptions) {
  const {
    url,
    method = 'GET',
    data,
    fileName,
    mimeType,
    onProgress,
  } = options

  const response = await request.http({
    url,
    method,
    data,
    responseType: 'blob',
    onDownloadProgress: (progressEvent) => {
      if (onProgress && progressEvent.total) {
        const progress = Math.round((progressEvent.loaded * 100) / progressEvent.total)
        onProgress(progress)
      }
    },
  }) as DownloadResponse

  const downloadFileName = fileName || response.fileName || '未命名文件'

  // 创建 Blob
  const blob = new Blob([response.data], {
    type: mimeType || response.headers['content-type'],
  })

  if (window.navigator && window.navigator.msSaveOrOpenBlob) {
    window.navigator.msSaveOrOpenBlob(blob, downloadFileName)
    return
  }

  const downloadUrl = window.URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = downloadUrl
  link.download = downloadFileName
  link.style.display = 'none'
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  window.URL.revokeObjectURL(downloadUrl)
}
