import Vue from "vue";
import Router from "vue-router";
import Alerts from "app/js/routes/alerts/Alerts";
import Orders from "app/js/routes/orders/Orders";
import Products from "app/js/routes/products/Products";
import ProductEdit from "app/js/routes/product_edit/ProductEdit";
import Billing from "app/js/routes/billing/Billing";
import Settings from "app/js/routes/settings/Settings";
import store from "./store"

Vue.use(Router);


const router = new Router({
    mode: "history",
    base: process.env.BASE_URL,
    routes: [
        {
            path: "/my",
            name: "products",
            redirect: "/my/products",

        },
        {
            path: "/my/products",
            name: "products",
            component: Products,
            props: {product_status: "all"}

        },
        {
            path: "/my/products/connected",
            name: "connected",
            component: Products,
            props: {product_status: "connected"}
        },
        {
            path: "/my/products/non_connected",
            name: "non_connected",
            component: Products,
            props: {product_status: "non_connected"}
        },

        {
            path: "/my/product_edit/:id",
            name: "product_edit",
            component: ProductEdit
        },
        {
            path: "/my/orders",
            name: "orders",
            component: Orders
        },
        {
            path: "/my/orders/hold",
            name: "orders",
            component: Orders
        },
        {
            path: "/my/orders/confirmed",
            name: "orders",
            component: Orders
        },
        {
            path: "/my/orders/shipped",
            name: "orders",
            component: Orders
        },
        {
            path: "/my/alerts",
            name: "alerts",
            component: Alerts
        },
        {
            path: "/my/billing",
            name: "billing",
            component: Billing
        },
        {
            path: "/my/settings",
            name: "settings",
            component: Settings
        }

    ]
});

router.beforeEach((to, from, next) => {
    store.commit("collapseMobileMenu");
    store.commit("deselectAllProducts");
    next();
})

export default router;
