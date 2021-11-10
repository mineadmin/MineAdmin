<template>
  <el-dialog title="保存网络图片" v-model="openDialog" width="450px" :before-close="handleResClose">
    <div>
      网络图片地址：
      <el-input style="margin-top: 5px;" v-model="url">
        <template #append>
          <el-button @click="url = ''">清空</el-button>
        </template>
      </el-input>

      <el-image class="image" :src="url" fit="contain" :preview-src-list="[ url ]">
        <template #error class="image-slot" >
          <i class="el-icon-picture-outline"></i>
        </template>
      </el-image>
    </div>

    <template #footer class="dialog-footer">

        <el-button @click="openDialog = false" size="small">
          关 闭
        </el-button>

        <el-button
          type="primary"
          @click="save"
          size="small">
          保存到本地
        </el-button>
        
        <el-button
          type="success"
          @click="useImage"
          size="small">
          使用图片
        </el-button>
        
      </template>
  </el-dialog>
</template>
<script>
export default {
  name: 'networkImage',

  emits: ['useSuccess', 'saveSuccess'],

  props: {
    path: {
      default: '',
      type: String
    }
  },

  data() {
    return {
      openDialog: false,
      url: '',
    }
  },

  methods: {

    open () {
      this.openDialog = true
      this.url = ''
    },

    handleResClose() {
      this.openDialog = false
    },

    save() {
      this.$API.upload.saveNetWorkImage({ url: this.url, path: this.path }).then(res => {
        this.$emit('saveSuccess', res.data)
        this.openDialog = false
        this.$message.success('保存成功')
      })
    },

    useImage() {
      if (this.url === '' || this.url === null) {
        this.$message.error('没有网络图片')
      }
      this.$emit('useSuccess', this.url)
      this.openDialog = false
      this.$message.success('使用成功')
    }
  }
}
</script>
<style scoped>
.image {
  margin-top: 20px;
  width: 100%;
  height: 260px;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 4px;
}
.image i {
  font-size: 180px;
  color: #ccc;
}
</style>