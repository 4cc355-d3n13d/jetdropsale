import store from "app/js/store";
import axios from "axios";

const notificationActionName = "addUserNotificationWithRemovalTimeout";

class HTTPRequest {

    static get(url, params, settings) {
        return axios.get(url, params).then(res => this.handleSuccess(res, settings)).catch(err=>this.handleError(err, settings));
    }

    static put(url, params, settings) {
        return axios.put(url, params).then(res => this.handleSuccess(res, settings)).catch(err=>this.handleError(err, settings));
    }

    static post(url, params, settings) {
        return axios.post(url, params).then(res => this.handleSuccess(res, settings)).catch(err=>this.handleError(err, settings));
    }

    static delete(url, params, settings) {
        return axios.delete(url, params).then(res => this.handleSuccess(res, settings)).catch(err=>this.handleError(err, settings));
    }

    static handleSuccess(res, settings) {

        if (!res.data || !res.data.result) {
            throw new Error("No result field in response");
        }

        const {message, result} = res.data;

        if (result && result === "ok") {
            if (message && !(settings && settings.notShowSuccessMessage)) {
                store.dispatch(notificationActionName,
                    {
                        type: "success",
                        text: message
                    })
            }

        } else {
            if (message && !(settings && settings.notShowErrorMessage)) {
                store.dispatch(notificationActionName,
                    {
                        type: "error",
                        text: message || "Error occurred"
                    })
            }
        }
        return res;
    }

    static handleError(err, settings) {
        const errorText =
            err.response && err.response.data && err.response.data.message
                ?
                err.response.data.message
                :
                err.message || "Error occurred";
        if (!(settings && settings.notShowErrorMessage)) {
            store.dispatch(notificationActionName,
                {
                    type: "error",
                    text: errorText
                })
        }
        return err;
    }
}

export default HTTPRequest;