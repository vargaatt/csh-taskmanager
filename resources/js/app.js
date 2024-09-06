import { createApp } from 'vue';
import App from './components/App.vue';
import './bootstrap.js'
// Import Vuetify components and styles
import 'vuetify/styles';
import { createVuetify } from 'vuetify';
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';
import '@mdi/font/css/materialdesignicons.css'

const vuetify = createVuetify({
    components,
    directives,
    theme: {
        defaultTheme: 'dark'
    }
});

const app = createApp(App);
app.use(vuetify);
app.mount('#app');
