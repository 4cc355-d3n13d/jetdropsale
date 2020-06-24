import axios from "axios"
import HTTPRequest from "app/js/utils/HTTPRequest"

const products = {
    state: {
        products: [],
        loadingProducts: false,
        selectedProducts: [],
        count: {
            total: 0,
            connected: 0,
            non_connected: 0
        },
        productsWithStatusArePresist: false,
        shop: '',

        //is set in router
        product_status: "all",
        productsAreFetching: false,

        pagination: {
            all: {
                total: 1,
                current: 1
            },
            connected: {
                total: 1,
                current: 1
            },
            non_connected: {
                total: 1,
                current: 1
            }

        },
        popup: {
            shown: false,
            text: "",
            okHandler: f=>f,
            cancelHandler: f=>f
        }
    },
    mutations: {
        setLoadingProductsStatus(state, loadingStatus) {
            state.loadingProducts = loadingStatus;
        },
        setProductsFetchingTrue(state) {
            state.productsAreFetching = true;
        },
        setProductsFetchingFalse(state) {
            state.productsAreFetching = false;
        },

        setCurrentPage(state, page) {
            state.pagination[state.product_status].current = page;
        },
        setCurrentProductStatus(state, product_status) {
            state.product_status = product_status;
        },
        checkProduct(state, payload) {
            const {selectedProducts} = state;
            const index = selectedProducts.findIndex(item => {
                return item === payload
            });
            if (index !== -1) {
                selectedProducts.splice(index, 1);
            } else {
                selectedProducts.push(payload)
            }
        },
        setProducts(state, payload) {
            let pendingProductPresist = false;

            const checkPendingStatus = product => {
                if (product.is_shopify_send_pending) {
                    pendingProductPresist = true;
                }
            };
            payload.products.forEach(checkPendingStatus);
            if (pendingProductPresist) {
                state.productsWithStatusArePresist = true
            } else {
                state.productsWithStatusArePresist = false
            }

            //ToDo: Решить че делать с пагинацией!

            state.pagination[payload.product_status].total = payload.pagination.total;
            state.products = payload.products;
            state.count = payload.count;
            state.shop = payload.shop;

        },
        selectAllProducts(state) {
            state.selectedProducts = state.products.map(product => product.id)
        },
        deselectAllProducts(state) {
            state.selectedProducts = [];
        },
        removeIdsFromSelected(state, ids) {

            if (Array.isArray(ids) && ids.length > 0) {
                state.selectedProducts = state.selectedProducts.filter(id => {
                    return !(ids.includes(+id) || ids.includes(id.toString()));
                })
            }
        },
        showRemovalPopup(state, params) {
            state.popup.shown = true;
            state.popup.okHandler = params.okHandler;
            state.popup.cancelHandler = params.cancelHandler;
            state.popup.text = params.text;
        },
        hideRemovalPopup(state) {
            state.popup.shown = false;
            state.popup.okHandler = f=>f;
            state.popup.cancelHandler = f=>f;
        }
    },
    actions: {
        deleteMyProduct({commit, state, dispatch}, id) {
            if (state.popup.shown) {
                commit("hideRemovalPopup");
            }

            HTTPRequest.delete(`/api/my-products/`, {params: [id]}).then(res => {

                if (res.data.deleted_my_product_ids) {

                    commit("removeIdsFromSelected", res.data.deleted_my_product_ids)
                }
                dispatch("getProducts",{dontSetLoader: true});
            })
        },
        deleteMyProducts({commit, state, dispatch}) {
            HTTPRequest.delete(`/api/my-products/`, {params: state.selectedProducts}).then(res => {

                if (res.data.deleted_my_product_ids) {

                    commit("removeIdsFromSelected", res.data.deleted_my_product_ids)
                }
                dispatch("getProducts", {dontSetLoader: true});
            })
        },
        getProducts({commit, state}, filters) {
            // if (state.productsAreFetching) {
            //     return false;
            // }
            const {product_status} = state;
            // commit("setProductsFetchingTrue");
            if (!(filters && filters.dontSetLoader)) {
                commit("setLoadingProductsStatus", true);
            }
            axios.get(`/api/my-products`, {
                params: {
                    page: filters && filters.page ? filters.page : state.pagination[product_status].current || 1,
                    product_status: state.product_status
                }
            }).then(res => {
                if (!(filters && filters.dontSetLoader)) {
                    commit("setLoadingProductsStatus", false);
                }
                // commit("setProductsFetchingFalse");
                commit("setProducts", {
                    products: res.data.my_products,
                    count: res.data.stats,
                    shop: res.data.shop,
                    pagination: {
                        current: res.data.pagination.current_page,
                        total: res.data.pagination.total_pages
                    },
                    product_status
                })
            }).catch(err => {
                if (!(filters && filters.dontSetLoader)) {
                    commit("setLoadingProductsStatus", false);
                }
            });

        },
        sendSelectedToShopify({state, commit, dispatch}) {
            HTTPRequest.post("/api/my-products/shopify/send", {
                "ids": state.selectedProducts
            },{notShowSuccessMessage: true}).then(res => {
                if (res.data.my_products) {
                    const selectedIdsToBeRemoved = [];
                    Object.keys(res.data.my_products).forEach(key => {
                        if (res.data.my_products[key].result
                            && res.data.my_products[key].result === "ok") {
                            return selectedIdsToBeRemoved.push(+key);
                        }
                    });

                    commit("removeIdsFromSelected", selectedIdsToBeRemoved);
                }
                dispatch("getProducts", {dontSetLoader: true})
            })
        },
        duplicateProduct({state, commit, dispatch}, id) {
            HTTPRequest.post(`/api/my-products/${id}/clone`).then(res => {
                dispatch("getProducts")
            })
        }
    },
    getters: {
        products: state => {
            return state.products.map(product => {
                return {
                    ...product,
                    checked: state.selectedProducts.includes(product.id),
                }
            })
        }
    }
};

export default products;
