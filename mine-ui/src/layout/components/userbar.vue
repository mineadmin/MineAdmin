<template>
	<div class="user-bar">
		<div class="screen panel-item hidden-sm-and-down" @click="lockScreen">
			<el-icon><el-icon-lock /></el-icon>
		</div>
		<div class="screen panel-item hidden-sm-and-down" @click="goDoc">
			<el-icon><sc-icon-file-word /></el-icon>
		</div>
		<div class="screen panel-item hidden-sm-and-down" @click="screen">
			<el-icon><el-icon-full-screen /></el-icon>
		</div>
		<div class="msg panel-item" @click="showMsg">
			<el-badge :hidden="msgList.length==0" :value="msgList.length" class="badge" type="danger">
				<el-icon><el-icon-chat-dot-round /></el-icon>
			</el-badge>
			<el-drawer title="新消息" v-model="msg" :size="400" append-to-body destroy-on-close>
				<el-container>
					<el-main class="nopadding">
						<el-scrollbar>
							<ul class="msg-list">
								<li v-for="item in msgList" v-bind:key="item.id" @click="showDetails(item)">
									<a :href="item.link" target="_blank">
										<div class="msg-list__icon">
											<el-avatar :size="40" :src="avatar ? avatar : '/img/avatar.jpg'" class="avatar"></el-avatar>
										</div>
										<div class="msg-list__main">
											<h2>{{item.title}}</h2>
											<p>发送人：{{ item.send_user.nickname }}，时间：{{ item.created_at }}</p>
										</div>
										<div class="msg-list__time">
											<p></p>
										</div>
									</a>
								</li>
								<el-empty v-if="msgList.length==0" description="暂无新消息" :image-size="100"></el-empty>
							</ul>
						</el-scrollbar>
					</el-main>
					<el-footer>
						<el-button type="primary" @click="()=>{$router.push('message'); msg= false;}" size="small">消息中心</el-button>
						<el-button size="small" @click="markRead">全部设为已读</el-button>
					</el-footer>
				</el-container>
			</el-drawer>
			<el-drawer v-model="drawer" title="详细内容" size="50%" >
				<el-main v-loading="drawerLoading" element-loading-background="rgba(50, 50, 50, 0.5)"
					element-loading-text="数据加载中..." style="height:100%;"
				>
				<h2 style="font-size: 24px; line-height: 60px; text-align:center"> {{ row.title }} </h2>
				<div v-html="row.content"></div>
				</el-main>
			</el-drawer>
		</div>
		<el-dropdown class="user panel-item" trigger="click" @command="handleUser">
			<div class="user-avatar">
				<el-avatar :size="30" :src="avatar">{{ userNameF }}</el-avatar>
				<label>{{ userName }}</label>
				<el-icon class="el-icon--right"><el-icon-arrow-down /></el-icon>
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
	import Message from '@/ws-serve/message'
	// import { union, xor, difference } from 'lodash'
	import { ElNotification } from 'element-plus'
	export default {
		data(){
			return {
				userName: "",
				userNameF: "",
				avatar: this.$TOOL.data.get('user').avatar,
				msg: false,
				msgList:[],
				wsMessage: null,
				drawer: false,
				drawerLoading: false,
				row: {}
			}
		},
		created() {
			let userInfo = this.$TOOL.data.get('user').user
			this.userName = userInfo.username;
			this.userNameF = this.userName.substring(0, 1)

			this.avatar = userInfo.avatar ? userInfo.avatar : '/img/avatar.jpg'

			this.wsMessage = new Message()
			this.wsMessage.connection()
			this.wsMessage.getMessage()

			this.wsMessage.ws.on('ev_new_message', (msg, data) => {
				if (data.length > this.msgList.length) {
					ElNotification.success({
						title: '新消息提示',
						message: "您有新的消息，请注意查收！",
						onClick: () => {
							this.msg = true
						}
					})
				}
				this.msgList = data
			})
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
								this.wsMessage.ws.close()
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
			// 跳转文档
			goDoc() {
				this.$TOOL.data.set('apiAuth', false)
				this.$router.push({ name: 'doc' })
			},
			// 全部设置已读
			markRead() {
				let ids = this.msgList.map(item => item.id)
				if (ids.length > 0) {
					this.$API.queueMessage.updateReadStatus(ids.join(',')).then(res => {
						if (res.success) {
							this.msgList = []
							this.$message.success(res.message)
						}
					})
				}
			},
			async showDetails(row) {
				this.drawerLoading = true
				this.drawer = true
				await this.$API.queueMessage.updateReadStatus(row.id)
				this.drawerLoading = false 
				this.row = row
			},
		}
	}
</script>

<style scoped>
	.user-bar {display: flex;align-items: center;height: 100%;}
	.user-bar .panel-item {padding: 0 10px;cursor: pointer;height: 100%;display: flex;align-items: center;}
	.user-bar .panel-item i {font-size: 16px;}
	.user-bar .panel-item:hover {background: rgba(0, 0, 0, 0.1);}
	.user-bar .user-avatar {height:49px;display: flex;align-items: center;}
	.user-bar .user-avatar label {display: inline-block;margin-left:5px;font-size: 12px;cursor:pointer;}

	.msg-list li {border-top:1px solid #eee; cursor: pointer;}
	.msg-list li a {display: flex;padding:20px;}
	.msg-list li a:hover {background: #ecf5ff;}
	.msg-list__icon {width: 40px;margin-right: 15px;}
	.msg-list__main {flex: 1;}
	.msg-list__main h2 {font-size: 15px;font-weight: normal;color: #333;}
	.msg-list__main p {font-size: 12px;color: #999;line-height: 1.8;margin-top: 5px;}
	:deep(.el-avatar--circle) {
		display: flex;
		justify-content: center;
		align-items: center;
	}

	[data-theme='dark'] .msg-list__main h2 {color: #d0d0d0;}
	[data-theme='dark'] .msg-list li {border-top:1px solid #363636;}
	[data-theme='dark'] .msg-list li a:hover {background: #383838;}
</style>
