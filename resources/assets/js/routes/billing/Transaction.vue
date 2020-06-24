<template>
    <div class="billing-container">
        <breadcrumps/>
        <container>
            <div class="billing-transaction">
                <div class="billing-transaction__info">
                    <div class="billing-transaction__info__header">
                        <div class="label">Transaction</div>
                        <div class="value">{{transaction}}</div>
                    </div>
                    <div class="billing-transaction__info__body">
                        <div class="date">{{date}}</div>
                        <div class="amount">$ {{amount}}</div>
                    </div>
                </div>
                <div class="billing-transaction__actions">
                    <div class="download-invoice">
                        <font-awesome-icon class="file-download-icon" icon="file-download"/>
                        <div class="label">
                            Download invoice
                        </div>
                    </div>
                    <div class="export-to-csv">
                        <font-awesome-icon class="export-to-csv-icon" icon="file-excel"/>
                        <div class="label">
                            Export to csv
                        </div>
                    </div>
                </div>
            </div>
        </container>
        <container>
            <div class="billing-orders__header">
                Orders ({{ordersCount}})
            </div>
            <table class="billing-orders__table">
                <thead class="billing-orders__table__head">
                <td>Date</td>
                <td>ID</td>
                <td>Payment Method</td>
                <td>Amount Billed</td>
                <td>Payment Status</td>
                </thead>
                <tbody class="billing-orders__table__body">
                <tr v-for="order in orders" class="row">
                    <td class="order-date">{{order.date}}</td>
                    <td class="order-id">{{order.orderId}}</td>
                    <td class="order-payment-method">
                        <div class="order-payment-method-label">
                            {{order.paymentMethod}}
                        </div>
                        <div class="order-payment-method-value">
                            {{order.paymentInfo}}
                        </div>
                    </td>
                    <td class="order-amount-billed">
                        ${{order.amountBilled.toFixed(2)}}
                    </td>
                    <td class="order-payment-status">
                        <div :class="['order-payment-status-circle', order.paymentStatus === 'Paid' ? 'green' : 'red']">

                        </div>
                        {{order.paymentStatus}}
                    </td>
                </tr>
                </tbody>
            </table>

        </container>

            <pagination/>

    </div>
</template>

<script>

    import Breadcrumps from "app/js/common/Breadcrumps";
    import Container from "app/js/common/Container";
    import Pagination from "app/js/common/Pagination";
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import { mapActions, mapState } from "vuex";
    export default {
        name: "Billing",
        components: {
            Breadcrumps,
            Container,
            Pagination
        },


        data() {
            return {
                transaction: 12312313,
                date: "Sep 12, 2018",
                amount: "100.20",
                ordersCount: 43,
                orders: [
                    {
                        id: 1,
                        date: "Sep 12, 2018",
                        orderId: "lj34-fs4f-234tf4-fg65d",
                        paymentMethod: "Credit card",
                        paymentInfo: "sdkh6765dsf75sd".toUpperCase(),
                        amountBilled: 100.20,
                        paymentStatus: "Paid"
                    },
                    {
                        id: 1,
                        date: "Sep 12, 2018",
                        orderId: "lj34-fs4f-234tf4-fg65d",
                        paymentMethod: "Credit card",
                        paymentInfo: "sdkh6765dsf75sd".toUpperCase(),
                        amountBilled: 100.20,
                        paymentStatus: "Paid"
                    },
                    {
                        id: 1,
                        date: "Sep 12, 2018",
                        orderId: "lj34-fs4f-234tf4-fg65d",
                        paymentMethod: "Credit card",
                        paymentInfo: "sdkh6765dsf75sd",
                        amountBilled: 100.20,
                        paymentStatus: "Paid"
                    },
                    {
                        id: 1,
                        date: "Sep 12, 2018",
                        orderId: "lj34-fs4f-234tf4-fg65d",
                        paymentMethod: "Credit card",
                        paymentInfo: "sdkh6765dsf75sd",
                        amountBilled: 100.20,
                        paymentStatus: "Paid"
                    }, {
                        id: 1,
                        date: "Sep 12, 2018",
                        orderId: "lj34-fs4f-234tf4-fg65d",
                        paymentMethod: "Credit card",
                        paymentInfo: "sdkh6765dsf75sd",
                        amountBilled: 100.20,
                        paymentStatus: "Paid"
                    },

                ]
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import "app/styles/_variables.module.scss";

    .billing-container {
        & > * {
            margin: $base-gutter * 2 0;
        }

        .billing-transaction {

            display: flex;
            align-items: center;
            &__info {
                flex: 2;
                &__header {
                    display: flex;
                    font-size: 24px;
                    .label {
                        padding: $base-gutter/2;
                    }
                    .value {
                        padding: $base-gutter/2;
                        font-weight: bold;
                    }
                }

                &__body {
                    display: flex;
                    .date {
                        padding: $base-gutter/2;
                        font-size: 14px;
                        color: $gray-dark;
                        font-weight: 500;
                    }
                    .amount {
                        padding: $base-gutter/2;
                        font-size: 14px;
                        color: $gray-dark;
                        font-weight: 500;
                    }
                }
            }

            &__actions {
                flex: 1;
                height: 100%;
                align-items: center;
                display: flex;
                justify-content: center;

                .download-invoice {
                    display: flex;
                    align-items: center;
                    margin: 0 $base-gutter;
                    font-weight: bold;
                    .label {
                        text-decoration: underline;
                        color: $gray-dark;
                    }
                    .file-download-icon {
                        color: $brand-green;
                        margin-right: $base-gutter/2;
                    }
                }
                .export-to-csv {
                    margin: 0 $base-gutter;
                    display: flex;
                    align-items: center;
                    .label {
                        text-decoration: underline;
                        color: $gray-dark;
                        font-weight: bold;
                    }
                    .export-to-csv-icon {
                        color: $brand-green;
                        margin-right: $base-gutter/2;
                    }
                }
            }

        }

        .billing-orders__header {
            font-size: 24px;
            color: $gray-dark;
        }

        .billing-orders__table {
            width: 100%;
            border-collapse: collapse;
            margin: $base-gutter*2 0;
            &__head {
                width: 100%;
                height: $base-gutter*2;
                background-color: $gray-light;
                color: $gray-dark;
                font-weight: 600;
                & > td {
                    padding: $base-gutter;
                }
            }

            &__body {
                .row {
                    & > td {
                        padding: $base-gutter;
                    }
                    .order-date {
                        font-weight: 500;
                        color: $gray-dark;
                        font-size: 14px;

                    }
                    .order-id {
                        color: $brand-green;
                        font-weight: bold;
                        text-decoration: underline;
                    }
                    .order-payment-method {
                        &-label {
                            font-weight: 500;
                            font-size: 14px;
                            color: $gray-dark;
                        }

                        &-value {
                            font-weight: 500;
                            font-size: 11px;
                            color: #999999;
                        }
                    }
                    .order-amount-billed {
                        font-weight: 500;
                        font-size: 14px;
                        color: $gray-dark;
                    }
                    .order-payment-status {
                        display: flex;
                        align-items: center;
                        font-size: 14px;


                        &-circle {
                            height: 8px;
                            width: 8px;
                            border-radius: 50%;
                            margin-right: $base-gutter;
                        }
                        .red {
                            background-color: $brand-invalid;
                        }
                        .green {
                            background-color: #23BB55;
                        }
                    }
                }
            }
        }

    }

</style>