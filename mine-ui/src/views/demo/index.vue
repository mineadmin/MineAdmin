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

                <el-card shadow="never" header="选择资源组件（SCUI版)">
                    <sc-file-select v-model="scuiList" />

                    <div class="selectResource">
                        <p>选择的资源：</p>
                        {{ scuiList }}
                    </div>
                </el-card>

                <el-card shadow="never" header="城市联级选择器">
                    
                    <el-alert title="返回城市编码" type="warning" />
                    <city-linkage v-model="cityDataCode" valueType="code"/>

                    <p>{{cityDataCode}}</p>

                    <el-alert title="返回城市名称，并限制层级，只显示省份" type="warning" class="mt" />
                    <city-linkage v-model="cityDataName" valueType="name" />

                    <p class="value">{{cityDataName}}</p>
                </el-card>

                <el-card shadow="never" header="城市下拉联动选择器">
                    <el-alert title="返回城市编码" type="warning" />
                    <three-level-linkage v-model="cityDataCode2" valueType="code" />
                    <p>{{cityDataCode2}}</p>

                    <el-alert title="返回城市名称" type="warning" class="mt" />
                    <three-level-linkage v-model="cityDataName2" valueType="name" />
                    <p>{{cityDataName2}}</p>
                </el-card>

                <el-card shadow="never" header="城市code翻译成城市名称函数">
                    <el-alert title="codeToCity('省代码', '市代码', '区代码', '分隔符')" type="warning" />

                    <h2>{{ codeToCity('11', '1101', '110105') }}</h2>
                    <h2>{{ codeToCity('11', '1101', '110105', ' - ') }}</h2>
                    <h2>{{ codeToCity('11', '1101') }}</h2>
                    <h2>{{ codeToCity('11') }}</h2>

                </el-card>
            </el-col>
        </el-row>
    </el-main>
</template>
<script>
import cityLinkage from '@/components/maCityLinkage'
import threeLevelLinkage from '@/components/maCityLinkage/threeLevelLinkage'

export default {
    name: 'demo',

    components: {
        cityLinkage,
        threeLevelLinkage
    },

    data () {
        return {
            list: [],
            scuiList: null,
            cityDataCode: [],
            cityDataName: [],
            cityDataCode2: {},
            cityDataName2: {}
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
.mt {
    margin-top: 15px;
}
.selectResource {
    margin: 15px 0;
    font-size: 14px;
    line-height: 25px;
}
.value {
    margin: 15px 0;
}
</style>