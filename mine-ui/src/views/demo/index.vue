<template>
    <el-main>
        <el-row :gutter="15">
            <el-col :lg="24">
                <el-card shadow="never" header="选择资源组件（MineAdmin版)">
                    <el-alert title="只有上传文件" type="warning" />
                    <ma-resource-select :resource="false" @upload-data="handleSuccess" />

                    <el-alert title="有选择图片" type="warning" style="margin-top: 20px;" />
                    <ma-resource-select :resource="true" @upload-data="handleSuccess" />

                    <el-alert title="包含预览" type="warning" style="margin-top: 20px;" />
                    <ma-resource-select :resource="true" :thumb="true" :value="list" @upload-data="handleSuccess" />
                    <div class="selectResource">
                        <p>选择的资源：</p>
                        {{ list }}
                    </div>
                </el-card>
            </el-col>
        </el-row>
    </el-main>
</template>
<script>
export default {
    name: 'demo:index',
    data () {
        return {
            list: []
        }
    },

    methods: {
        handleSuccess(data) {
            this.list = data
        }
    }
}
</script>

<style scoped>
.selectResource {
    margin: 15px 0;
    font-size: 14px;
    line-height: 25px;
}
</style>