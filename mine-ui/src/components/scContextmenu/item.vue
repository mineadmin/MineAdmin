<!--
 * @Descripttion: scContextmenuItem组件
 * @version: 1.1
 * @Author: sakuya
 * @Date: 2021年7月23日16:29:36
 * @LastEditors: sakuya
 * @LastEditTime: 2021年11月26日23:13:57
-->

<template>
	<hr v-if="divided">
	<li :class="disabled?'disabled':''" @click.stop="liClick" @mouseenter="openSubmenu($event)" @mouseleave="closeSubmenu($event)">
		<span class="title">
			<el-icon class="sc-contextmenu__icon"><component :is="icon" /></el-icon>
			{{title}}
		</span>
		<span class="sc-contextmenu__suffix">
			<el-icon v-if="$slots.default"><el-icon-arrow-right /></el-icon>
			<template v-else>{{suffix}}</template>
		</span>
		<ul v-if="$slots.default" class="sc-contextmenu__menu">
			<slot></slot>
		</ul>
	</li>
</template>

<script>
	export default {
		props: {
			command: { type: String, default: "" },
			title: { type: String, default: "" },
			suffix: { type: String, default: "" },
			icon: { type: String, default: "" },
			divided: { type: Boolean, default: false },
			disabled: { type: Boolean, default: false },
		},
		inject: ['menuClick'],
		methods: {
			liClick(){
				if(this.$slots.default){
					return false
				}
				if(this.disabled){
					return false
				}
				this.menuClick(this.command)
			},
			openSubmenu(e){
				var menu = e.target.querySelector('ul')
				if(!menu){
					return false
				}
				menu.style.display = 'inline-block'
				var rect = menu.getBoundingClientRect()
				var menuX = rect.left
				var menuY = rect.top
				var innerWidth = window.innerWidth
				var innerHeight = window.innerHeight
				var menuHeight = menu.offsetHeight
				var menuWidth = menu.offsetWidth
				if(menuX + menuWidth > innerWidth){
					menu.style.left = 'auto'
					menu.style.right = '100%'
				}
				if(menuY + menuHeight > innerHeight){
					menu.style.top = 'auto'
					menu.style.bottom = '0'
				}
			},
			closeSubmenu(e){
				var menu = e.target.querySelector('ul')
				if(!menu){
					return false
				}
				menu.removeAttribute("style")
				menu.style.display = 'none'
			}
		}
	}
</script>

<style>

</style>
