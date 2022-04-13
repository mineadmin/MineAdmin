<template>
  <el-main>
    <ma-chunk-upload v-model="test" />
    <el-row :gutter="15">
      <el-col :lg="24">
        <el-card shadow="never" header="选择资源组件">
          <sc-upload v-model="avatar" title="选择头像" file-select></sc-upload>

          <sc-upload-multiple
            v-model="imagelist"
            title="多选"
            file-select
          ></sc-upload-multiple>

          <sc-file-select v-model="scuiList" style="margin-top: 10px" />

          <div class="selectResource">
            <p>选择的资源：</p>
            {{ scuiList }}
          </div>
        </el-card>

        <el-card shadow="never" header="二维码组件">
          <el-row :gutter="20">
            <el-col :span="8">
              <el-card shadow="never" header="常用"
                ><sc-qr-code text="mineadmin"></sc-qr-code
              ></el-card>
            </el-col>
            <el-col :span="8">
              <el-card shadow="never" header="带Logo">
                <sc-qr-code text="mineadmin" logo="img/logo.svg"></sc-qr-code>
              </el-card>
            </el-col>
            <el-col :span="8">
              <el-card shadow="never" header="自定义大小和颜色">
                <sc-qr-code
                  text="mineadmin"
                  :size="100"
                  colorDark="#088200"
                  colorLight="#fff"
                ></sc-qr-code>
              </el-card>
            </el-col>
          </el-row>
        </el-card>
        <el-card shadow="never" header="水印组件">
          <sc-water-mark
            ref="wm"
            text="欢迎体验MineAdmin"
            subtext="www.mineadmin.com"
          >
            <el-table :data="wmData" border stripe style="width: 100%">
              <el-table-column prop="date" label="Date" width="180" />
              <el-table-column prop="name" label="Name" width="180" />
              <el-table-column prop="address" label="Address" />
            </el-table>
          </sc-water-mark>
          <div style="margin-top: 10px">
            <el-button type="primary" @click="$refs.wm.create()"
              >创建水印</el-button
            >
            <el-button type="primary" @click="$refs.wm.clear()"
              >移除水印</el-button
            >
          </div>
        </el-card>
        <el-card shadow="never" header="选择用户组件">
          <ma-select-user v-model="users" />
          <div class="selectResource">
            <p>选择的用户：</p>
            {{ users }}
          </div>
        </el-card>

        <el-card shadow="never" header="SCUI视频组件">
          <sc-video
            src="https://hsplay-360.v.btime.com/live_btime/btv_sn_20170706_s4/index.m3u8"
            isLive
          ></sc-video>
        </el-card>

        <el-card shadow="never" header="城市联级选择器">
          <el-alert title="返回城市编码" type="warning" />
          <city-linkage v-model="cityDataCode" valueType="code" />

          <p>{{ cityDataCode }}</p>

          <el-alert
            title="返回城市名称，并限制层级，只显示省份"
            type="warning"
            class="mt"
          />
          <city-linkage v-model="cityDataName" valueType="name" />

          <p class="value">{{ cityDataName }}</p>
        </el-card>

        <el-card shadow="never" header="城市下拉联动选择器">
          <el-alert title="返回城市编码" type="warning" />
          <three-level-linkage v-model="cityDataCode2" valueType="code" />
          <p>{{ cityDataCode2 }}</p>

          <el-alert title="返回城市名称" type="warning" class="mt" />
          <three-level-linkage v-model="cityDataName2" valueType="name" />
          <p>{{ cityDataName2 }}</p>
        </el-card>

        <el-card shadow="never" header="城市code翻译成城市名称函数">
          <el-alert
            title="codeToCity('省代码', '市代码', '区代码', '分隔符')"
            type="warning"
          />

          <h2>{{ codeToCity("11", "1101", "110105") }}</h2>
          <h2>{{ codeToCity("11", "1101", "110105", " - ") }}</h2>
          <h2>{{ codeToCity("11", "1101") }}</h2>
          <h2>{{ codeToCity("11") }}</h2>
        </el-card>
      </el-col>
    </el-row>
  </el-main>
</template>
<script>
import cityLinkage from "@/components/maCityLinkage";
import scVideo from "@/components/scVideo";
import threeLevelLinkage from "@/components/maCityLinkage/threeLevelLinkage";

export default {
  name: "demo",

  components: {
    scVideo,
    cityLinkage,
    threeLevelLinkage,
  },

  data() {
    return {
      avatar: "",
      imagelist: "",
      test: 'asdfdsf',
      list: [],
      users: [],
      scuiList: null,
      cityDataCode: [],
      cityDataName: [],
      cityDataCode2: {},
      cityDataName2: {},

      wmData: [
        {
          date: "2016-05-03",
          name: "Tom",
          address: "No. 189, Grove St, Los Angeles",
        },
        {
          date: "2016-05-02",
          name: "Tom",
          address: "No. 189, Grove St, Los Angeles",
        },
        {
          date: "2016-05-04",
          name: "Tom",
          address: "No. 189, Grove St, Los Angeles",
        },
        {
          date: "2016-05-01",
          name: "Tom",
          address: "No. 189, Grove St, Los Angeles",
        },
      ],

      qrcode: "mineadmin",
    };
  }
};
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