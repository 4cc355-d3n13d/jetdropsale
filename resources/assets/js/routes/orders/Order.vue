<template>
    <div class="order">
        <div class="order-header">
            <div class="all-except-total-container">
                <div :class="{'toggler':true, active: expanded}" @click="()=>onOrderExpandToggle(id)">
                    <font-awesome-icon icon="angle-down"/>
                </div>
                <div class="dropwow-id">
                    <div class="label">
                        Dropwow ID:
                    </div>
                    <div class="value">
                        {{id || ""}}
                    </div>
                </div>
                <div class="shopify-id">
                    <div class="label">
                        Shopify ID:
                    </div>
                    <div class="value">
                        <a :href="origin_path" target="_blank">{{origin_id || ""}}</a>
                    </div>
                </div>
                <div class="payment-status">
                    <div class="value">
                        {{status || ""}}
                    </div>
                </div>
                <div class="order-time-date">
                    <div class="label">
                        Order date
                    </div>
                    <div class="value">
                        {{created_at || ""}}
                    </div>
                </div>
            </div>
            <div class="total">
                <div class="label">
                    Total:
                </div>
                <div class="value">
                    $ {{price}}
                </div>
            </div>
        </div>


        <div class="shipment-block">
            <div class="order-action-panel">
                <div class="order-action-panel-info">
                    <!--<base-checkbox-->
                            <!--:checked="getShipmentCheckedStatusById(id)"-->
                            <!--:onChange="()=>onShipmentCheck(id)"-->
                            <!--class="shipment-id-checkbox"/>-->
                    <!--<div class="shipment-id">-->
                        <!--<div class="label">Shipment ID:</div>-->
                        <!--<div class="value"></div>-->
                    <!--</div>-->
                    <!--<div class="order-status">-->
                        <!--<div class="value">-->

                        <!--</div>-->
                    <!--</div>-->
                    <div class="tracking-number">
                        <div class="label">
                            Tracking number:
                        </div>
                        <div class="value">
                            {{tracking_number || ""}}
                        </div>

                    </div>
                </div>
                <div class="actions">
                    <base-button :onClick="()=>onOrderActionClick(url)"
                                 v-for="(url, buttonName) in can_be"
                                 :key="uuid()"
                                 :filled="buttonName === 'CONFIRMED'"
                                 small>{{buttonName}}</base-button>
                    <!--<base-button small>Add notes</base-button>-->
                </div>
            </div>
            <transition name="expand">
                <div v-show="expanded"  class="order-shipments">
                    <div class="order-body">
                        <div class="ordered-products">
                            <div v-for="item in cart" :key="item.my_variant_id" class="ordered-product">
                                <div class="ordered-product-image-container">
                                    <img :src="item.image" class="ordered-product-image">
                                </div>

                                <div class="title-price">
                                    <div class="title">
                                        {{item.title || ""}}
                                    </div>
                                    <div class="price">
                                        $ {{item.price || ""}} X {{item.quantity || ""}}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="shipping_address" class="customer-info">
                            <div class="customer-name">
                                {{shipping_address.name || ""}}
                            </div>
                            <div class="customer-address">
                                {{`${shipping_address.zip || ""} ${shipping_address.country || ""}
                                ${shipping_address.province || ""} ${shipping_address.city || ""}
                                ${shipping_address.address1 || ""} ${shipping_address.address2 || ""}`}}
                            </div>
                        </div>

                        <div class="status-container">
                            <div class="status">
                                Dropwow status:&nbsp;
                                <div class="status-value">
                                    {{status || ""}}
                                </div>
                            </div>
                            <div v-if="status.toLowerCase && status.toLowerCase() === 'hold'" class="hold-time">
                                <font-awesome-icon class="clock-icon" icon="clock"/>
                                <div class="remain-confirm">
                                    <div class="remain">
                                        Hold time remaining
                                    </div>
                                    <div class="confirm">
                                        <div class="time">
                                            {{auto_confirm_secs || ""}}
                                        </div>
                                        <div @click="()=>confirmOrder(id)" class="confirm-now">
                                            ➔Confirm now
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>


    </div>
</template>

<script>
    import BaseCheckbox from "../../common/BaseCheckbox";
    import BaseButton from "app/js/common/BaseButton";
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import {mapMutations, mapActions, mapGetters} from "vuex";
    import uuid from "uuid"

    export default {
        name: "Order",
        created() {
            this.uuid = uuid;
        },
        components: {
            BaseCheckbox,
            BaseButton
        },
        methods: {
            ...mapMutations({
                onShipmentCheck: "onShipmentCheck",
                onOrderExpandToggle: "onOrderExpandToggle"
            }),
            ...mapActions({
                onOrderActionClick: "onOrderActionClick",
                confirmOrder: "confirmOrder"
            }),
            onCheck(id) {
            },
            getShipmentCheckedStatusById(id) {

                return this.$store.state.orders.selectedShipments.includes(id)
            }
        },
        computed: {},
        props: [
            'status',
            'cart',
            'id',
            'shipping_address',
            'price',
            'can_be',
            'shipping_track',
            'expanded',
            'origin_id',
            'created_at',
            'origin_path',
            'tracking_number',
            'auto_confirm_secs'
        ]
    }
</script>

