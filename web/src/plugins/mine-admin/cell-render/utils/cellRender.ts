import type { ImageOptions, LabelOptions } from '../type'
import Label from '../components/label/index.vue'
import Image from '../components/image/index.vue'
import Amount from '../components/amount/index.vue'

export default function cellRender(render: (component, options) => any) {
  return {
    label(map: LabelOptions['map'], options?: Omit<LabelOptions, 'map'>) {
      options = options || {}
      const _options = { map, ...options }
      return render(Label, _options)
    },
    // switch(urlOrOptions: Record<any, any> | string) {
    //   let options: Record<any, any> = {
    //     url: '',
    //   }
    //   if (typeof urlOrOptions === 'string') {
    //     options.url = urlOrOptions
    //   }
    //   else {
    //     options = urlOrOptions
    //   }
    //   return render(Switch, options)
    // },
    image(options?: ImageOptions) {
      // 获取Image 的类型定义
      return render(Image, options)
    },
    amount(options?: any) {
      return render(Amount, options)
    },
  }
}
