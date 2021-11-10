<template>
  <drag-verify-img-chip
    class="dragVerify"
    :imgsrc="img"
    v-model:isPassing="isPassing"
    :showRefresh="true"
    :text="text"
    :successText="successText"
    handlerIcon="el-icon-d-arrow-right"
    successIcon="el-icon-circle-check"
    :diffWidth="5"
    @refresh="reimg"
    @passcallback="pass"
  >
  </drag-verify-img-chip>
</template>
<script>
import dragVerifyImgChip from './components/dragVerifyImgChip'
export default {
  name: 'imageChip',

  props: {
    modelValue: {
      type: Boolean,
      default: false
    },

    images: {
      type: Array
    },

    text: {
      type: String,
      default: '请按住滑块拖动'
    },

    successText: {
      type: String,
      default: '验证通过'
    }
  },

  watch: {
    isPassing (val) {
      this.$emit('update:modelValue', val)
    }
  },

  components: {
    dragVerifyImgChip
  },

  created () {
    this.setImg()
  },

  data () {
    return {
      isPassing: false,
      img: '',
    }
  },

  methods: {
    setImg () {
      if (this.images) {
        let number = Math.floor( Math.random() * this.images.length )
        this.img = this.images[number]
      } else {
        this.img = ''
      }
    },
    reimg() {

    },

    pass() {
      
    }
  }

}
</script>
<style lang="scss" scoped>
</style>