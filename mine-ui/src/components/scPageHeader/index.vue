<!--
 * @Descripttion: 页面头部样式组件
 * @version: 1.0
 * @Author: sakuya
 * @Date: 2021年7月20日08:49:07
 * @LastEditors:
 * @LastEditTime:
-->

<template>
	<div class="sc-page-header">
		<div v-if="icon" class="sc-page-header__icon">
			<span>
				<el-icon><component :is="icon" /></el-icon>
			</span>
		</div>
		<div class="sc-page-header__title">
			<h2>{{ title }}</h2>
			<p v-if="description || $slots.default">
				<slot>
				{{ description }}
				</slot>
			</p>
		</div>
		<div v-if="$slots.main" class="sc-page-header__main">
			<slot name="main"></slot>
		</div>
	</div>
</template>

<script>
	export default {
		props: {
			title: { type: String, required: true, default: "" },
			description: { type: String, default: "" },
			icon: { type: String, default: "" },
		}
	}
</script>

<style scoped>
	.sc-page-header {background: #fff;border-bottom: 1px solid #e6e6e6;padding:20px 25px;display: flex;}
	.sc-page-header__icon {width: 50px;}
	.sc-page-header__icon span {display: inline-block;width: 30px;height: 30px;background: #409EFF;border-radius: 40%;display: flex;align-items: center;justify-content: center;}
	.sc-page-header__icon span i {color: #fff;font-size: 14px;}
	.sc-page-header__title {flex: 1;}
	.sc-page-header__title h2 {font-size: 17px;color: #3c4a54;font-weight: bold;margin-top: 3px;}
	.sc-page-header__title p {font-size: 13px;color: #999;margin-top: 15px;}

	[data-theme='dark'] .sc-page-header {background:#2b2b2b ;border-color:var(--el-border-color-base);}
	[data-theme='dark'] .sc-page-header__title h2 {color: #d0d0d0;}
</style>
