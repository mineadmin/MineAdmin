<script lang="tsx">
import type { PropType } from 'vue'
import { defineComponent } from 'vue'
import { cellRenderPluginName, createOptions, createRowFieldValues, getConfig } from '../../utils/tools.ts'
import {ElLink,ElPopover,ElRow,ElCol,ElAvatar} from "element-plus";
import UserInfo from './components/userInfo.vue'
import defaultAvatar from "@/assets/images/defaultAvatar.jpg";

// 定义options类型,与ImageProps类型合并
export interface Options {

}

export default defineComponent({
  name: cellRenderPluginName('user'),
  computed: {
    defaultAvatar() {
      return defaultAvatar
    }
  },
  props: {
    scope: {
      type: Object,
      default: () => ({}),
    },
    options: {
      type: Object as PropType<Options>,
      default: () => ({}),
    },
  },
  setup(props) {
    const { value } = createRowFieldValues(props)
    const options = createOptions(props, getConfig('user'))

    const avatar = computed(()=>{
      return (value.value.avatar === '' || !value.value.avatar) ? defaultAvatar : value.value.avatar
    })

    const bind = computed(() => {
      const {
        ...rest
      } = options.value
      return rest
    })

    return ()=>(
      <>
      <ElPopover
        placement="right-start"
        width={280}
        trigger="click"
        content="this is content, this is content, this is content"
        v-slots={{
          reference:()=>(
            <ElLink>
              <ElAvatar src={avatar.value} size={25}/>
              <span class="ml-4px">{value.value.nickname}</span>

            </ElLink>
          ),
          default: () => (<UserInfo user={value.value}></UserInfo>)
        }}
      >
      </ElPopover>
      </>
    )
  },
})
</script>
