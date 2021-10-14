<template>
	<!-- 通栏布局 -->
	<template v-if="layout=='header'">
		<header class="adminui-header">
			<div class="adminui-header-left">
				<div class="logo-bar">
					<img class="logo" src="img/logo.png">
					<span>{{ $CONFIG.APP_NAME }}</span>
				</div>
				<ul v-if="!ismobile" class="nav">
					<li v-for="item in menu" :key="item" :class="pmenu.path==item.path?'active':''" @click="showMenu(item)">
						<i :class="item.meta.icon || 'el-icon-menu'"></i>
						<span>{{ item.meta.title }}</span>
					</li>
				</ul>
			</div>
			<div class="adminui-header-right">
				<userbar></userbar>
			</div>
		</header>
		<section class="aminui-wrapper">
			<div v-if="!ismobile" :class="menuIsCollapse?'aminui-side isCollapse':'aminui-side'">
				<div v-if="!menuIsCollapse" class="adminui-side-top">
					<h2>{{ pmenu.meta.title }}</h2>
				</div>
				<div class="adminui-side-scroll">
					<el-scrollbar>
						<el-menu :default-active="$route.meta.active || $route.fullPath" router :collapse="menuIsCollapse">
							<NavMenu :navMenus="nextMenu"></NavMenu>
						</el-menu>
					</el-scrollbar>
				</div>
			</div>
			<Side-m v-if="ismobile"></Side-m>
			<div class="aminui-body el-container">
				<Topbar v-if="!ismobile"></Topbar>
				<Tags v-if="!ismobile && layoutTags"></Tags>
				<div class="adminui-main" id="adminui-main">
					<router-view></router-view>
					<iframe-view></iframe-view>
				</div>
			</div>
		</section>
	</template>

	<!-- 经典布局 -->
	<template v-else-if="layout=='menu'">
		<header class="adminui-header">
			<div class="adminui-header-left">
				<div class="logo-bar">
					<img class="logo" src="img/logo.png">
					<span>{{ $CONFIG.APP_NAME }}</span>
				</div>
			</div>
			<div class="adminui-header-right">
				<userbar></userbar>
			</div>
		</header>
		<section class="aminui-wrapper">
			<div v-if="!ismobile" :class="menuIsCollapse?'aminui-side isCollapse':'aminui-side'">
				<div class="adminui-side-scroll">
					<el-scrollbar>
						<el-menu :default-active="$route.meta.active || $route.fullPath" router :collapse="menuIsCollapse">
							<NavMenu :navMenus="menu"></NavMenu>
						</el-menu>
					</el-scrollbar>
				</div>
			</div>
			<Side-m v-if="ismobile"></Side-m>
			<div class="aminui-body el-container">
				<Topbar v-if="!ismobile"></Topbar>
				<Tags v-if="!ismobile && layoutTags"></Tags>
				<div class="adminui-main" id="adminui-main">
					<router-view></router-view>
					<iframe-view></iframe-view>
				</div>
			</div>
		</section>
	</template>

	<!-- 经典布局 -->
	<template v-else-if="layout=='dock'">
		<header class="adminui-header">
			<div class="adminui-header-left">
				<div class="logo-bar">
					<img class="logo" src="img/logo.png">
					<span>{{ $CONFIG.APP_NAME }}</span>
				</div>
			</div>
			<div class="adminui-header-right">
				<userbar></userbar>
			</div>
		</header>

		<section class="aminui-wrapper">
			<Side-m v-if="ismobile"></Side-m>
			<div class="aminui-body el-container">
				<div v-if="!ismobile" class="adminui-header-menu">
					<el-menu :default-active="$route.meta.active || $route.fullPath" router mode="horizontal">
						<NavMenu :navMenus="menu"></NavMenu>
					</el-menu>
				</div>
				<div class="adminui-main" id="adminui-main">
					<router-view></router-view>
					<iframe-view></iframe-view>
				</div>
			</div>
		</section>
	</template>

	<!-- 默认布局 -->
	<template v-else>
		<section class="aminui-wrapper">
			<div v-if="!ismobile" class="aminui-side-split">
				<div class="adminui-side-split-scroll">
					<el-scrollbar>
						<ul>
							<li v-for="item in menu" :key="item" :class="pmenu.path==item.path?'active':''"
								@click="showMenu(item)">
								<i :class="item.meta.icon || 'el-icon-menu'"></i>
								<p>{{ item.meta.title }}</p>
							</li>
						</ul>
					</el-scrollbar>
				</div>
			</div>
			<div v-if="!ismobile" :class="menuIsCollapse?'aminui-side isCollapse':'aminui-side'">
				<div v-if="!menuIsCollapse" class="adminui-side-top">
					<h2>{{ pmenu.meta.title }}</h2>
				</div>
				<div class="adminui-side-scroll">
					<el-scrollbar>
						<el-menu :default-active="$route.meta.active || $route.fullPath" router :collapse="menuIsCollapse">
							<NavMenu :navMenus="nextMenu"></NavMenu>
						</el-menu>
					</el-scrollbar>
				</div>
			</div>
			<Side-m v-if="ismobile"></Side-m>
			<div class="aminui-body el-container">
				<Topbar>
					<userbar></userbar>
				</Topbar>
				<Tags v-if="!ismobile && layoutTags"></Tags>
				<div class="adminui-main" id="adminui-main">
					<router-view></router-view>
					<iframe-view></iframe-view>
				</div>
			</div>
		</section>
	</template>

	<div class="layout-setting" @click="openSetting"><i class="el-icon-brush"></i></div>

	<el-drawer title="布局实时演示" v-model="settingDialog" :size="400" append-to-body destroy-on-close>
		<setting></setting>
	</el-drawer>
