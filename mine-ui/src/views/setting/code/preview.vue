<template>
  <el-dialog
    append-to-body
    title="预览代码"
    v-model="dialogVisible"
    width="1220px"
    :before-close="handleDialogClose"
  >
    <el-tabs v-model="activeName">

      <el-tab-pane
        v-for="(item, index) in previewCode"
        :key="index"
        :label="item.tab_name"
        :name="item.name"
      >
        <el-alert
          title="说明"
          type="info"
          :closable="false"
          v-if="item.name === 'model'"
          description="模型类预览的代码与实际生成的代码会稍微不同"
          style="margin-bottom: 10px;"
        ></el-alert>
        <ma-highlight :code="item.code" :lang="item.lang" />

      </el-tab-pane>

    </el-tabs>
  </el-dialog>
</template>
<script>
import maHighlight from '@/components/maHighlight'
export default {

  components: {
    maHighlight
  },

  data () {
    return {
      // modal
      dialogVisible: false,
      // 激活tab
      activeName: 'controller',
      // 要预览的代码
      previewCode: []
    }
  },

  methods: {

    // 显示modal
    async show (id) {
      this.activeName = 'controller'
      await this.$API.generate.preview({ id }).then(res => {
        if (res.success) {
          this.previewCode = res.data
          this.dialogVisible = true
        } else {
          this.$message.error(res.message)
        }
      })
    },

    // 表字段modal关闭
    handleDialogClose () {
      this.dialogVisible = false
    }
  }
}
</script>
