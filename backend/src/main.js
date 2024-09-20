import { createApp } from 'vue'
import store from './store'
import router from './router'
import './index.css'
import App from './App.vue'
// import './style.css'

createApp(App)
.use(store)
.use(router)
.mount('#app')
