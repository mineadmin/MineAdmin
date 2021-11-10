<template>
	<div class="user-bar">
		<div class="screen panel-item hidden-sm-and-down" @click="lockScreen">
			<i class="el-icon-lock"></i>
		</div>
		<div class="screen panel-item hidden-sm-and-down" @click="screen">
			<i class="el-icon-full-screen"></i>
		</div>
		<div class="msg panel-item" @click="showMsg">
			<el-badge :hidden="msgList.length==0" :value="msgList.length" class="badge" type="danger">
				<i class="el-icon-chat-dot-round"></i>
			</el-badge>
			<el-drawer title="新消息" v-model="msg" :size="400" append-to-body destroy-on-close>
				<el-container>
					<el-main class="nopadding">
						<el-scrollbar>
							<ul class="msg-list">
								<li v-for="item in msgList" v-bind:key="item.id">
									<a :href="item.link" target="_blank">
										<div class="msg-list__icon">
											<el-badge is-dot type="danger">
												<el-avatar :size="40" :src="item.avatar" class="avatar"></el-avatar>
											</el-badge>
										</div>
										<div class="msg-list__main">
											<h2>{{item.title}}</h2>
											<p>{{item.describe}}</p>
										</div>
										<div class="msg-list__time">
											<p>{{item.time}}</p>
										</div>
									</a>
								</li>
								<el-empty v-if="msgList.length==0" description="暂无新消息" :image-size="100"></el-empty>
							</ul>
						</el-scrollbar>
					</el-main>
					<el-footer>
						<el-button type="primary" size="small">消息中心</el-button>
						<el-button size="small" @click="markRead">全部设为已读</el-button>
					</el-footer>
				</el-container>
			</el-drawer>
		</div>
		<el-dropdown trigger="click" @command="handleUser" style="height: 100%;">
			<div class="user panel-item">
				<el-avatar :size="30" :src="avatar">{{ userNameF }}</el-avatar>
				<label>{{ userName }}<i class="el-icon-arrow-down el-icon--right"></i></label>
			</div>
			<template #dropdown>
				<el-dropdown-menu>
					<el-dropdown-item command="uc">个人设置</el-dropdown-item>
					<el-dropdown-item command="clearSelfCache">清除缓存</el-dropdown-item>
					<el-dropdown-item divided command="outLogin">退出登录</el-dropdown-item>
				</el-dropdown-menu>
			</template>
		</el-dropdown>
	</div>
</template>

<script>
	export default {
		data(){
			return {
				userName: "",
				userNameF: "",
				avatar: 'img/avatar.jpg',
				msg: false,
				msgList: [
					{
						id: 1,
						type: 'user',
						avatar: "img/avatar.jpg",
						title: "superAdmin",
						describe: "如果喜欢就点个星星支持一下哦",
						link: "https://gitee.com/xmo/MineAdmin",
						time: "5分钟前"
					},
					{
						id: 3,
						type: 'system',
						avatar: "img/avatar.jpg",
						title: "感谢登录MineAdmin",
						describe: "Swoole + Hyperf + Vue 3.0 + Vue-Router 4.0 + ElementPlus + Axios 后台管理系统。",
						link: "https://gitee.com/xmo/MineAdmin",
						time: "2020年8月1日"
					}
				]
			}
		},
		created() {
			let userInfo = this.$TOOL.data.get('user').user;
			this.userName = userInfo.username;
			this.userNameF = this.userName.substring(0,1);
			this.avatar = userInfo.avatar
			if (this.avatar == '' || this.avatar == null) {
				this.avatar = 'img/avatar.jpg'
			}
		},
		methods: {
			//个人信息
			handleUser(command) {
				if(command == "uc"){
					this.$router.push({path: '/usercenter'});
				}
				if(command == "clearSelfCache"){
					this.$API.user.clearSelfCache().then(res => {
						this.$message.success('缓存清除完毕')
					})
				}
				if(command == "outLogin"){
					this.$confirm('确认是否退出当前用户？','提示', {
						type: 'warning',
						confirmButtonText: '退出',
						confirmButtonClass: 'el-button--danger'
					}).then(async () => {
						await this.$API.login.Logout().then(res => {
							if (res.success) {
								this.$store.commit('SET_ROUTERS', undefined)
								this.$TOOL.data.clear()
								this.$router.replace({path: '/login'})
							}
						})
					}).catch(() => {
						//取消退出
					})
				}
			},
			//全屏
			screen(){
				var element = document.documentElement;
				this.$TOOL.screen(element)
			},
			//显示短消息
			showMsg(){
				this.msg = true
			},
			//标记已读
			markRead(){
				this.msgList = []
			},
			// 锁屏
			lockScreen () {
				this.$prompt('请输入锁屏密码，解锁需要此密码', '设置锁屏密码', {
					confirmButtonText: '确定',
					cancelButtonText: '取消',
					inputPattern: /^[A-Za-z0-9_]+$/,
					inputErrorMessage: '密码只能是字母、数字和下划线'
				}).then(({ value }) => {
					this.$TOOL.data.set('lockPassword', this.$TOOL.crypto.MD5(value))
        			this.$TOOL.data.set('lockScreen', true)
					this.$router.push('/lockScreen')
				})
			},
		}
	}
</script>

<style scoped>
	.user-bar {display: flex;align-items: center;height: 100%;}
	.user-bar .panel-item {padding: 0 10px;cursor: pointer;height: 100%;display: flex;align-items: center;}
	.user-bar .panel-item i {font-size: 16px;}
	.user-bar .panel-item:hover {background: rgba(0, 0, 0, 0.1);}
	.user-bar .user {display: flex;align-items: center;}
	.user-bar .user label {display: inline-block;margin-left:5px;font-size: 12px;cursor:pointer;}

	.msg-list li {border-top:1px solid #eee;}
	.msg-list li a {display: flex;padding:20px;}
	.msg-list li a:hover {background: #ecf5ff;}
	.msg-list__icon {width: 40px;margin-right: 15px;}
	.msg-list__main {flex: 1;}
	.msg-list__main h2 {font-size: 15px;font-weight: normal;color: #333;}
	.msg-list__main p {font-size: 12px;color: #999;line-height: 1.8;margin-top: 5px;}
	.msg-list__time {width: 100px;text-align: right;color: #999;}
	:deep(.el-avatar--circle) {
		display: flex;
		justify-content: center;
		align-items: center;
	}

	[data-theme='dark'] .msg-list__main h2 {color: #d0d0d0;}
	[data-theme='dark'] .msg-list li {border-top:1px solid #363636;}
	[data-theme='dark'] .msg-list li a:hover {background: #383838;}
</style>