<style lang="scss" scoped>
    @import "app/styles/_variables.module.scss";

    .expand-enter {
        transition: .5s;
        max-height: 0px;
    }

    .expand-enter-to {
        transition: .5s;
        max-height: 800px;
    }

    .expand-leave {
        transition: .5s;
        max-height: 800px;
    }

    .expand-leave-to {
        transition: .5s;
        max-height: 0;
    }

    .order-header {
        background-color: $gray-light;
        /*height: 20px;*/
        padding: $base-gutter*2 $base-gutter;
        margin: $base-gutter*2 0;
        width: 100%;
        display: flex;
        justify-content: space-between;
        font-size: .9em;
        border-top: 1px solid $gray;

        @media only screen and (max-width: 768px) {
           font-size: 11px;
            flex-direction: column;
        }

        .all-except-total-container {
            @media only screen and (max-width: 768px) {
                flex-direction: column;
            }
            display: flex;
            .toggler {
                transition: .5s ease-out;
                margin-right: $base-gutter;
                background-color: $brand-green;
                border-radius: 50%;
                height: 18px;
                width: 18px;
                color: $white;
                display: flex;
                justify-content: center;
                align-items: center;
                cursor: pointer;
                & > * {
                    width: 18px;
                    height: 18px;
                    margin-top: 1px;
                }
            }

            .active {
                transform: rotate(180deg);
                transition: .5s ease-out;
            }
            .dropwow-id {
                display: flex;
                margin-right: $base-gutter * 2;
                @media only screen and (max-width: 768px) {
                    /*flex-direction: column;*/
                    margin-right: 0;
                }
                .label {
                    color: $gray-dark;
                    font-size: 14px;
                }
                .value {
                    color: $brand-green;
                    text-decoration: underline;
                    font-weight: bold;
                    margin-left: $base-gutter;
                    @media only screen and (max-width: 768px) {
                        flex-direction: column;
                        margin: 0;
                    }
                }
            }
            .shopify-id {
                display: flex;
                margin-right: $base-gutter * 2;
                @media only screen and (max-width: 768px) {
                    /*flex-direction: column;*/
                    width: 100%;
                    margin-right:0;
                }
                .label {

                    color: $gray-dark;
                }
                .value {
                    color: $brand-green;
                    text-decoration: underline;
                    font-weight: bold;
                    margin-left: $base-gutter;
                    @media only screen and (max-width: 768px) {
                        margin: 0;
                    }
                }
            }
            .payment-status {
                color: $gray-dark;
                margin-right: $base-gutter * 2;
                .value {
                    font-weight: bold;
                    @media only screen and (max-width: 768px) {
                        margin: 0;
                    }
                }
            }
            .order-time-date {
                margin-right: $base-gutter * 2;
                display: flex;
                @media only screen and (max-width: 768px) {
                    /*flex-direction: column;*/
                    margin-right: 0;
                }
                .label {
                    color: $gray-dark;

                }

                .value {
                    color: $gray-dark;
                    font-weight: bold;
                    margin-left: $base-gutter;
                    @media only screen and (max-width: 768px) {
                        margin: 0;
                    }
                }
            }
        }

        .total {
            justify-self: flex-end;
            display: flex;
            margin-right: $base-gutter;
            @media only screen and (max-width: 768px) {
                /*flex-direction: column;*/
            }
            .label {
                color: $gray-dark;
            }

            .value {
                color: $brand-green;
                font-weight: bold;
                margin-left: $base-gutter;
            }
        }
    }

    .shipment-block {
        .order-action-panel {
            margin: $base-gutter*2 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            .order-action-panel-info {
                display: flex;
                font-size: .9em;
                .shipment-id-checkbox {
                    margin-right: $base-gutter;
                }
                .shipment-id {
                    display: flex;
                    margin-right: $base-gutter*2;
                }
                .order-status {
                    margin-right: $base-gutter*2;
                    .value {
                        color: $gray-dark;
                        font-weight: bold;
                    }

                }
                .tracking-number {
                    display: flex;
                    margin-right: $base-gutter*2;
                    .label {

                    }
                    .value {
                        color: $brand-green;
                        text-decoration: underline;
                        font-weight: bold;
                        margin-left: $base-gutter/2;
                    }

                }
            }

            .actions {
                display: flex;

                & > * {
                    margin-left: $base-gutter;
                }
            }
        }
    }

    .order-shipments {
        overflow: hidden;
        @media only screen and (max-width: 768px) {
            font-size: 12px !important;
        }

        .order-body {
            font-size: .9em;
            display: flex;
            border-top: 1px solid $gray-light;
            border-bottom: 1px solid $gray-light;
            & > * {
                &:not(:last-child) {
                    border-right: 1px solid $gray-light;
                }
            }
            .ordered-products {

                flex: 3;
                display: flex;
                flex-direction: column;
                .ordered-product {
                    display: flex;

                    .ordered-product-image-container {
                        border: 1px solid $gray-light;
                        margin: $base-gutter/2;
                        padding: $base-gutter/2;

                        .ordered-product-image {
                            max-width: 80px;
                            max-height: 80px;
                        }
                    }

                    .title-price {
                        display: flex;
                        flex-direction: column;
                        padding: $base-gutter;
                        .title {

                        }

                        .price {

                        }
                    }
                }
            }

            .customer-info {
                flex: 2;
                padding: $base-gutter;

            }

            .status-container {
                flex: 2;
                display: flex;
                /*justify-content: center;*/


                color: $gray-dark;
                font-size: 14px;
                flex-direction: column;
                padding: $base-gutter;
                .status {
                    @media only screen and (max-width: 768px) {
                        flex-direction: column;
                    }
                    font-weight: bold;
                    display: flex;
                    .status-value {
                        font-weight: bold;

                    }
                }
                .hold-time {
                    display: flex;
                    align-items: center;

                    .clock-icon {
                        margin-right: $base-gutter;
                    }

                    .remain-confirm {
                        .remain {

                        }

                        .confirm {
                            display: flex;
                            .time {
                                margin-right: $base-gutter/2;
                            }

                            .confirm-now {
                                color: $brand-green;
                                text-decoration: underline;
                                cursor: pointer;
                                font-weight: bold;
                            }
                        }
                    }
                }

            }
        }
    }


</style>