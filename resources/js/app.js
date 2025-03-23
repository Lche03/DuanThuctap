import '../css/app.css';
import './bootstrap';
import { createApp } from 'vue';
import App from './components/App.vue';
import router from './router';
import axios from 'axios';

const app = createApp(App);
app.config.globalProperties.$axios = axios;
app.use(router).mount('#app');