</template>

<script>
	import SideM from './components/sideM.vue';
	import Topbar from './components/topbar.vue';
	import Tags from './components/tags.vue';
	import NavMenu from './components/NavMenu.vue';
	import userbar from './components/userbar.vue';
	import setting from './components/setting.vue';
	import iframeView from './components/iframeView.vue';

	export default {
		name: 'index',
		components: {
			SideM,
			Topbar,
			Tags,
			NavMenu,
			userbar,
			setting,
			iframeView
		},
		data() {
			return {
				settingDialog: false,
				menu: [],
				nextMenu: [],
				pmenu: {}
			}
		},
		computed:{
			ismobile(){
				return this.$store.state.global.ismobile
			},
			layout(){
				return this.$store.state.global.layout
			},
			layoutTags(){
				return this.$store.state.global.layoutTags
			},
			menuIsCollapse(){
				return this.$store.state.global.menuIsCollapse
			}
		},
		created() {
			this.onLayoutResize();
			window.addEventListener('resize', this.onLayoutResize);
			let menu = this.$TOOL.data.get('user').routers
			let home = this.$router.options.routes[0].children[0];
			// 根据权限动态删除系统配置菜单
			if ( !this.$TOOL.data.get("user").codes.includes('setting:config') && this.$TOOL.data.get("user").codes[0] != '*') {
				home.children.pop()
			}
			menu.unshift(home);
			this.menu = this.filterUrl(menu)
			;
			this.showThis()
		},
		watch: {
			$route() {
				this.showThis()
			},
			layout: {
				handler(val){
					document.body.setAttribute('data-layout', val)
				},
				immediate: true,
			}
		},
		methods: {
			openSetting(){
				this.settingDialog = true;
			},
			onLayoutResize(){
				const clientWidth = document.body.clientWidth;
				if(clientWidth < 992){
					this.$store.commit("SET_ismobile", true)
				}else{
					this.$store.commit("SET_ismobile", false)
				}
			},
			//路由监听高亮
			showThis(){
				var home = this.$router.options.routes[0].children[0];
				this.pmenu = this.$route.matched[1] || home;
				this.nextMenu = this.filterUrl(this.pmenu.children);
			},
			//点击显示
			showMenu(route) {
				this.pmenu = route;
				this.nextMenu = this.filterUrl(route.children);
			},
			//转换外部链接的路由
			filterUrl(map){
				var newMap = []
				map && map.forEach(item => {
					item.meta = item.meta?item.meta:{};
					//处理隐藏
					if(item.meta.hidden){
						return false
					}
					//处理http
					if(item.meta.type == 'I'){
						item.path = `/i/${item.name}`;
					}
					//递归循环
					if(item.children&&item.children.length > 0){
						item.children = this.filterUrl(item.children)
					}
					newMap.push(item)
				})
				return newMap;
			}
		}
	}
</script>
