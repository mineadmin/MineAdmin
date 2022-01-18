<template>
  <el-main>
    <el-row :gutter="15">
      <el-col :lg="8">
        <el-card shadow="never">
          <div class="user-info">
            <div class="user-info-top">

              <sc-upload
                v-model="avatar"
                title="上传头像"
                :cropper="true"
                :compress="1"
                :aspectRatio="1/1"
                @success="uploadSuccess"
               />

              <h2>{{ userInfo.user.nickname||'-' }}</h2>
              <el-button type="primary" round icon="el-icon-collection-tag" size="medium">{{ userInfo.user.username }}</el-button>
            </div>
            <div class="user-info-main">
              <ul>
                <li><label><el-icon><el-icon-message /></el-icon></label><span>{{ userInfo.user.email }}</span></li>
                <li><label><el-icon><el-icon-present /></el-icon></label><span> {{ userInfo.user.user_type == '100' ? '系统用户' : '其他类型' }}</span></li>
                <li><label><el-icon><el-icon-phone /></el-icon></label><span>{{ userInfo.user.phone }}</span></li>
                <li><label><el-icon><el-icon-coin /></el-icon></label><span>{{ userInfo.roles[0] }}</span></li>
              </ul>
            </div>
            <div class="user-info-bottom">
              <h2>{{ $t('usercenter.signed') }}</h2>
              <el-space wrap>
                {{ userInfo.user.signed == null || userInfo.user.signed == '' ? '这家伙很懒，什么都没有留下。' : userInfo.user.signed }}
              </el-space>
            </div>
          </div>
        </el-card>
      </el-col>
      <el-col :lg="16">
        <el-card shadow="never">
          <el-tabs tab-position="top">
            <el-tab-pane :label="$t('usercenter.baseInfo')">
              <el-form ref="formUser" :model="formUser" label-width="80px" style="width: 600px; margin-top:20px;">

                <el-form-item :label="$t('usercenter.username')">
                  <el-input v-model="formUser.username" disabled></el-input>
                  <div class="el-form-item-msg">{{ $t('usercenter.usernameMsg') }}</div>
                </el-form-item>

                <el-form-item :label="$t('usercenter.nickname')">
                  <el-input v-model="formUser.nickname"></el-input>
                </el-form-item>

                <el-form-item :label="$t('usercenter.phone')">
                  <el-input v-model="formUser.phone"></el-input>
                </el-form-item>

                <el-form-item :label="$t('usercenter.email')">
                  <el-input v-model="formUser.email"></el-input>
                </el-form-item>

                <el-form-item :label="$t('usercenter.signed')">
                  <el-input
                    v-model="formUser.signed"
                    type="textarea"
                    :rows="3"
                    maxlength="255"
                    show-word-limit
                  />
                </el-form-item>

                <el-form-item>
                  <el-button type="primary" @click="updateInfo" :loading="infoLoading">{{ $t('sys.save') }}</el-button>
                </el-form-item>

              </el-form>
            </el-tab-pane>
            <el-tab-pane :label="$t('usercenter.modifyPassword')">
              <el-form
                ref="formPassword"
                :rules="passwordRule"
                :model="formPassword"
                :label-width="config.lang === 'en' ? '150px' : '80px'"
                style="width:600px;margin-top:20px;"
              >
                <el-form-item :label="$t('usercenter.oldPassword')" prop="oldPassword">
                  <el-input v-model="formPassword.oldPassword" type="password" clearable show-password></el-input>
                </el-form-item>

                <el-form-item :label="$t('usercenter.newPassword')" prop="newPassword">
                  <el-input v-model="formPassword.newPassword"></el-input>
                </el-form-item>

                <el-form-item :label="$t('usercenter.newPassword_confirmation')" prop="newPassword_confirmation">
                  <el-input v-model="formPassword.newPassword_confirmation "></el-input>
                </el-form-item>

                <el-form-item>
                  <el-button type="primary" @click="modifyPassword" :loading="passwordLoading">{{ $t('sys.save') }}</el-button>
                </el-form-item>

              </el-form>
            </el-tab-pane>
            <el-tab-pane :label="$t('usercenter.settings')">
							<el-form ref="formSetting" :model="formSetting" label-width="120px" style="margin-top:20px;">
								<el-form-item :label="$t('usercenter.nightmode')">
									<el-switch v-model="config.theme" active-value="dark" inactive-value="default"></el-switch>
								</el-form-item>
								<el-form-item :label="$t('usercenter.colorPrimary')" style="margin-bottom: 6px;">
									<el-color-picker v-model="config.colorPrimary" :predefine="colorList">></el-color-picker>
								</el-form-item>
								<el-form-item :label="$t('usercenter.language')">
									<el-select v-model="config.lang">
										<el-option label="简体中文" value="zh_CN"></el-option>
										<el-option label="English" value="en"></el-option>
									</el-select>
								</el-form-item>
                <el-form-item :label="$t('usercenter.layout')">
                  <el-select v-model="config.layout" placeholder="请选择">
                    <el-option label="默认" value="default"></el-option>
                    <el-option label="通栏" value="header"></el-option>
                    <el-option label="经典" value="menu"></el-option>
                    <el-option label="功能坞" value="dock"></el-option>
                  </el-select>
                </el-form-item>
                <el-form-item :label="$t('usercenter.layoutTags')">
                  <el-switch v-model="config.layoutTags" :active-value="true" :inactive-value="false"></el-switch>
                </el-form-item>

                <el-form-item>
                  <el-button type="primary" @click="saveSetting" :loading="settingLoading">{{ $t('sys.save') }}</el-button>
                </el-form-item>
							</el-form>
						</el-tab-pane>
          </el-tabs>
        </el-card>
      </el-col>
    </el-row>
  </el-main>
