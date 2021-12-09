<!--
 * @Descripttion: 仿钉钉流程设计器
 * @version: 1.1
 * @Author: sakuya
 * @Date: 2021年9月14日08:38:35
 * @LastEditors: sakuya
 * @LastEditTime: 2021年10月28日23:07:06
-->

<template>
	<div class="sc-workflow-design">
		<div class="box-scale">
			<node-wrap v-if="nodeConfig" v-model="nodeConfig"></node-wrap>
			<div class="end-node">
				<div class="end-node-circle"></div>
				<div class="end-node-text">流程结束</div>
			</div>
		</div>
		<use-select v-if="selectVisible" ref="useselect" @closed="selectVisible=false"></use-select>
	</div>
</template>

<script>
	import nodeWrap from './nodeWrap'
	import useSelect from './select'

	export default {
		provide(){
			return {
				select: this.selectHandle
			}
		},
		props: {
			modelValue: { type: Object, default: () => {} }
		},
		components: {
			nodeWrap,
			useSelect
		},
		data() {
			return {
				nodeConfig: this.modelValue,
				selectVisible: false
			}
		},
		watch:{
			modelValue(val){
				this.nodeConfig = val
			}
		},
		mounted() {

		},
		methods: {
			selectHandle(type, data){
				this.selectVisible = true
				this.$nextTick(() => {
					this.$refs.useselect.open(type, data)
				})
			}
		}
	}
</script>

