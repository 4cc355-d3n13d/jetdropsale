import axios from "axios";
import moment from "moment"
//ToDo: substitute expanded with collapsed
const orders = {
    state: {
        selectedShipments: [],
        expandedOrders: [],
        orders: [],
        manyShipmentsSelected: false,
        filters: {
            id: "",
            originId: "",
            title: "",
            trackingNumber: "",
            status: ""
        },
        statuses: []

    },
    getters: {

        parseOrdersHoldTime(state, getters) {

            return getters.getOrdersWithExpandedStatus.map(order => {
                if (order.auto_confirm_secs) {
                    const time = moment.utc(order.auto_confirm_secs*1000);
                    return {...order, auto_confirm_secs: time.format('H') + "h " + time.format('mm') + "m"}
                }
                return order;
            })
        },

        getOrdersWithExpandedStatus(state, getters) {
            return state.orders.map(order => {
               return {
                   ...order,
                   expanded: !state.expandedOrders.includes(order.id)
               }
            })
        },
        getStatuses(state) {
            return Object.keys(state.statuses).map(key => {
                return {id: key, value: state.statuses[key]}
            })
        }
    },
    mutations: {
        onShipmentCheck(state, id) {
            if (state.selectedShipments.includes(id)) {
                state.selectedShipments.splice(state.selectedShipments.indexOf(id), 1)
            } else {
                state.selectedShipments.push(id)
            }
        },
        setOrders(state, orders) {
            state.orders = orders;
        },
        onOrderExpandToggle(state, id) {
            if (!state.expandedOrders.includes(id)) {
                state.expandedOrders.push(id)
            } else {
                state.expandedOrders = state.expandedOrders.filter(expandedId => expandedId !== id)
            }
        },
        onSelectManyOrdersCheck(state) {
            if(state.manyShipmentsSelected) {
                state.manyShipmentsSelected = false;
                state.selectedShipments = [];
            } else {
                state.manyShipmentsSelected = true;
                state.selectedShipments = state.orders.map(shipment => shipment.id)
            }

        },
        changeOrdersFilter(state, {value, type}) {
            state.filters[type] = value;
        },
        setStatuses(state, statuses) {
            state.statuses = statuses;
        }
    },
    actions: {
        getStatuses({commit}) {
            axios.get("/api/user/orders/statuses").then(res => {
                if (res.data.statuses) {
                    commit('setStatuses', res.data.statuses);
                }

            })
        },

        getOrders({commit}) {
            axios.get("/api/user/orders").then(res => {
                if (res.data.orders) {
                    commit('setOrders', res.data.orders);
                }

            })
        },
        onOrderActionClick({commit,dispatch }, url) {
            axios.put(url).then(res => {
                if (res.data.result === "ok") {
                    dispatch("getOrders");
                }
            })
        },
        confirmOrder({commit, dispatch}, id) {
            axios.put(`/api/user/orders/${id}/status/CONFIRMED`).then(res => {
                if (res.data.result === "ok") {
                    dispatch("getOrders");
                }
            })
        },
        submitOrdersFilters({state, commit}) {
            axios.get("/api/user/orders", {params: state.filters}).then(res => {
                if (res.data.orders) {
                    commit('setOrders', res.data.orders);
                }
            })
        }
    }
};

export default orders;