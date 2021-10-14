<template>
	<router-view v-slot="{ Component }">
		<keep-alive :include="this.$store.state.keepAlive.keepLiveRoute">
			<component :is="Component" :key="$route.fullPath" v-if="$store.state.keepAlive.routeShow"/>
		</keep-alive>
	</router-view>
</template>

<script>
	export default {
		name: 'empty',
		data() {
			return {

			}
		}
	}
</script>
