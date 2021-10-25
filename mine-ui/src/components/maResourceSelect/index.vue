 <template>
  <el-main style="padding-left: 0">

    <div
      v-if="imageList.length === 0 && thumb"
      class="el-upload el-upload--picture-card ma-upload"
      @click="handleShowUploadDialog">
      <i class="el-icon-plus"/>
    </div>

    <ma-photo :value="imageList" v-if="thumb" @remove="remove" />

    <el-row :gutter="0">

        <el-button v-if="resource" icon="el-icon-finished" size="small" class="button" :disabled="disabled" @click="$refs.Res.show()">
          {{ selectButtonText }}
        </el-button>

        <el-button icon="el-icon-upload2" type="primary" class="button" size="small" @click="handleShowUploadDialog" :disabled="disabled">
          {{ uploadButtunText }}
        </el-button>

    </el-row>

    <el-dialog :title="uploadButtunText" v-model="uploadDialog" width="420px" :before-close="handleUploadClose">

      <el-select v-model="uploadDir" filterable placeholder="请选择上传目录" style="width: 100%" size="small">
        <el-option
          v-for="item in dirs"
          :key="item.path"
          :label="item.path === '' ? '根目录按日期存放' : item.path"
          :value="item.path"></el-option>
      </el-select>

      <el-button
        size="small"
        class="ma-mt-10"
        style="width: 100%"
        icon="el-icon-plus"
        @click="createDir"
        >新建目录
      </el-button>

      <el-upload
        class="mt-20"
        ref="upload"
        drag
        :multiple="multiple"
        :accept="allowUploadFile"
        style="width: 100%"
        action="Fake Action"
        :disabled="disabled"
        :limit="limit"
        :auto-upload="false"
        :file-list="fileList"
        :on-exceed="handleExceed"
        :on-change="handleChange"
        :on-remove="handleRemove"
        :http-request="handleUpload"
      >

        <i class="el-icon-upload"></i>

        <div class="el-upload__text" style="width: 100%">将文件拖到此处，或<em>点击上传</em></div>
        <div class="el-upload__tip" style="width: 100%">只能上传{{allowUploadFile}}文件，单文件不超过 {{ config.maxSize }} M</div>

      </el-upload>

      <template #footer class="dialog-footer">

        <el-button @click="uploadDialog = false" size="small">
          关 闭
        </el-button>

        <el-button
          type="primary"
          @click="uploadSubmit"
          :loading="loading"
          :disabled="disabled" size="small">
          上 传
        </el-button>

      </template>

    </el-dialog>

    <res ref="Res" :type="type" @confirmData="getConfirmData"></res>
  </el-main>
