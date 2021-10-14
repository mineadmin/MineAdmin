export default {
	state: {
		keepLiveRoute: ['empty'],
		routeKey: null,
		routeShow: true
	},
	mutations: {
		pushKeepLive(state, component){
			if(!state.keepLiveRoute.includes(component)){
				state.keepLiveRoute.push(component)
			}
		},
		removeKeepLive(state, component){
			var index = state.keepLiveRoute.indexOf(component);
			if(index !== -1){
				state.keepLiveRoute.splice(index, 1);
			}
		},
		clearKeepLive(state){
			state.keepLiveRoute = ['empty']
		},
		setRouteKey(state, key){
			state.routeKey = key
		},
		setRouteShow(state, key){
			state.routeShow = key
		}
	},
	actions: {
		setRouteKey({ commit }, key) {
			commit('setRouteKey', key);
		}
	}
}
