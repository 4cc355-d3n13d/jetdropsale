<template>
    <div class="container">
        <div class="action-panel">
            <!--<base-button :onClick="openFiltersPopup" filled>Filters</base-button>-->
            <base-button :onClick="selectAllProducts" filled>Select all</base-button>
            <base-button :disabled="!(product_status !== 'connected' && selectedProducts.length && selectedProducts.length > 0)" :onClick="sendSelectedToShopify" filled>Send to Shopify</base-button>
            <base-button :disabled="!(selectedProducts.length && selectedProducts.length > 0)" :onClick="deleteProductsHandler()">Delete</base-button>
        </div>
        <filter-popup
                v-if="showFiltersPopup"
                @close-filters-popup="closeFiltersPopup"
        />
    </div>
</template>

<script>
    import BaseButton from "app/js/common/BaseButton";
    import FilterPopup from "./FiltersPopup";
    import {mapMutations, mapActions, mapState} from "vuex";

    export default {
        components: {
            BaseButton,
            FilterPopup
        },
        computed: {
            ...mapState({
                selectedProducts: state => state.products.selectedProducts
            })
        },
        methods: {
            ...mapMutations([
                "selectAllProducts",
               "hideRemovalPopup",
                "showRemovalPopup"
            ]),
            ...mapActions([
                "sendSelectedToShopify",
                "deleteMyProducts"
            ]),
            closeFiltersPopup() {
                this.showFiltersPopup = false;
            },
            openFiltersPopup() {
                this.showFiltersPopup = true;
            }
        },
        data() {
            return {
                showFiltersPopup: false,
                deleteProductsHandler() {
                    if (this.product_status === 'connected') {
                        return () => this.showRemovalPopup({
                            "okHandler": () => {
                                this.deleteMyProducts();
                                this.hideRemovalPopup();
                            },
                            "cancelHandler": () => {
                                this.hideRemovalPopup();
                            },
                            "text": "Products will be removed from Shopify as well"
                        })
                    } else if (this.product_status === 'all') {
                        return () => this.showRemovalPopup({
                            "okHandler": () => {
                                this.deleteMyProducts();
                                this.hideRemovalPopup();
                            },
                            "cancelHandler": () => this.hideRemovalPopup(),
                            "text": "If connected products are selected they will be deleted from Shopify as well"
                        })
                    } else {
                        return this.deleteMyProducts;
                    }
                }
            };
        },
        props: {
            product_status: {
                type: String,
                default: ""
            }
        }
    };
</script>

<style lang="scss">
    @import "app/styles/_variables.module.scss";

    .container {
        position: relative;
        .action-panel {
            width: 100%;
            background-color: $white;
            border-radius: $round-corners;
            padding: $base-gutter * 2;
            box-shadow: 0 0 4px rgba(0, 0, 0, 0.1);
            // height: 80px;
            // margin: 20px;

            & > * {
                margin: 0 10px;
            }

            @media only screen and (max-width: 768px) {
                display: flex;
                & > * {
                    margin: $base-gutter/2;
                    flex: 1;

                }
            }

            @media only screen and (max-width: 450px) {
                display: flex;
                flex-direction: column;
                & > * {
                    margin: $base-gutter/2;
                    width: 100%;

                }
            }
        }
    }
</style>
