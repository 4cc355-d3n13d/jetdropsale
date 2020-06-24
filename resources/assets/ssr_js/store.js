import Vue from "vue";
import Vuex from "vuex";
import axios from "axios";
import uuid from "uuid"

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        test: true,
        userNotifications: [],
        menuExpanded: false,
        showGuest: false

    },
    mutations: {
        removeUserNotificationByIndex(state, index) {
            state.userNotifications.splice(index, 1);
        },
        removeUserNotificationById(state, id) {
            const index = state.userNotifications.findIndex(notification => {
                return notification.id === id
            });
            if (index !== -1) {
                state.userNotifications.splice(index, 1);
            }
        },
        addNotification(state, {text, type, id}) {
            state.userNotifications.push({
                text,
                type,
                id
            });
        },
        showGuestPopup(state) {
            state.showGuest = true;
        },
        closeGusetPopup(state) {
            state.showGuest = false;
        },
    },
    actions: {
        addProduct({commit, dispatch}, {id}) {
            axios.post(`/api/products/${id}/my/add`)
                .then(res => {
                    if (res.data.result === "ok") {
                        dispatch("addUserNotificationWithRemovalTimeout", {type: "success", text: "Successfully imported to my products"})
                    } else {
                        dispatch("addUserNotificationWithRemovalTimeout", {type: "error", text: res.data.message})
                    }
                })
        },
        addUserNotificationWithRemovalTimeout({commit}, {text, type}, timeout) {
            const DEFAULT_TIMEOUT = 5000;
            const id = uuid();
            commit("addNotification", {text, type, id});
            setTimeout(() => {
                    commit("removeUserNotificationById", id)
                },
                timeout || DEFAULT_TIMEOUT)
        }
    },
});