</template>
<script>
import Res from './components/res'
import Config from '@/config/upload'
export default {
  name: 'maResourceSelect',

  emits: ['uploadData'],

  components: {
    Res
  },
  props: {
    value: {
      type: Array,
      default: () => []
    },
    // 组件类型， image图片  file文件上传
    type: {
      default: 'image',
      type: String
    },
    // 上传个数限制
    limit: {
      default: 5,
      type: Number
    },
    // 是否支持多选
    multiple: {
      type: Boolean,
      required: false,
      default: true
    },
    // 是否禁用
    disabled: {
      type: Boolean,
      required: false,
      default: false
    },
    // 是否显示选择按钮
    resource: {
      type: Boolean,
      required: false,
      default: true
    },
    // 是否显示图片缩略图
    thumb: {
      type: Boolean,
      required: false,
      default: false
    }
  },
  data () {
    return {
      // 选择按钮文字
      selectButtonText: '',
      // 上传按钮文字
      uploadButtunText: '',
      // 上传modal框
      uploadDialog: false,
      // loading
      loading: false,
      // 目录列表
      dirs: [],
      // 上传目录
      uploadDir: '',
      // 待上传文件列表
      fileList: [],
      // 上传后文件数据
      fileData: [],
      // 上传方法
      uploadMethod: null,
      // 配置信息
      config: Config,
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
    }
  },
  created () {
    if (this.type === 'image') {
      this.selectButtonText = '选择图片'
      this.uploadButtunText = '上传图片'
      this.allowUploadFile = this.config.images
      this.uploadMethod = this.$API.upload.uploadImage
    } else {
      this.selectButtonText = '选择文件'
      this.uploadButtunText = '上传文件'
      this.allowUploadFile = this.config.files
      this.uploadMethod = this.$API.upload.uploadFile
    }
  },
  methods: {

    // 获取目录内容
    getDirectorys () {
      this.$API.upload.getDirectory({ path: '/', isChildren: true }).then(res => {
        this.dirs = res.data
        this.dirs.unshift({ path: '' })
      })
    },

    // 显示modal
    handleShowUploadDialog () {
      this.uploadDialog = true
      this.uploadDir = ''
      this.fileList = []
      this.getDirectorys()
    },

    // 关闭modal
    handleUploadClose () {
      this.uploadDialog = false
      this.fileList = []
    },

    // 上传处理方法，空方法。
    handleUpload (data) { },

    // 获取选择资源里的数据
    getConfirmData (data) {
      this.uploadDialog = false
      if (data.length > 0) {
        data.map(item => this.imageList.push(item))
        this.$emit('uploadData', this.imageList)
        this.$message.success('选择成功')
      }
    },

    // 提交上传
    uploadSubmit () {
      this.loading = true

      if (this.type === 'image' || this.type === 'file') {
        this.fileList.forEach(async item => {
          const dataForm = new FormData()
          dataForm.append(this.type, item.raw)
          dataForm.append('path', this.uploadDir)
          await setTimeout(async () => {
            await this.uploadMethod(dataForm).then(res => {
              this.fileData.push(res.data)
            })
          }, 1000)
        })
        this.loading = false
        this.uploadDialog = false
        if (this.fileList.length > 0) {
          this.fileData.map(item => this.imageList.push(item))
          this.$emit('uploadData', this.fileData)
          this.$message.success('上传成功')
        }
      } else {
        this.$message.error('上传类型指定错误，组件type只能是image或者file')
        this.loading = false
        return false
      }
    },

    remove(data) {
      this.$emit('uploadData', data)
    },

    // 处理修改事件
    handleChange (file, fileList) {
      this.fileList = fileList
    },

    // 处理移除事件
    handleRemove (file, fileList) {
      this.fileList = fileList
    },

    // 文件超出个数限制时的钩子
    handleExceed (files, fileList) {
      if (fileList.length >= this.limit) {
        this.$message.error(`最多只能上传 ${this.limit} 个文件`)
        return
      }

      if (files.length + fileList.length > this.limit) {
        const count = this.limit - fileList.length
        this.$message.error(`上传数量超出限制，最多还能选择 ${count} 个文件`)
      }
    },

    // 请求创建目录
    createDir () {
      this.$prompt('请输入目录名称（只允许字母、数字和下划线组成）', '新建目录', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        inputPattern: /^[A-Za-z0-9_]+$/,
        inputErrorMessage: '请输入合法的目录名称'
      }).then(({ value }) => {
        this.$API.upload.createUploadDir({ name: value, path: this.uploadDir }).then(res => {
          this.success(res.message)
          this.getDirectorys()
        })
      })
    }

  }
}
</script>
<style scoped lang="scss">
:deep(.button) {
  position: relative !important;
  z-index: 1 !important;
}

.padding-10 {
  padding: 0 10px !important;
}
.padding-left{
  padding-left: 7px;
}
.ma-mt-10 {
	margin-top: 10px;
}
:deep(.el-upload) {
	width: 100%;
	& .el-upload-dragger {
		width: 100% !important;
		margin-top: 20px;
	}
}
.ma-upload {
  width: 120px; height: 120px; display: flex;
  justify-content: center; align-items: center;
  margin-bottom: 10px;
}

</style>
