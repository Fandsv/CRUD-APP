import "mdb-vue-ui-kit";
import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';
import { createApp } from "vue";
import NavigationComponent from "./components/NavigationComponent.vue";
import { routes } from "./routes.js";
import { createRouter, createWebHistory } from "vue-router";
const router = createRouter({
    history: createWebHistory(),
    routes: routes,
});
const app = createApp(NavigationComponent);
app.use(router);
app.mount("#app");

