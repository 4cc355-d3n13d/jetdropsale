import axios from "axios";
import HTTPRequest from "app/js/utils/HTTPRequest"

const settings = {
    state: {
        currentSettings: {
            gpr_type: "",
            gpr_rate: "",
            order_hold_time: "",
        },
        defaultSettings: {
            gpr_type: "",
            gpr_rate: "",
            order_hold_time: "",
        }
    },
    actions: {
        getAllSettings({commit, state}) {
            HTTPRequest.get(`/api/user/settings`).then(res => {
                commit("setAllSettings", res.data.settings)
            })
        },
        saveAllSettings({commit, dispatch, state}) {
            const currentSettings = {...state.currentSettings};
            HTTPRequest.put(`/api/user/settings`, {...currentSettings}).then(res => {
                if (res.data.result && res.data.result === "ok") {
                    commit("setDefaultSettings", currentSettings);

                }
            })

        }
    },
    mutations: {
        setDefaultSettings(state, settings) {
            state.defaultSettings = settings;
        },
        changeSettingValue(state, {value, setting}) {
            state.currentSettings[setting] = value;
        },
        setAllSettings(state, settings) {
            Object.keys(settings).map(key => {
                state.currentSettings[key] = settings[key];
                state.defaultSettings[key] = settings[key];
            })
        },
        cancelAllSettings(state) {
            state.currentSettings = {...state.defaultSettings}
        }
    }
}

export default settings;