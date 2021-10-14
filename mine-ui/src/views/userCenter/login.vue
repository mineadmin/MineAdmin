<template>
	<div class="container">
		<div class="login_container">
			<div class="login_body">
				<div class="login-form">
					<div class="login-logo">
						<img class="logo" :alt="appName" src="img/logo.png">
						<h2>MineAdmin</h2>
					</div>
					<el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="0" size="large">
						<el-form-item prop="user">
							<el-input 
								v-model="ruleForm.user"
								prefix-icon="el-icon-user"
								clearable placeholder="请输入用户名"
								@keyup.enter="submitForm()"
							></el-input>
						</el-form-item>
						<el-form-item prop="password">
							<el-input
								v-model="ruleForm.password"
								prefix-icon="el-icon-lock"
								clearable show-password placeholder="请输入密码"
								@keyup.enter="submitForm()"
							></el-input>
						</el-form-item>
						<el-form-item prop="code">
							<el-input
								type="text"
								v-model="ruleForm.code"
								clearable
								prefix-icon="el-icon-camera"
								placeholder="验证码"
								@keyup.enter="submitForm()"
							>
								<template #append>
									<img class="login-code" :src="captchaImg" @click="getCaptchaImg">
								</template>
							</el-input>
						</el-form-item>
						<!-- <el-form-item style="margin-bottom: 10px;">
							<el-row>
								<el-col :span="12">
									<el-checkbox label="记住我" v-model="ruleForm.autologin"></el-checkbox>
								</el-col>
							</el-row>
						</el-form-item> -->
						<el-form-item>
							<el-button type="primary" style="width: 100%;" :loading="islogin" @click="submitForm">登 录</el-button>
						</el-form-item>
					</el-form>
				</div>
			</div>
		</div>
		<div class="login-footer">Copyright © 2021-2022 mineadmin.com All Rights Reserved.</div>

		<!-- <ul class="circles">
			<li v-for="n in 10" :key="n"></li>
		</ul> -->
	</div>
</template>

<script>

	export default {
		data() {
			return {
				appName: this.$CONFIG.APP_NAME,
				appVar: this.$CONFIG.APP_VER,
				appUrl: this.$CONFIG.APP_URL,
				ruleForm: {
					user: "superAdmin",
					password: "admin123",
					code: '',
					autologin: false
				},
				rules: {
					user: [
						{required: true, message: '请输入用户名', trigger: 'blur'}
					],
					password: [
						{required: true, message: '请输入密码', trigger: 'blur'}
					],
					code: [
						{required: true, message: '请输入验证码', trigger: 'blur'}
					]
				},
				captchaImg: null,
				islogin: false
			}
		},
		created: function() {
			this.$TOOL.data.remove("user")
			this.$TOOL.data.remove("grid")
			this.$store.commit("clearViewTags")
			this.$store.commit("clearKeepLive")
			this.$store.commit("clearIframeList")
			this.getCaptchaImg()
		},
		methods: {
			Login() {
				this.islogin = true
				this.$API.login.Login({
					username: this.ruleForm.user,
					password: this.ruleForm.password,
					code: this.ruleForm.code
				}).then(res => {
					if (res.success) {
						this.$TOOL.data.set('token', res.data.token)
						this.$router.push(this.$route.query.redirect || '/')
						this.$notify({
							title: '提示',
							message: '登录成功',
							type: 'success'
						})
					} else {
						this.getCaptchaImg()
					}
				}).catch(() => this.getCaptchaImg())
				this.islogin = false
			},

			submitForm() {
				this.$refs['ruleForm'].validate((valid) => {
					if (valid) {
						this.Login()
					}else{
						console.log('error submit!!');
						return false;
					}
				})
			},

			getCaptchaImg () {
				this.$API.login.getCaptch().then(res => {
					this.captchaImg = res.data.img
				})
			},
		}
	}
</script>