</template>

<script>
import colorTool from '@/utils/color'
export default {
  name: 'userCenter',
  data() {
    return {

      uploadShow: false,

      infoLoading: false,
      passwordLoading: false,
      settingLoading: false,
      avatarLoading: false,

      formUser: {
        id: '',
        username: '',
        nickname: '',
        signed: '',
        phone: '',
        email: ''
      },

      avatar: '',

      formPassword: {
        'oldPassword': '',
        'newPassword': '',
        'newPassword_confirmation ' : '',
      },

      formSetting: {},

      passwordRule: {
        oldPassword: [{
            required: true,
            message: this.$t('sys.pleaseInput') +  this.$t('usercenter.oldPassword'),
            trigger: 'blur'
        }],
        newPassword: [{
            required: true,
            message:  this.$t('sys.pleaseInput') +  this.$t('usercenter.newPassword'),
            trigger: 'blur'
        }],
        newPassword_confirmation: [{
          required: true,
          message:  this.$t('sys.pleaseInput') +  this.$t('usercenter.newPassword_confirmation'),
          trigger: 'blur'
        }]
      },

      userInfo: null,

      colorList: ['#0960bd', '#409EFF', '#009688', '#536dfe', '#ff5c93', '#c62f2f', '#fd726d'],
      config: {
          lang: this.$TOOL.data.get('APP_LANG') || this.$CONFIG.LANG,
          theme: this.$TOOL.data.get('APP_THEME') || 'default',
          colorPrimary: this.$TOOL.data.get('APP_COLOR') || this.$CONFIG.COLOR || '#409EFF',
          layout: this.$TOOL.data.get('APP_LAYOUT') || this.$store.state.global.layout,
          layoutTags: this.$store.state.global.layoutTags
      }
    }
  },

  created () {
    this.userInfo = this.$TOOL.data.get('user')
    this.formUser = this.userInfo.user
    this.avatar =  this.userInfo.user.avatar ? this.userInfo.user.avatar : '/img/avatar.jpg'
  },

  watch:{
    'config.layout'(val) {
			this.$store.commit("SET_layout", val)
      this.$TOOL.data.set('APP_LAYOUT', val)
		},
    'config.layoutTags'() {
      this.$store.commit("TOGGLE_layoutTags")
		},
    'config.theme'(val){
      document.body.setAttribute('data-theme', val)
      this.$TOOL.data.set("APP_THEME", val);
    },
    'config.lang'(val){
      this.$i18n.locale = val
      this.$TOOL.data.set("APP_LANG", val);
    },
    'config.colorPrimary'(val){
      document.documentElement.style.setProperty('--el-color-primary', val);
      for (let i = 1; i <= 9; i++) {
        document.documentElement.style.setProperty(`--el-color-primary-light-${i}`, colorTool.lighten(val,i/10));
      }
      document.documentElement.style.setProperty(`--el-color-primary-darken-1`, colorTool.darken(val,0.1));
      this.$TOOL.data.set("APP_COLOR", val);
    }
  },

	//路由跳转进来 判断from是否有特殊标识做特殊处理
	beforeRouteEnter (to, from, next){
    next((vm)=>{
      if(from.is){
        //删除特殊标识，防止标签刷新重复执行
        delete from.is
        //执行特殊方法
        vm.$alert('路由跳转过来后含有特殊标识，做特殊处理', '提示', {
          type: 'success',
          center: true
        }).then(() => {}).catch(() => {})
      }
    })
  },

  methods: {

    // 更新用户资料
    updateInfo() {
      this.$refs.formUser.validate( async (valid) => {
        if (valid) {
          this.formUser.avatar = undefined
          this.formUser.backend_setting = undefined
          this.infoLoading = true
          let res = await this.$API.user.updateInfo(this.formUser)
          this.infoLoading = false

          if ( res.success ) {
            this.$TOOL.data.set('user', this.userInfo)
            this.$message.success(res.message)
          }else{
            this.$alert(res.message, "提示", { type: 'error' })
          }

        }
      })
    },

    // 上传头像
    async uploadSuccess(res) {
      if (res.url) {
        let data = {
          id: this.formUser.id,
          avatar: this.viewImage(res.url)
        }
        this.avatar = this.viewImage(res.url)
        await this.$API.user.updateInfo(data).then(res => {
            this.$nextTick(() => {
              this.$TOOL.data.set('user', this.userInfo)
              this.$message.success(res.message)
            })
        })
      }
    },

    // 修改密码
    modifyPassword() {
      this.$refs.formPassword.validate( async (valid) => {
        if (valid) {
          this.passwordLoading = true
          let res = await this.$API.user.modifyPassword(this.formPassword)
          this.passwordLoading = false

          if (res.success) {
            this.$message.success(res.message)
          }else{
            this.$alert(res.message, "提示", { type: 'error' })
          }

        }
      })
    },

    // 保存个人设置
    async saveSetting () {
      this.formSetting = {
        id: this.formUser.id,
        backend_setting: this.config
      }
      this.settingLoading = true
      let res = await this.$API.user.updateInfo(this.formSetting)
      this.settingLoading = false

      if ( res.success ) {
        this.$TOOL.data.set('user', this.userInfo)
        this.$message.success(res.message)
      }else{
        this.$alert(res.message, "提示", { type: 'error' })
      }
    }

  }
}
</script>

<style scoped>
  .el-card {margin-bottom:15px;}
  .activity-item {font-size: 13px;color: #999;display: flex;align-items: center;}
  .activity-item label {color: #333;margin-right:10px;}
  .activity-item .el-avatar {margin-right:10px;}
  .activity-item .el-tag {margin-right:10px;}

  [data-theme='dark'] .user-info-bottom {border-color: var(--el-border-color-base);}

  [data-theme='dark'] .activity-item label {color: #999;}


  .user-avatar {
    position: relative;
    display: inline-block;
    border-radius: 50%;
    line-height: 110px;
    height: 160px;
    width: 160px;
  }

  .user-avatar:hover:after {
    content: '+';
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    color: #eee;
    background: rgba(0, 0, 0, 0.5);
    font-size: 48px;
    font-style: normal;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    cursor: pointer;
    border-radius: 50%;
    line-height: 150px;
  }
  .user-avatar > img {
    width: 160px;
    border-radius: 50%;
  }
</style>
