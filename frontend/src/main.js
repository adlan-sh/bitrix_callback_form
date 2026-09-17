import { createApp } from 'vue';
import App from './App.vue';

const mountPoint = document.getElementById('callback-form-root');

if (mountPoint && window.CALLBACK_FORM_DATA) {
    const app = createApp(App, {
        formData: window.CALLBACK_FORM_DATA,
    });
    app.mount(mountPoint);
}