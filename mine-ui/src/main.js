import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import 'element-plus/theme-chalk/display.css'
import mineadmin from './mineadmin'
import i18n from './locales'
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'

const app = createApp(App)

app.use(store)
app.use(router)
app.use(ElementPlus, {size: 'small', zIndex: 1000})
app.use(i18n)
app.use(mineadmin)

//挂载app
app.mount('#app')
