<template>

    <div class="category__header">
        <h3 class="category__title">My products
        </h3>
        <div class="category__tabs">
            <router-link to="/my/products">
                <div :class="['category__tabs_item', currentRoute === '/my/products' ? 'is-active' : '']"><i class="fas fa-shopping-cart"></i>All products({{allProductsCount || 0}})
                </div>
            </router-link>
            <router-link to="/my/products/connected">
                <div :class="['category__tabs_item', currentRoute === '/my/products/connected' ? 'is-active' : '']"><i class="fas fa-folder-plus"></i>Connected({{connected || 0}})
                </div>
            </router-link>
            <router-link to="/my/products/non_connected">
                <div :class="['category__tabs_item', currentRoute === '/my/products/non_connected' ? 'is-active' : '']">
                    <i class="fas fa-folder-minus"></i>Non connected({{non_connected || 0}})
                </div>
            </router-link>
        </div>
    </div>
</template>

<script>
    import Breadcrumps from "./Breadcrumps";
    import {mapState} from "vuex";

    export default {
        name: "CategoryNavigation",
        components: {
            Breadcrumps
        },
        computed: {
            ...mapState({
                allProductsCount: state => state.products.count ? state.products.count.total : 0,
                connected: state => state.products.count ? state.products.count.connected : 0,
                non_connected: state => state.products.count ? state.products.count.non_connected : 0
            })
        },
        data() {
            return {
                currentRoute: this.$route.path
            }
        },
        watch: {
            '$route'(val) {
                this.currentRoute = val.path;
            }
        }
    }
</script>

<style lang="scss">
    // @import "../../assets/_variables.module.scss";
    // @import "../../assets/_variables.module.scss";
    @import "app/styles/_variables.module.scss";

    .category__header {
        align-items: center;
        display: flex;
        justify-content: space-between;
        @media only screen and (max-width: 1200px) {
            align-items: flex-start;
            justify-content: flex-start;
            flex-direction: column;
            & > * {
                margin: $base-gutter 0;
                &:first-child {
                    margin-top: 0;
                }
                &:last-child {
                    margin-bottom: -$base-gutter;
                }
            }
        }
    }

    .category__title {
        font-size: 24px;
    }

    .category__tabs {
        justify-content: flex-end;
        display: flex;
        flex: 1;
        width: 100%;
        @media only screen and (max-width: 768px) {
            justify-content: flex-start;
            margin-bottom: $base-gutter !important;
        }
        @media only screen and (max-width: 1200px) {
            justify-content: flex-start;
            margin-bottom: $base-gutter !important;
        }
        &_item {
            color: #666;
            cursor: pointer;
            font-size: 16px;
            line-height: 20px;
            margin-left: $base-gutter * 5;
            position: relative;
            white-space: nowrap;
            @media only screen and (max-width: 768px) {
                font-size: 13px;
                margin-left: $base-gutter;
            }
            i {
                color: #999;
                @media only screen and (max-width: 768px) {
                    display: none;
                }
            }
            &:before {
                content: "";
                background-color: transparent;
                bottom: -$base-gutter / 2;
                height: 2px;
                left: 0;
                position: absolute;
                width: 100%;
            }
            &:first-child {
                margin-left: 0;
            }
            i {
                margin-right: $base-gutter;
            }
            &.is-active {
                color: #1f8d5f;
                cursor: default;
                &:before {
                    background-color: #1f8d5f;
                }
                i {
                    color: inherit;
                }
            }
        }
        & > * {
            margin-left: 50px;
            @media only screen and (max-width: 768px) {
                    margin-left: 0;
                    margin-right: 20px !important;

            }
            @media only screen and (max-width: 1200px) {
                margin-left: 0;
                margin-right: 50px;
            }
        }

    }
</style>
