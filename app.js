import './bootstrap';
import { createApp } from 'vue';
import SimForm from './components/SimForm.vue';

const app = createApp({});
app.component('sim-form', SimForm);
app.mount('#app');
