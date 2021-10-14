import config from "@/config";

export default {
	state: {
		//移动端布局
		ismobile: false,
		//布局
		layout: config.LAYOUT,
		//菜单是否折叠 toggle
		menuIsCollapse: config.MENU_IS_COLLAPSE,
		//多标签栏
		layoutTags: config.LAYOUT_TAGS,
		//主题
		theme: config.THEME,
	},
	mutations: {
		SET_ismobile(state, key){
			state.ismobile = key
		},
		SET_layout(state, key){
			state.layout = key
		},
		SET_theme(state, key){
			state.theme = key
		},
		TOGGLE_menuIsCollapse(state){
			state.menuIsCollapse = !state.menuIsCollapse
		},
		TOGGLE_layoutTags(state){
			state.layoutTags = !state.layoutTags
		}
	}
}