<style lang="scss">
	.sc-workflow-design {width: 100%;}
	.sc-workflow-design .box-scale {display: inline-block;position: relative;width: 100%;padding: 54.5px 0px;align-items: flex-start;justify-content: center;flex-wrap: wrap;min-width: min-content;}

	.sc-workflow-design {
		.node-wrap {display: inline-flex;width: 100%;flex-flow: column wrap;justify-content: flex-start;align-items: center;padding: 0px 50px;position: relative;z-index: 1;}
		.node-wrap-box {display: inline-flex;flex-direction: column;position: relative;width: 220px;min-height: 72px;flex-shrink: 0;background: rgb(255, 255, 255);border-radius: 4px;cursor: pointer;box-shadow: 0 2px 5px 0 rgba(0,0,0,.1);}
		.node-wrap-box::before {content: "";position: absolute;top: -12px;left: 50%;transform: translateX(-50%);width: 0px;border-style: solid;border-width: 8px 6px 4px;border-color: rgb(202, 202, 202) transparent transparent;background: #f6f8f9;}
		.node-wrap-box.start-node:before {content: none}
		.node-wrap-box .title {height:24px;line-height: 24px;color: #fff;padding-left: 16px;padding-right: 30px;border-radius: 4px 4px 0 0;position: relative;display: flex;align-items: center;}
		.node-wrap-box .title .icon {margin-right: 5px;}
		.node-wrap-box .title .close {font-size: 15px;position: absolute;top:50%;transform: translateY(-50%);right:10px;display: none;}
		.node-wrap-box .content {position: relative;padding: 15px;}
		.node-wrap-box .content .placeholder {color: #999;}
		.node-wrap-box:hover .close {display: block;}
		.add-node-btn-box {width: 240px;display: inline-flex;flex-shrink: 0;position: relative;z-index: 1;}
		.add-node-btn-box:before {content: "";position: absolute;top: 0px;left: 0px;right: 0px;bottom: 0px;z-index: -1;margin: auto;width: 2px;height: 100%;background-color: rgb(202, 202, 202);}
		.add-node-btn {user-select: none;width: 240px;padding: 20px 0px 32px;display: flex;justify-content: center;flex-shrink: 0;flex-grow: 1;}
		.add-node-btn span {}
		.add-branch {justify-content: center;padding: 0px 10px;position: absolute;top: -16px;left: 50%;transform: translateX(-50%);transform-origin: center center;z-index: 1;display: inline-flex;align-items: center;}
		.branch-wrap {display: inline-flex;width: 100%;}
		.branch-box-wrap {display: flex;flex-flow: column wrap;align-items: center;min-height: 270px;width: 100%;flex-shrink: 0;}
		.col-box {display: inline-flex;flex-direction: column;align-items: center;position: relative;background: #f6f8f9;}
		.branch-box {display: flex;overflow: visible;min-height: 180px;height: auto;border-bottom: 2px solid #ccc;border-top: 2px solid #ccc;position: relative;margin-top: 15px;}
		.branch-box .col-box::before {content: "";position: absolute;top: 0px;left: 0px;right: 0px;bottom: 0px;z-index: 0;margin: auto;width: 2px;height: 100%;background-color: rgb(202, 202, 202);}
		.condition-node {display: inline-flex;flex-direction: column;min-height: 220px;}
		.condition-node-box {padding-top: 30px;padding-right: 50px;padding-left: 50px;justify-content: center;align-items: center;flex-grow: 1;position: relative;display: inline-flex;flex-direction: column;}
		.condition-node-box::before {content: "";position: absolute;top: 0px;left: 0px;right: 0px;bottom: 0px;margin: auto;width: 2px;height: 100%;background-color: rgb(202, 202, 202);}
		.auto-judge {position: relative;width: 220px;min-height: 72px;background: rgb(255, 255, 255);border-radius: 4px;padding: 15px 15px;cursor: pointer;box-shadow: 0 2px 5px 0 rgba(0,0,0,.1);}
		.auto-judge::before {content: "";position: absolute;top: -12px;left: 50%;transform: translateX(-50%);width: 0px;border-style: solid;border-width: 8px 6px 4px;border-color: rgb(202, 202, 202) transparent transparent;background: rgb(245, 245, 247);}
		.auto-judge .title {line-height: 16px;}
		.auto-judge .title .node-title {color: #15BC83;}
		.auto-judge .title .close {font-size: 15px;position: absolute;top:15px;right:15px;color: #999;display: none;}
		.auto-judge .title .priority-title {position: absolute;top:15px;right:15px;color: #999;}
		.auto-judge .content {position: relative;padding-top: 15px;}
		.auto-judge .content .placeholder {color: #999;}
		.auto-judge:hover {
			.close {display: block;}
			.priority-title {display: none;}
		}
		.top-left-cover-line, .top-right-cover-line {position: absolute;height: 3px;width: 50%;background-color: #f6f8f9;top: -2px;}
		.bottom-left-cover-line, .bottom-right-cover-line {position: absolute;height: 3px;width: 50%;background-color: #f6f8f9;bottom: -2px;}
		.top-left-cover-line {left: -1px;}
		.top-right-cover-line {right: -1px;}
		.bottom-left-cover-line {left: -1px;}
		.bottom-right-cover-line {right: -1px;}
		.end-node {border-radius: 50%;font-size: 14px;color: rgba(25,31,37,.4);text-align: left;}
		.end-node-circle {width: 10px;height: 10px;margin: auto;border-radius: 50%;background: #dbdcdc;}
		.end-node-text {margin-top: 5px;text-align: center;}
		.auto-judge:hover {
			.sort-left {display: flex;}
			.sort-right {display: flex;}
		}
		.auto-judge .sort-left {position: absolute;top: 0;bottom: 0;z-index: 1;left: 0;display: none;justify-content: center;align-items: center;flex-direction: column;}
		.auto-judge .sort-right {position: absolute;top: 0;bottom: 0;z-index: 1;right: 0;display: none;justify-content: center;align-items: center;flex-direction: column;}
		.auto-judge .sort-left:hover, .auto-judge .sort-right:hover {background: #eee;}
		.auto-judge:after {pointer-events: none;content: "";position: absolute;top:0;bottom:0;left:0;right:0;z-index: 2;border-radius: 4px;transition: all .1s;}
		.auto-judge:hover:after {border: 1px solid #3296fa;box-shadow: 0 0 6px 0 rgba(50,150,250,.3);}
		.node-wrap-box:after {pointer-events: none;content: "";position: absolute;top:0;bottom:0;left:0;right:0;z-index: 2;border-radius: 4px;transition: all .1s;}
		.node-wrap-box:hover:after {border: 1px solid #3296fa;box-shadow: 0 0 6px 0 rgba(50,150,250,.3);}
	}

	.tags-list {margin-top: 15px;}
	.add-node-popover-body {}
	.add-node-popover-body li {display: inline-block;width: 80px;text-align: center;padding:10px 0;}
	.add-node-popover-body li i {border: 1px solid var(--el-border-color-light);width:40px;height:40px;border-radius: 50%;text-align: center;line-height: 38px;font-size: 18px;cursor: pointer;}
	.add-node-popover-body li i:hover {border: 1px solid #3296fa;background: #3296fa;color: #fff!important;}
	.add-node-popover-body li p {font-size: 12px;margin-top: 5px;}
	.node-wrap-drawer__title {padding-right:40px;}
	.node-wrap-drawer__title label {cursor: pointer;}
	.node-wrap-drawer__title label:hover {border-bottom: 1px dashed #409eff;}
	.node-wrap-drawer__title .node-wrap-drawer__title-edit {color: #409eff;margin-left: 10px;vertical-align: middle;}

	[data-theme='dark'] .sc-workflow-design {
		.node-wrap-box,.auto-judge {background: #2b2b2b;}
		.col-box {background: #222225;}
		.top-left-cover-line,
		.top-right-cover-line,
		.bottom-left-cover-line,
		.bottom-right-cover-line {background-color: #222225;}
		.node-wrap-box::before,.auto-judge::before {background-color: #222225;}
		.branch-box .add-branch {background: #222225;}
		.end-node .end-node-text {color: #d0d0d0;}
		.auto-judge .sort-left:hover, .auto-judge .sort-right:hover {background: #222225;}
	}

</style>
