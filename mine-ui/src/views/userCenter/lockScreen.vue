<template>
  <div
    :class="{ onLogin: goLogin }"
    class="lockscreen"
  >
    <div class="lock" v-if="! goLogin">
      <div class="lock-box">
        <div class="lock" @click="handleGoLogin">
          <span class="lock-icon" title="解锁屏幕">
            <el-icon><el-icon-unlock /></el-icon>
          </span>
        </div>
      </div>
      
      <recharge />

      <div class="local-time">
        <div class="time">{{ date }}</div>
        <div class="date">{{ year }}年{{ month }}月{{ day }}号，星期{{ week }}</div>
      </div>
    </div>

    <div class="login" v-if="goLogin">
      <el-avatar
        class="avatar"
        shape="square"
        :size="120"
        fit="cover"
        :src="$TOOL.data.get('user').user.avatar"
        @error="() => true"
      >
      <el-icon><ma-icon-user style="font-size: 120px; margin-left:-53px" /></el-icon>
      </el-avatar>
      <div class="username"> {{ $TOOL.data.get('user').user.username }} </div>

      <div class="input">
        <el-input
          size="large"
          type="password"
          v-model="unlockPassword"
          placeholder="请输入锁屏密码"
          @keydown.enter="unlock"
        >
          <template #append>
            <el-button @click="unlock">进入</el-button>
          </template>
        </el-input>

        <div style="text-align:center">
          <el-button type="text" size="large" @click="goLogin = false"><span style="color: #fff">关闭</span></el-button>
        </div>

      </div>
    </div>
  </div>
</template>
<script>
import dayjs from 'dayjs'
import recharge from './components/recharge'
export default {
    name: 'lockScreen',
    components: {
      recharge,
    },
    data () {
      return {
        goLogin: false,
        unlockPassword: '',
        date: dayjs().format('HH:mm:ss'),
        resTimeout: null
      }
    },

    computed: {
      year: () => {
        return dayjs().year()
      },
      week: () => {
        return '日一二三四五六'.charAt(dayjs().day())
      },
      day: () => {
        return dayjs().date()
      },
      month: () => {
        return dayjs().month() + 1
      }
    },

    methods: {

      handleGoLogin() {
        this.goLogin = true

        // 5分钟没动静则返回锁屏界面
        this.resTimeout = setTimeout( () => {
          if (this.goLogin) {
            this.goLogin = false
            this.$message.info('长时间无操作，返回锁屏界面')
          }
        }, 3 * 100 * 1000)
      },

      unlock () {
        if (this.unlockPassword === '') {
          this.$message.error('请输入锁屏密码')
          return
        }
        let password = this.$TOOL.crypto.MD5(this.unlockPassword)
        if (this.$TOOL.data.get('lockPassword') && password !== this.$TOOL.data.get('lockPassword')) {
          this.$message.error('锁屏密码错误')
          return
        }
        clearTimeout(this.resTimeout)
        this.$TOOL.data.remove('lockPassword')
        this.$TOOL.data.remove('lockScreen')
        this.$router.push('/dashboard')
      }
    },

    created() {
      setInterval(() => {
        this.date = dayjs().format('HH:mm:ss')
      }, 1000)
    }
}
</script>
<style scoped lang="scss">
.login .el-icon {
  width: 100%; margin-left: 50px;
}
.lockscreen {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    background: #000;
    color: white;
    overflow: hidden;
    z-index: 300;

    &.onLogin {
      background-color: rgba(37, 37, 37, 0.88);
      backdrop-filter: blur(7px);
    }

    .login {
      z-index: 400;
      display: flex;
      width: 100%;
      flex-direction: column;
      align-items: center;

      .avatar {
        margin-top: 25vh;
      }

      .username {
        font-size: 26px; line-height: 30px; margin-top: 15px;
      }
      .input {
        width: 300px; margin-top: 10px;
      }
    }

    .login-box {
      position: absolute;
      top: 45%;
      left: 50%;
      transform: translate(-50%, -50%);
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;

      > * {
        margin-bottom: 14px;
      }
    }

    .lock-box {
      position: absolute;
      top: 20px;
      left: 50%;
      transform: translateX(-50%);
      font-size: 56px;
      z-index: 100;

      .tips {
        color: white;
        cursor: text;
      }

      .lock {
        display: flex;
        justify-content: center;

        .lock-icon {
          cursor: pointer;
        }
      }
    }

    .local-time {
      position: absolute;
      bottom: 60px;
      left: 60px;
      font-family: helvetica;

      .time {
        font-size: 70px;
      }

      .date {
        font-size: 40px;
      }
    }
  }
</style>