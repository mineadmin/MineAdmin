<template>
  <el-dialog
    title="定时任务表达式生成器"
    v-model="visible"
    :width="550"
    destroy-on-close
    @closed="$emit('closed')"
  >
    <template #reference>
      <el-input v-model="defaultValue" placeholder="请输入Cron规则" clearable />
    </template>
    <el-tabs type="border-card" v-model="activeName">
      <el-tab-pane label="秒" name="second">
        <second
          :check="checkNumber"
          @update="updateCrontabValue"
          ref="cronsecond"
        />
      </el-tab-pane>
      <el-tab-pane label="分钟" name="minute">
        <minute
          :check="checkNumber"
          :cron="crontabValueObj"
          @update="updateCrontabValue"
          ref="cronminute"
        />
      </el-tab-pane>
      <el-tab-pane label="小时" name="hour">
        <hour
          :check="checkNumber"
          :cron="crontabValueObj"
          @update="updateCrontabValue"
          ref="cronhour"
        />
      </el-tab-pane>
      <el-tab-pane label="日" name="day">
        <day
          :check="checkNumber"
          :cron="crontabValueObj"
          @update="updateCrontabValue"
          ref="cronday"
        />
      </el-tab-pane>
      <el-tab-pane label="月" name="month">
        <month
          :check="checkNumber"
          :cron="crontabValueObj"
          @update="updateCrontabValue"
          ref="cronmonth"
        />
      </el-tab-pane>
      <el-tab-pane label="周" name="week">
        <week
          :check="checkNumber"
          :cron="crontabValueObj"
          @update="updateCrontabValue"
          ref="cronweek"
        />
      </el-tab-pane>
    </el-tabs>

    <div class="popup-main">
      <div class="popup-result">
        <p class="title">时间表达式</p>
        <table>
          <thead>
            <th v-for="item of tabTitles" width="40" :key="item">{{item}}</th>
            <th>Cron 表达式</th>
          </thead>
          <tbody>
            <td>
              <span>
                {{ (typeof crontabValueObj.second == 'function') ? crontabValueObj.second() : crontabValueObj.second }}
              </span>
            </td>
            <td>
              <span>
                {{ (typeof crontabValueObj.min == 'function') ? crontabValueObj.min() : crontabValueObj.min }}
              </span>
            </td>
            <td>
              <span>
                {{ (typeof crontabValueObj.hour == 'function') ? crontabValueObj.hour() : crontabValueObj.hour }}
              </span>
            </td>
            <td>
              <span>
                {{ (typeof crontabValueObj.day == 'function') ? crontabValueObj.day() : crontabValueObj.day }}
              </span>
            </td>
            <td>
              <span>
                {{ (typeof crontabValueObj.month == 'function') ? crontabValueObj.month() : crontabValueObj.month }}
              </span>
            </td>
            <td>
              <span>
                {{ (typeof crontabValueObj.week == 'function') ? crontabValueObj.week() : crontabValueObj.week }}
              </span>
            </td>
            <td>
              <span>{{crontabValueString}}</span>
            </td>
          </tbody>
        </table>
      </div>
    </div>
    <template #footer>
      <el-button @click="visible = false">取 消</el-button>
      <el-button type="primary" :loading="isSaveing" @click="confirm()"
        >确 定</el-button
      >
    </template>
  </el-dialog>
</template>

<script>
import second from "./components/second"
import minute from "./components/minute"
import hour from "./components/hour"
import day from "./components/day"
import month from "./components/month"
import week from "./components/week"

