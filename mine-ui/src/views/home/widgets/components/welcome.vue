<template>
	<el-card shadow="hover" header="欢迎">
		<div class="welcome">
			<div class="logo">
				<img src="img/logo.svg">
				<h2>欢迎体验 MineAdmin</h2>
			</div>
			<div class="tips">
				<div class="tips-item">
					<div class="tips-item-icon"><el-icon><el-icon-promotion /></el-icon></div>
					<div class="tips-item-message">PHP有很多优秀的后台管理系统，但基于Swoole的后台管理系统没找到合适我自己的。 所以就开发了一套后台管理系统。系统可以用于网站管理后台、CMS、CRM、OA、ERP等。</div>
				</div>
				<div class="tips-item">
					<div class="tips-item-icon"><el-icon><el-icon-star /></el-icon></div>
					<div class="tips-item-message">MineAdmin是一个后台权限管理系统，提供完善的权限体系，让开发者把注意力集中到具体业务当中，降低开发成本，提高项目效率。同时，支持PC和移动端。企业和个人可以免费使用</div>
				</div>
				<div class="tips-item">
					<div class="tips-item-icon"><el-icon><el-icon-coffee /></el-icon></div>
					<div class="tips-item-message">欢迎大家提交PR，有问题可以上Gitee提Issues</div>
				</div>
			</div>
			<div class="actions">
				<el-button type="primary" icon="el-icon-check" @click="godoc">文档</el-button>
			</div>
		</div>
	</el-card>
</template>

<script>
	export default {
		title: "欢迎",
		icon: "el-icon-present",
		description: "项目特色以及文档链接",
		data() {
			return {

			}
		},
		methods: {
			godoc(){
				window.open("https://doc.mineadmin.com/")
			}
		}
	}
</script>

<style scoped>
	.welcome {}
	.welcome .logo {text-align: center;}
	.welcome .logo img {vertical-align: bottom;width: 100px;height: 100px;margin-bottom: 20px;}
	.welcome .logo h2 {font-size: 30px;font-weight: normal;display: flex;align-items: center;justify-content: center;}

	.tips {margin-top: 20px;padding:0 40px;}
	.tips-item {display: flex;align-items: center;justify-content: center;padding:7.5px 0;}
	.tips-item-icon {width: 40px;height:40px;display: flex;align-items: center;justify-content: center;border-radius: 50%;font-size: 18px;margin-right: 20px;color: var(--el-color-primary);background: rgba(180,180,180,0.1);}
	.tips-item-message {flex: 1;font-size: 14px;}

	.actions {text-align: center;margin: 40px 0 20px 0;}
</style>
