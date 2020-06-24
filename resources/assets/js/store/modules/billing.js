import axios from "axios";
import moment from "moment";
import http from "app/js/utils/HTTPRequest";

const billing = {
    state: {
        invoices: [],
        cards: [],

    },
    actions: {
        getInvoices({commit}) {
            axios.get(`/api/user/invoices`)
                .then(res => {
                    commit("setInvoices", res.data.invoices);
                })
        },

        getCards({commit}) {
            axios.get(`/api/user/cards`)
                .then(res => {
                    commit("setCards", res.data.cards);
                })
        },

        onCardRemove({commit, dispatch}, card_id) {
            http.delete(`/api/user/cards/${card_id}`).then(res => {
                if (res.data.result === "ok") {
                    dispatch("getCards");
                }
            })
        },
        onBillPay({commit, dispatch}, id) {
            http.put(`/api/user/invoices/${id}/payment`).then(res => {
                if (res.data.result === "ok") {
                    dispatch("getInvoices");
                }
            })
        },
        setCardAsPrimary({commit, dispatch}, id) {
            http.put(`/api/user/cards/${id}/primary`).then(res => {
                if (res.data.result === "ok") {
                    dispatch("getCards");
                }
            })
        }
    },
    mutations: {
        setInvoices(state, invoices) {
            state.invoices = invoices;
        },
        setCards(state, cards) {
            state.cards = cards;
        }
    },
    getters: {
        getNextBillData(state) {
            const index = state.invoices.findIndex(invoice => {
                return invoice.status === "open"
            });

            if (index !== -1) {
                const invoice = state.invoices[index];
                return {
                    openInvoices: true,
                    date: invoice.expire && moment(invoice.expire).format("MMMM DD, YYYY"),
                    sum: invoice.sum && invoice.sum.toFixed(2),
                    id: invoice.id
                }
            } else {
                return {
                    openInvoices: false,
                    date: "",
                    id: "",
                    sum: ""
                }
            }
        },

    }
};

export default billing