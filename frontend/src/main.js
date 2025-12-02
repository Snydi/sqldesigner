import 'vue-toast-notification/dist/theme-sugar.css';
import { createApp } from 'vue';
import ToastPlugin from 'vue-toast-notification';
import axios from '@/axios';

import store from '@/store/index.js'
import router from '@/router/index.js';
import App from '@/App.vue';

const app = createApp(App);

app.config.globalProperties.$http = axios;

app.use(router);
app.use(store);
app.use(ToastPlugin, {
  position: 'top-right'
});

app.mount('#app');