<template>

    <el-button
        type="success"
        v-auth="auth"
        icon="el-icon-upload"
        @click="visible = true"
        style="margin: 0 10px;"
    >导入</el-button>

    <el-dialog
        title="导入数据"
        v-model="visible"
        :width="400"
        destroy-on-close
        @closed="$emit('closed')"
    >
        <el-upload
            drag
            action="#"
            :show-file-list="false"
            :auto-upload="true"
            accept=".xlsx,.xls"
            :http-request="uploadFile"
        >
            <el-icon-upload style="width:140px; color: #ccc" />
            <div class="el-upload__text">将文件拖到此处，或<em>点击上传</em></div>
            <template #tip>
                <div class="el-upload__tip">只能上传 xlsx/xls 文件</div>
            </template>
        </el-upload>

        <el-button @click="downloadTemplate" type="primary" style="margin-top: 15px;">下载模板</el-button>

        <template #footer>
            <el-button @click="visible=false" >关闭</el-button>
        </template>
    </el-dialog>
</template>

<script>
export default {
    emits: ['success', 'closed'],
    props: {
        auth: {
            default: () => {},
            type: Array
        },
        uploadApi: {
            default: () => {},
            type: Function,
        },
        downloadTplApi: {
            default: () => {},
            type: Function,
        }
    },
    data () {
        return {
            visible: false,
        }
    },
    methods: {

        // 下载模板
        downloadTemplate () {
            this.downloadTplApi().then(res => {
                this.$TOOL.download(res)
            })
        },

        // 上传导入文件
        async uploadFile (param) {
            let form = new FormData()
            form.append('file', param.file)
            await this.uploadApi(form).then( res => {
                if (res.success) {
                    this.$message.success(res.message)
                    this.visible = false
                    this.$emit('success')
                }
            })
        }
    }
}
</script>
