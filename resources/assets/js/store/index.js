import Vue from "vue";
import Vuex from "vuex";
import actions from "./actions";
import mutations from "./mutations";
import orders from "./modules/orders"
import products from "./modules/products"
import product from "./modules/product"
import billing from "./modules/billing"
import settings from "./modules/settings"

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        orders,
        products,
        product,
        billing,
        settings
    },
    state: {
        menuExpanded: false,
        userNotifications: [

        ],
        user: {
            name: ""
        }
    },
    actions,
    mutations,
});
