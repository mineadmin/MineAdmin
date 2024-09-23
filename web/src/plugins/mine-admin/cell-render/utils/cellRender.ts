import type { ImageOptions, LabelOptions, SwitchOptions,UserOptions } from '../type'
import Label from '../components/label/index.vue'
import Image from '../components/image/index.vue'
import Amount from '../components/amount/index.vue'
import Switch from '../components/switch/index.vue'
import User from '../components/user/index.vue'

export default function cellRender(render: (component, options) => any) {
  return {
    label(map: LabelOptions['map'], options?: Omit<LabelOptions, 'map'>) {
      options = options || {}
      const _options = { map, ...options }
      return render(Label, _options)
    },
    // urlOrApi 可以是字符串表示请求地址,也可以是 api函数
    switch(api: SwitchOptions['api'], options?: Omit<SwitchOptions, 'api'>) {
      options = options || {}
      const _options = { api, ...options }
      return render(Switch, _options)
    },
    image(options?: ImageOptions) {
      return render(Image, options)
    },
    amount(options?: any) {
      return render(Amount, options)
    },
    user(options?:  UserOptions){
      return render(User, options)
    }
  }
}
