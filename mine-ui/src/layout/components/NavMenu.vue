<template>
	<div v-if="navMenus.length<=0" style="padding:20px;">
		<el-alert title="无子集菜单" center type="info" :closable="false"></el-alert>
	</div>
	<template v-for="navMenu in navMenus" v-bind:key="navMenu">
		<el-menu-item v-if="!hasChildren(navMenu)" :index="navMenu.path">
			<a v-if="navMenu.meta&&navMenu.meta.type == 'L' " :href="navMenu.path" target="_blank" @click.stop='a'></a>
			<i v-if="navMenu.meta&&navMenu.meta.icon" :class="navMenu.meta.icon || 'el-icon-menu'"></i>
			<template #title>
				<span>{{navMenu.meta.title}}</span>
			</template>
		</el-menu-item>
		<el-sub-menu v-else :index="navMenu.path">
			<template #title>
				<i v-if="navMenu.meta&&navMenu.meta.icon" :class="navMenu.meta.icon || 'el-icon-menu'"></i>
				<span>{{navMenu.meta.title}}</span>
			</template>
			<NavMenu :navMenus="navMenu.children"></NavMenu>
		</el-sub-menu>
	</template>
</template>

<script>
	export default {
		name: 'NavMenu',
		props: ['navMenus'],
		data() {
			return {}
		},
		methods: {
			a(){},
			hasChildren(item){
				var flag = true
				if (item.children) {
					if (item.children.every(item => item.meta.hidden)){
						flag = false
					}
				}else{
					flag = false
				}
				return flag;
			}
		}
	}
</script>
