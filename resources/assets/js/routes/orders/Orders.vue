<template>
    <div class="orders-container">
        <breadcrumps/>
        <container>
            <div class="orders-navigation">
                <div class="orders-navigation__header">
                    Orders
                </div>
                <div class="category__tabs">
                    <router-link
                            :key="item.id"
                            v-for="item in menuItems"
                            :to="item.route">
                        <div

                                :class="['category__tabs_item', currentRoute === item.route ? 'is-active' : '']">
                            {{item.name}}({{item.value}})
                        </div>
                    </router-link>
                    <!--<div class="[orders-navigation__filters__filter]">Hold({{hold}})</div>-->
                    <!--<div class="[orders-navigation__filters__filter]">Confirmed({{confirmed}})</div>-->
                    <!--<div class="[orders-navigation__filters__filter]">Shipped({{shipped}})</div>-->
                </div>
            </div>
            <div class="orders-filters">
                <div class="orders-filters__line">


                    <div class="orders-filters__line__standard">
                        <div>
                            Dropwow ID
                        </div>
                        <input type="text" @input="(e)=>changeOrdersFilter({value: e.target.value, type: 'id'})" class="text-input"/>
                    </div>
                    <div class="orders-filters__line__standard">
                        <div>
                            Shopify ID
                        </div>
                        <input type="text" @input="(e)=>changeOrdersFilter({value: e.target.value, type: 'originId'})" class="text-input"/>
                    </div>
                    <div class="orders-filters__line__title">
                        <div>
                            Title
                        </div>
                        <input type="text" @input="(e)=>changeOrdersFilter({value: e.target.value, type: 'title'})" class="text-input"/>
                    </div>
                    <div class="orders-filters__line__standard">
                        <div>
                            Tracking number
                        </div>
                        <input type="text" @input="(e)=>changeOrdersFilter({value: e.target.value, type: 'trackingNumber'})" class="text-input"/>
                    </div>
                </div>
                <div class="orders-filters__line">
                    <div class="orders-filters__line__standard before-search">
                        <div>
                            Statuses
                        </div>
                        <select-input
                                :value="filters.status"
                                :onSelect="value => changeOrdersFilter({value, type: 'status'})"
                                :options="statuses"
                                class="text-input"/>
                    </div>
                    <!--<div class="orders-filters__line__standard">-->
                        <!--<div>-->
                            <!--Period-->
                        <!--</div>-->
                        <!--<select-input class="text-input"/>-->
                    <!--</div>-->
                    <div class="orders-filters__line__search-and-more">
                        <div v-on:click="submitOrdersFilters" class="search-button">
                            Search
                        </div>
                        <!--<div class="more-filters">-->
                            <!--<u>-->
                                <!--More filters-->
                                <!--<font-awesome-icon icon="angle-down"></font-awesome-icon>-->
                            <!--</u>-->
                        <!--</div>-->

                    </div>
                </div>
            </div>
            <!--<div class="orders-action-bar">-->
                <!--<div class="orders-action-bar__accept-container">-->
                    <!--<base-checkbox-->
                            <!--class="orders-action-bar__accept-container__checkbox"-->
                            <!--:checked="manyShipmentsSelected"-->
                            <!--:onChange="onSelectManyOrdersCheck"/>-->
                    <!--<base-button :disabled="selectedShipments.length === 0" small >-->
                        <!--Confirm checked orders-->
                    <!--</base-button>-->
                <!--</div>-->
                <!--<div class="orders-action-bar__selected-info">-->
                    <!--<div class="items-selected">-->
                        <!--{{selectedShipments.length}} items selected-->
                    <!--</div>-->
                    <!--<div class="select-all">-->
                        <!--<u>Select all items({{orders.length}})</u>-->
                    <!--</div>-->
                <!--</div>-->
            <!--</div>-->
            <order
                    v-for="order in orders"
                    key="order.id"
                    v-bind="order"
            />


        </container>
    </div>
</template>