<style scoped lang="scss">
	.container {
		position: absolute; z-index:2;
		width: 100%; height: 100%;
		background-size: cover;
		background-image: url(/img/login@bg.jpg);
	}
	.login_container {
		position: absolute;top:50%;left:50%; width: 400px; margin: 0 auto;z-index: 1;transform: translate(-50%, -50%);
	}
	.login_body {width: 100%;display: flex; margin:0 auto;box-shadow: 0px 20px 80px 0px rgba(0,0,0,0.3);}

	.login-logo {text-align: center;margin-bottom: 30px;}
	.login-logo .logo {width: 70px;height: 70px;vertical-align: bottom;}
	.login-logo h2 {font-size: 24px;color: #40485b;}

	.login-title {margin-top: 20px;}
	.login-title h2 {font-size: 22px;font-weight: normal;}
	.login-title p {font-size: 12px;margin-top:40px;line-height: 1.8;color: rgba(255,255,255,0.8);}

	.login-form {width: 100%;padding: 15px 30px; border-radius: 5px; margin: 0 auto; background-color: #fff; background: hsla(0,0%,100%,.8);}
	.login-oauth {display: flex;justify-content:space-around;}
	.login-form .el-divider {margin-top:40px;}

	.login-footer {
		text-align: center;color: #fff; position: fixed;
		bottom: 20px; width: 100%;
		font-family: Arial; letter-spacing: 1px;
	}
	.login-code {
      height: 40px - 2px;
      display: block;
      margin: 0px -20px;
      border-top-right-radius: 2px;
      border-bottom-right-radius: 2px;
    }

	.demo-user-item {display: flex;align-items: center;line-height: 1;padding:10px 0;}
	.demo-user-item .icon {margin-right: 20px;}
	.demo-user-item .info h2 {font-size: 14px;}
	.demo-user-item .info p {color: #666;margin-top: 6px;}

	@media (max-height: 650px){
		.login_container {position: static; transform: none;margin:50px auto;}
	}
	@media (max-width: 1200px){
		.login-form {padding:15px 30px;}
	}
	@media (max-width: 1000px){
		.login_container {margin: 0 auto;transform:none;top:20%;bottom:0px;left:0px;right: 0px;}
		.login_body {box-shadow: none;}
		.login-form {width:95%;padding:15px 20px;}
		.login-footer {margin-top: 0; font-family: Arial;}
	}
	@media (max-width: 380px){
		.login_container {width: 360px;margin: 0 auto;transform:none;top:20%;bottom:0px;left:0px;right: 0px;}
		.login_body {box-shadow: none;}
		.login-form {width:100%;padding:15px 20px;}
		.login-footer {margin-top: 0; font-family: Arial;}
	}
	
	.circles {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		overflow: hidden;
		margin: 0px;
		padding: 0px;
		li {
		position: absolute;
		display: block;
		list-style: none;
		width: 20px;
		height: 20px;
		background: #FFF;
		animation: animate 25s linear infinite;
		bottom: -200px;
		@keyframes animate {
			0%{
			transform: translateY(0) rotate(0deg);
			opacity: 1;
			border-radius: 0;
			}
			100%{
			transform: translateY(-1000px) rotate(720deg);
			opacity: 0;
			border-radius: 50%;
			}
		}
		&:nth-child(1) {
			left: 15%;
			width: 80px;
			height: 80px;
			animation-delay: 0s;
		}
		&:nth-child(2) {
			left: 5%;
			width: 20px;
			height: 20px;
			animation-delay: 2s;
			animation-duration: 12s;
		}
		&:nth-child(3) {
			left: 70%;
			width: 20px;
			height: 20px;
			animation-delay: 4s;
		}
		&:nth-child(4) {
			left: 40%;
			width: 60px;
			height: 60px;
			animation-delay: 0s;
			animation-duration: 18s;
		}
		&:nth-child(5) {
			left: 65%;
			width: 20px;
			height: 20px;
			animation-delay: 0s;
		}
		&:nth-child(6) {
			left: 75%;
			width: 150px;
			height: 150px;
			animation-delay: 3s;
		}
		&:nth-child(7) {
			left: 35%;
			width: 200px;
			height: 200px;
			animation-delay: 7s;
		}
		&:nth-child(8) {
			left: 50%;
			width: 25px;
			height: 25px;
			animation-delay: 15s;
			animation-duration: 45s;
		}
		&:nth-child(9) {
			left: 20%;
			width: 15px;
			height: 15px;
			animation-delay: 2s;
			animation-duration: 35s;
		}
		&:nth-child(10) {
			left: 85%;
			width: 150px;
			height: 150px;
			animation-delay: 0s;
			animation-duration: 11s;
		}
		}
	}
</style>
