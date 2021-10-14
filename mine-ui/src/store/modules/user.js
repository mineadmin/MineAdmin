import login from '@/api/apis/login';

export default {
    state: {
		routers: undefined
	},
    mutations: {
		SET_ROUTERS(state, routers){
            state.routers = routers
		},
    },
	actions: {
		getUserInfo({ commit }) {
			return new Promise((resolve, reject) => {
                login.getInfo().then(response => {
                if (response.data.roles && response.data.routers.length > 0) {
                    commit('SET_ROUTERS', response.data)
                    resolve(response.data)
                }
                }).catch(error => {
                    reject(error)
                })
            })
		}
	}
}