<script>
    import Breadcrumps from "app/js/common/Breadcrumps";
    import SelectInput from "app/js/common/SelectInput"
    import Container from "app/js/common/Container";
    import BaseButton from "app/js/common/BaseButton";
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import Order from "./Order"
    import BaseCheckbox from "../../common/BaseCheckbox";
    import {mapState, mapActions, mapGetters, mapMutations} from "vuex";

    export default {
        name: "orders",
        components: {
            BaseCheckbox,
            Breadcrumps,
            Container,
            SelectInput,
            BaseButton,
            Order
        },
        created() {
            this.getStatuses();
            this.getOrders();
        },
        methods: {
            ...mapActions({
                getOrders: "getOrders",
                getStatuses: "getStatuses",
                submitOrdersFilters: "submitOrdersFilters"
            }),
            ...mapMutations({
                onSelectManyOrdersCheck: "onSelectManyOrdersCheck",
                changeOrdersFilter: "changeOrdersFilter",
            })
        },
        data() {
            return {
                menuItems: [
                    // {
                    //     id: 1,
                    //     route: "/my/orders",
                    //     name: "All",
                    //     value: 0x
                    // },
                    // {
                    //     id: 2,
                    //     route: "/my/orders/hold",
                    //     name: "Hold",
                    //     value: 0
                    // },
                    // {
                    //     id: 3,
                    //     route: "/my/orders/confirmed",
                    //     name: "Confirmed",
                    //     value: 0
                    // },
                    // {
                    //     id: 4,
                    //     route: "/my/orders/shipped",
                    //     name: "Shipped",
                    //     value: 0
                    // }
                ],
                active: 1,
                currentRoute: this.$route.path

            }
        },
        computed: {
            ...mapGetters({
                statuses: "getStatuses",
                orders: 'parseOrdersHoldTime',
                selectedShipments: 'selectedShipments'
            }),
            ...mapState({
                filters: state => {
                    return state.orders.filters
                },
                selectedShipments: state => {
                    return state.orders.selectedShipments
                },
                manyShipmentsSelected: state => {
                    return state.orders.manyShipmentsSelected
                }
            })
        },
        watch: {
            '$route'(val) {
                this.currentRoute = val.path;
            }
        },
    }
</script>

<style lang="scss" scoped>
    @import "app/styles/_variables.module.scss";

    .category__tabs {
        justify-content: flex-end;
        display: flex;
        flex: 1;
        width: 100%;
        height: 20px;
        @media only screen and (max-width: 1200px) {
            justify-content: flex-start;
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
        }
    }

    .orders-container {
        & > * {
            margin-bottom: $base-gutter * 2;
        }

        .orders-navigation {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: $base-gutter * 2;
            &__header {
                font-size: 1.6em;
            }

            &__filters {
                display: flex;
                border-bottom: $gray;
                &__filter {
                    margin-left: $base-gutter * 2;

                }

                & .active {

                }
            }
        }

        .orders-filters {
            display: flex;
            flex-direction: column;
            width: 100%;

            &__line {
                width: 100%;
                align-items: flex-end;
                display: flex;
                @media only screen and (max-width: 768px) {
                    flex-direction: column;
                    align-items: start;

                    & > * {
                       width: 100%;
                    }
                }
                &__standard {
                    display: flex;
                    flex-direction: column;
                    flex: 3;

                }
                .before-search {
                    flex:2;
                    @media only screen and (max-width: 768px) {
                    margin-bottom: $base-gutter*2;
                }
                }
                &__title {
                    display: flex;
                    flex-direction: column;
                    flex: 4;
                }

                &__search-and-more {
                    display: flex;
                    flex: 7;
                    margin-right: $base-gutter * 2;
                    position: relative;
                    align-items: center;
                    height: 100%;
                    .search-button {
                        border: 1px solid #1f8d5f;
                        border-radius: 5px;

                        cursor: pointer;
                        /*display: inline-block;*/
                        font-size: 16px;
                        font-weight: 400;
                        line-height: 20px;
                        padding: 0 20px;
                        text-align: center;
                        white-space: nowrap;
                        background-color: #1f8d5f;
                        color: #fff;
                        display: flex;
                        align-items: center;
                        height: 40px;

                        &:hover {
                            background-color: #24a56f;
                            color: #fff;
                        }
                    }
                    .more-filters {
                        color: gray;
                        margin-left: $base-gutter * 3;
                        height: 100%;
                        display: flex;
                        align-items: center;
                        cursor: pointer;
                    }

                }

                & > * {
                    &:not(:last-child) {
                        margin-right: $base-gutter * 2;
                    }
                }
            }

        }

        .orders-action-bar {
            background-color: $gray-light;
            /*height: 20px;*/
            padding: $base-gutter*2;
            margin: $base-gutter*2 0;
            align-items: center;
            width: 100%;
            display: flex;
            justify-content: space-between;
            .orders-action-bar__accept-container {
                align-items: center;

                &__checkbox {
                    margin-right: $base-gutter;
                }
            }
            &__accept-container {
                display: flex;
                .confirm-checked {
                    margin: 0 $base-gutter;
                    padding: $base-gutter*1.2;
                    border: 1px solid $gray;
                    border-radius: $round-corners/2;
                    height: 18px;
                    /*width: 100px;*/
                    display: flex;
                    font-size: .8em;
                    justify-content: center;
                    align-items: center;
                    color: $gray;
                }
            }

            &__selected-info {
                display: flex;
                .items-selected {
                    color: $gray-dark;
                }
                .select-all {
                    margin-left: $base-gutter*2;
                    text-underline: $brand-green;
                    color: $brand-green;
                }
            }
        }
    }
</style>