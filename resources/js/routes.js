import ExampleComponent from "./components/ExampleComponent.vue";
import ProductComponent from "./components/ProductComponent.vue";
import OrderComponent from "./components/OrderComponent.vue";
import ProfileComponent from "./components/ProfileComponent.vue";
import CategoryComponent from "./components/CategoryComponent.vue";

export const routes = [
    {
        path: "/profile",
        component: ProfileComponent,
        meta: { title: 'Профиль' }
    },
    {
        path: "/product",
        component: ProductComponent,
        meta: { title: 'Мои товары' }
    },
    {
        path: "/category",
        component: CategoryComponent,
        meta: { title: 'Категории товаров' }
    },
    {
        path: "/order",
        component: OrderComponent,
        meta: { title: 'Мои заказы' }
    },
    {
        path: "/example",
        component: ExampleComponent,
    },
];