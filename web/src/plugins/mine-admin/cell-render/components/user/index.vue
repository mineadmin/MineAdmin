<script lang="tsx">
import { defineComponent } from 'vue'
import { ElAvatar, ElLink, ElPopover } from 'element-plus'
import {
  cellRenderPluginName,
  cellRenderPluginProps,
  createOptions,
  getConfig,
  organizeProps,
} from '../../utils/tools.ts'
import UserInfo from './components/userInfo.vue'
import defaultAvatar from '@/assets/images/defaultAvatar.jpg'

// 定义options类型,与ImageProps类型合并
export interface Options {

}

export default defineComponent({
  name: cellRenderPluginName('user'),
  props: cellRenderPluginProps(),
  setup(props) {
    const { value } = organizeProps(props)
    const options = createOptions(props, getConfig('user'))

    const avatar = computed(() => {
      return (value.value.avatar === '' || !value.value.avatar) ? defaultAvatar : value.value.avatar
    })

    const bind = computed(() => {
      const {
        ...rest
      } = options.value
      return rest
    })

    return () => (
      <>
        <ElPopover
          placement="right-start"
          width={280}
          trigger="click"
          content="this is content, this is content, this is content"
          v-slots={{
            reference: () => (
              <ElLink underline={false}>
                <ElAvatar src={avatar.value} size={25} />
                <span class="ml-4px">{value.value.nickname}</span>
              </ElLink>
            ),
            default: () => (<UserInfo user={value.value}></UserInfo>),
          }}
        >
        </ElPopover>
      </>
    )
  },
  computed: {
    defaultAvatar() {
      return defaultAvatar
    },
  },
})
</script>
