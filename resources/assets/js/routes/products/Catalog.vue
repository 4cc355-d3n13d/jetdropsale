<template>
    <div class="catalogue">
        <ul v-if="loading" class="catalogue__list">
            <mock-product :key="index" v-for="(item, index) in array, mockProductsAmount"/>
        </ul>
        <ul v-else class="catalogue__list">
            <catalog-item
                    v-for="product in products"
                    :key="product.id"
                    :checked="product.checked"
                    :id="product.id"
                    :product_id="product.product_id"
                    :shopify_id="product.shopify_id"
                    :title="product.title"
                    :price="product.price"
                    :amount="product.amount"
                    :image="product.image"
                    :pending="product.is_shopify_send_pending"
                    :status="product.status"
            />
        </ul>
    </div>
</template>

<script>
    import {mapState, mapActions} from "vuex";
    import CatalogItem from "./CatalogItem";
    import Icon from 'vue-awesome/components/Icon';
    import MockProduct from './MockProduct';
    import {PENDING_REQUEST_INTERVAL} from "app/js/configs/settings"
    import {MOCK_PRODUCTS_AMOUNT} from "app/js/configs/settings"


    export default {
        data() {
            return {
                mockProductsAmount: MOCK_PRODUCTS_AMOUNT,
                intervalValue: PENDING_REQUEST_INTERVAL,
                interval: null
            }
        },
        computed: {
            ...mapState({
                loading: state => state.products.loadingProducts,
                productsWithStatusArePresist: state => state.products.productsWithStatusArePresist,
                shop: state => state.products.shop
            }),
            products() {
                return this.$store.getters.products;
            }
        },
        components: {
            CatalogItem,
            'v-icon': Icon,
            MockProduct
        },
        created() {
            this.launchInterval()
        },
        updated() {
            this.launchInterval()
        },
        destroyed() {
            clearInterval(this.interval);
            this.interval = null;
        },
        methods: {
            ...mapActions({
                getProducts: "getProducts"
            }),
            launchInterval() {
                if (this.productsWithStatusArePresist) {
                    if (!this.interval) {
                        this.interval = setInterval(
                            () => this.getProducts({"dontSetLoader": true}),
                            this.intervalValue)
                    }
                } else {
                    clearInterval(this.interval);
                    this.interval = null;
                }
            }
        }
    };
</script>

<style lang="scss">
    @import "app/styles/_variables.module.scss";

    .catalogue {
        position: relative;
        // margin: 20px;

        justify-content: space-between;
        .product-mock {
            border: 2px dashed $gray-light;
            background-color: #fff;
            border-radius: $base-gutter / 2;
            position: relative;
            float: left;
            margin: $base-gutter;
            padding-bottom: $base-gutter * 2;
            height: 400px;
            flex-wrap: wrap;

            width: calc(#{100% / 5} - #{$base-gutter * 2});

            @media only screen and (max-width: 1700px) {
                width: calc(#{100% / 4} - #{$base-gutter * 2});
            }

            @media only screen and (max-width: 880px) {
                width: calc(#{100% / 2} - #{$base-gutter * 2});
            }
            @media only screen and (max-width: 540px) {
                width: calc(#{100%} - #{$base-gutter * 2});
            }

            &__upper_child {
                height: 70%;
                width: 100%;
                padding: $base-gutter*2;
                border-bottom: 2px dashed $gray-light;
                .image-mock {
                    display: flex;
                    width: 100%;
                    height: 100%;
                    border: 2px dashed $gray-light;

                }
            }
            &__bottom_child {
                height: 30%;
                width: 100%;
                display: flex;
                flex-direction: column;
                padding: $base-gutter;
                .title-mock {
                    margin-top: 20px;
                    width: 70%;
                    background-color: $gray-light;
                    border-radius: 10px;
                    height: 10px;
                }
                .price-mock {
                    background-color: $gray-light;
                    border-radius: 10px;
                    height: 10px;
                    width: 25%;
                    margin-top: 20px;
                }
            }
        }

        &:after {
            content: "";
            display: table;
            width: 100%;
        }
        &__title {
            font-size: 24px;
            font-weight: 600;
            line-height: 30px;
            padding-bottom: $base-gutter;
            padding-right: $base-gutter * 8;
        }
        &__more {
            color: #666;
            float: right;
            font-size: 14px;
            line-height: 20px;
            position: absolute;
            right: 0;
            text-align: right;
            text-decoration: underline;
            top: 8px;
            z-index: 5;
            &:hover {
                text-decoration: none;
            }
            @media only screen and (max-width: 768px) {
                left: 0;
                position: relative;
                top: 0;

                background-color: #1f8d5f;
                border-radius: $base-gutter / 2;
                color: #fff;
                cursor: pointer;
                display: inline-block;
                font-family: 400;
                font-size: 16px;
                line-height: 20px;
                margin-top: $base-gutter * 2;
                padding: $base-gutter * 1.5 $base-gutter * 3;
                text-align: center;
                text-decoration: none;
                white-space: nowrap;
                width: 100%;
            }
        }
        &__list {
            display: block;
            list-style: none;
            margin: 0 -$base-gutter;
            // overflow: hidden;
            padding: 0;
            @media only screen and (max-width: 1200px) {
                display: block;
            }
        }

        //mod for category
        &-category {
            .catalogue {
                &__item {
                    width: calc(#{100% / 3} - #{$base-gutter * 2});
                    @media only screen and (max-width: 880px) {
                        width: calc(#{100% / 2} - #{$base-gutter * 2});
                    }
                    @media only screen and (max-width: 540px) {
                        width: calc(#{100%} - #{$base-gutter * 2});
                    }
                }
            }
        }

        //mod for few rows
        &-rows {
        }
    }
</style>
