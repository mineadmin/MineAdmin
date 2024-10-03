import { h, render } from 'vue'
import type { ImageViewerProps } from 'element-plus'
import { ElImageViewer } from 'element-plus'

type Options = Partial<Omit<ImageViewerProps, 'urlList'>>
export function useImageViewer(images: string[], options?: Options) {
  const imageViewerDom = document.createElement('div')

  const viewerProps = {
    urlList: images,
    hideOnClickModal: true,
    zIndex: 2500,
    initialIndex: 0,
    ...options,
    onClose: () => {
      render(null, imageViewerDom)
      if (document.body.contains(imageViewerDom)) {
        document.body.removeChild(imageViewerDom)
      }
    },
  }

  const vnode = h(ElImageViewer, viewerProps)
  document.body.appendChild(imageViewerDom)
  render(vnode, imageViewerDom)
}
