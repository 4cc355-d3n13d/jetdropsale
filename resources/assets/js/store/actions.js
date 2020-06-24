import uuid from "uuid";
import axios from "axios";
const actions = {
    addUserNotificationWithRemovalTimeout({commit}, {text, type}, timeout) {
        const DEFAULT_TIMEOUT = 5000;
        const id = uuid();

        commit("addUserNotification", {text, type, id});
        setTimeout(() => {
                commit("removeUserNotification", id)
            },
            timeout || DEFAULT_TIMEOUT)
    },
    getUserInfo({commit}) {
        axios.get("/api/user/whoami").then(res => {
            commit("setUserInfo", {name: res.data.user.name})
        })
    },
    getProductsCount({commit}) {
        axios.get("/api/my-products/count").then(res => {
            commit("setProductsCount", res.data.my_products_count);

        })
    }
};

export default actions;