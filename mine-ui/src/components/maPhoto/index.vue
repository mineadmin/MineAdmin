<template>
  <el-main style="padding: 0">
    <ul class="el-upload-list el-upload-list--picture-card">
      <li
        v-for="(item, index) in imageList"
        :key="index"
        class="el-upload-list__item"
        :style="`width:120px; height:120px;`"
      >
        <div class="thumbnail" :style="`width:120px; height:120px;`">
          <div class="mask">
            <span class="del" @click.stop="remove(index)">
              <el-icon><el-icon-delete /></el-icon>
            </span>
          </div>
          <el-image :src="viewImage(item.url)" fit="cover" :preview-src-list="preview" hide-on-click-modal append-to-body>
            <template #placeholder>
              <el-icon-more />
            </template>
          </el-image>
        </div>
      </li>
    </ul>

  </el-main>
</template>

<script>
export default {
  emits: ['remove'],
  props: {
    // 外部v-model值
    value: {
      type: Array,
      default: () => []
    },
    disabled: {
      default: false
    }
  },
  data () {
    return {
      isDrag: false
    }
  },
  computed: {
    imageList: {
      get () {
        return this.value
      },
      set (value) {
        this.$emit('input', value)
      }
    },
    preview(){
      return this.imageList.map(v => this.viewImage(v.url))
    },
  },
  methods: {
    remove (index) {
      this.imageList.splice(index, 1)
      this.$emit('remove', this.imageList)
    }
  }
}
</script>

<style scoped lang="scss">
.thumbnail {
  display: flex;
  justify-content:center;
  align-items: center;
  position: relative;
}
.mask {display: none;position: absolute;top:0px;right:0px;line-height: 1;z-index: 1;}
.mask span {display: inline-block;width: 25px;height:25px;line-height: 23px;text-align: center;cursor: pointer;color: #fff;}
.mask span i {font-size: 12px;}
.mask .del {background: var(--el-color-primary);}
.thumbnail:hover .mask {display: inline-block;}
.el-upload-list--picture-card .el-upload-list__item {
  border-radius: 4px;
  border: none;
}
</style>