export default {
  name: "crontab:expression",

  emits: ['update'],

  components: {
    second,
    minute,
    hour,
    day,
    month,
    week
  },

  props: ['expressionValue'],

  data() {
    return {
      tabTitles: ["秒", "分钟", "小时", "日", "月", "周"],
      isSaveing: false,
      visible: false,
      activeName: "second",
      crontabValueObj: {
        second: "*",
        min: "*",
        hour: "*",
        day: "*",
        month: "*",
        week: "*",
      },
    };
  },

  computed: {
    crontabValueString: function() {
      let obj = this.crontabValueObj;
      let str =
        ((typeof obj.second == 'function') ? obj.second() : obj.second) +
        " " +
        ((typeof obj.min == 'function') ? obj.min() : obj.min) +
        " " +
        ((typeof obj.hour == 'function') ? obj.hour() : obj.hour) +
        " " +
        ((typeof obj.day == 'function') ? obj.day() : obj.day) +
        " " +
        ((typeof obj.month == 'function') ? obj.month() : obj.month) +
        " " +
        ((typeof obj.week == 'function') ? obj.week() : obj.week)

      for (let i = 0; i < 6; i++) {
        str = str.replace('?' , '-')
      }
      return str;
    },
  },

  methods: {
    init() {
      this.isSaveing = false;
      this.visible = true;
      this.resolveExp()
    },

    getResult() {
      this.$emit('update', this.crontabValueString)
    },

    resolveExp() {
      // 反解析 表达式
      if (this.expressionValue) {
        let arr = this.expressionValue.split(" ");
        if (arr.length === 6) {
          //6 位是合法表达式
          let obj = {
            second: arr[0],
            min: arr[1],
            hour: arr[2],
            day: arr[3],
            month: arr[4],
            week: arr[5],
          };
          this.crontabValueObj = {
            ...obj,
          };
          for (let i in obj) {
            if (obj[i]) this.changeRadio(i, obj[i]);
          }
        }
      } else {
        // 没有传入的表达式 则还原
        this.clearCron();
      }
    },

    clearCron() {
      // 还原选择项
      ("准备还原");
      this.crontabValueObj = {
        second: "*",
        min: "*",
        hour: "*",
        day: "*",
        month: "*",
        week: "?",
        year: "",
      };
      for (let j in this.crontabValueObj) {
        this.changeRadio(j, this.crontabValueObj[j]);
      }
    },

    // tab切换值
    tabCheck(index) {
      this.tabActive = index;
    },

    confirm() {
      this.getResult()
      this.isSaveing = true;
      this.visible = false;
    },

    updateCrontabValue(name, value, from) {
      "updateCrontabValue", name, value, from;
      this.crontabValueObj[name] = value;
      if (from && from !== name) {
        this.changeRadio(name, value);
      }
    },

    // 赋值到组件
    changeRadio(name, value) {
      let arr = ["second", "min", "hour", "month"],
        refName = "cron" + name,
        insValue;

      if (!this.$refs[refName]) return;

      if (arr.includes(name)) {
        if (value === "*") {
          insValue = 1;
        } else if (value.indexOf("-") > -1) {
          let indexArr = value.split("-");
          isNaN(indexArr[0])
            ? (this.$refs[refName].cycle01 = 0)
            : (this.$refs[refName].cycle01 = indexArr[0]);
          this.$refs[refName].cycle02 = indexArr[1];
          insValue = 2;
        } else if (value.indexOf("/") > -1) {
          let indexArr = value.split("/");
          isNaN(indexArr[0])
            ? (this.$refs[refName].average01 = 0)
            : (this.$refs[refName].average01 = indexArr[0]);
          this.$refs[refName].average02 = indexArr[1];
          insValue = 3;
        } else {
          insValue = 4;
          this.$refs[refName].checkboxList = value.split(",");
        }
      } else if (name == "day") {
        if (value === "*") {
          insValue = 1;
        } else if (value == "?") {
          insValue = 2;
        } else if (value.indexOf("-") > -1) {
          let indexArr = value.split("-");
          isNaN(indexArr[0])
            ? (this.$refs[refName].cycle01 = 0)
            : (this.$refs[refName].cycle01 = indexArr[0]);
          this.$refs[refName].cycle02 = indexArr[1];
          insValue = 3;
        } else if (value.indexOf("/") > -1) {
          let indexArr = value.split("/");
          isNaN(indexArr[0])
            ? (this.$refs[refName].average01 = 0)
            : (this.$refs[refName].average01 = indexArr[0]);
          this.$refs[refName].average02 = indexArr[1];
          insValue = 4;
        } else if (value.indexOf("W") > -1) {
          let indexArr = value.split("W");
          isNaN(indexArr[0])
            ? (this.$refs[refName].workday = 0)
            : (this.$refs[refName].workday = indexArr[0]);
          insValue = 5;
        } else if (value === "L") {
          insValue = 6;
        } else {
          this.$refs[refName].checkboxList = value.split(",");
          insValue = 7;
        }
      } else if (name == "week") {
        if (value === "*") {
          insValue = 1;
        } else if (value == "?") {
          insValue = 2;
        } else if (value.indexOf("-") > -1) {
          let indexArr = value.split("-");
          isNaN(indexArr[0])
            ? (this.$refs[refName].cycle01 = 0)
            : (this.$refs[refName].cycle01 = indexArr[0]);
          this.$refs[refName].cycle02 = indexArr[1];
          insValue = 3;
        } else if (value.indexOf("#") > -1) {
          let indexArr = value.split("#");
          isNaN(indexArr[0])
            ? (this.$refs[refName].average01 = 1)
            : (this.$refs[refName].average01 = indexArr[0]);
          this.$refs[refName].average02 = indexArr[1];
          insValue = 4;
        } else if (value.indexOf("L") > -1) {
          let indexArr = value.split("L");
          isNaN(indexArr[0])
            ? (this.$refs[refName].weekday = 1)
            : (this.$refs[refName].weekday = indexArr[0]);
          insValue = 5;
        } else {
          this.$refs[refName].checkboxList = value.split(",");
          insValue = 7;
        }
      }
      this.$refs[refName].radioValue = insValue;
    },

    // 表单选项的子组件校验数字格式（通过-props传递）
    checkNumber(value, minLimit, maxLimit) {
      // 检查必须为整数
      value = Math.floor(value);
      if (value < minLimit) {
        value = minLimit;
      } else if (value > maxLimit) {
        value = maxLimit;
      }
      return value;
    },
  },
};
</script>

<style lang="scss" scoped>
.popup-main {
  position: relative;
  margin: 10px auto;
  background: #fff;
  border-radius: 5px;
  font-size: 12px;
  overflow: hidden;
}
.popup-title {
  overflow: hidden;
  line-height: 34px;
  padding-top: 6px;
  background: #f2f2f2;
}
.popup-result {
  box-sizing: border-box;
  line-height: 24px;
  margin: 25px auto;
  padding: 15px 10px 10px;
  border: 1px solid #ccc;
  position: relative;
}
.popup-result .title {
  position: absolute;
  top: -15px;
  left: 50%;
  width: 140px;
  font-size: 14px;
  margin-left: -70px;
  text-align: center;
  line-height: 30px;
  background: #fff;
}
.popup-result table {
  text-align: center;
  width: 100%;
  margin: 0 auto;
}
.popup-result table span {
  display: block;
  width: 100%;
  font-family: arial;
  line-height: 30px;
  height: 30px;
  white-space: nowrap;
  overflow: hidden;
  border: 1px solid #e8e8e8;
}
</style>