<template>
    <container>

        <div class="bill__payments">
            <div class="next-bill">
                <div class="next-bill__header">
                    <div class="billing-title">
                        Next bill
                    </div>
                    <!--<base-button small gray>-->
                        <!--Manage-->
                    <!--</base-button>-->
                </div>
                <div class="next-bill__body">
                    <div class="date">

                        <div v-if="getNextBillData.openInvoices" class="title">
                            {{getNextBillData.date || ""}}
                        </div>
                        <div v-else class="title">
                            No invoices
                        </div>
                        <div class="body">
                            You'll also get a bill when you spend $20.00.
                        </div>
                    </div>
                    <div class="price">
                        <div class="amount">
                            ${{getNextBillData.sum || 0}}
                        </div>
                        <div class="amount-due">
                            Amount due
                        </div>
                        <div v-if="getNextBillData.openInvoices" class="pay-now" @click="() => onBillPay(getNextBillData.id)">
                            Pay now
                        </div>
                    </div>
                </div>
            </div>
            <div class="payment-methods">
                <div class="payment-methods__header">
                    <div class="billing-title">
                        Payment method
                    </div>
                    <base-button :onClick="onPaymentMethodAdd" small filled>
                        Add payment method
                    </base-button>
                </div>
                <div class="payment-methods__cards">

                    <div v-if="cards.length > 0" v-for="card in cards" class="payment-methods__card">
                        <div class="card-info">
                            <img class="card-image" :src="'/images/'+card.brand.toLowerCase()+'.png'" alt="">

                            <div class="info">
                                <div class="cr-card">
                                    Credit card
                                </div>
                                <div class="expires">
                                    {{card.exp_month || ""}} {{card.exp_year || ""}}
                                </div>
                            </div>
                            <div class="primary">
                                {{card.primary ? "Primary" : ""}}
                            </div>
                        </div>
                        <div class="card-actions">
                            <base-button v-if="!card.primary" :onClick="()=>setCardAsPrimary(card.id)" small gray>
                                Set as primary
                            </base-button>
                            <base-button :onClick="()=>onCardRemove(card.id)" small gray>
                                Remove
                            </base-button>
                        </div>
                    </div>
                    <!--<div class="payment-methods__card">-->
                        <!--&lt;!&ndash;<div class="card-image">&ndash;&gt;-->
                            <!--<img  class="card-image" src="/images/visa.png" alt="">-->
                        <!--&lt;!&ndash;</div>&ndash;&gt;-->
                        <!--<div class="info">-->
                            <!--<div class="cr-card">-->
                                <!--Credit card-->
                            <!--</div>-->
                            <!--<div class="expires">-->
                                <!--Expires on 03-->
                            <!--</div>-->
                        <!--</div>-->
                        <!--<div class="primary">-->

                        <!--</div>-->
                    <!--</div>-->
                </div>
            </div>
        </div>
        <!--<div class="billing-orders__header">-->
            <!--Billing History-->
        <!--</div>-->
        <!--<div class="ontable">-->
            <!--<div class="search-container">-->
                <!--<search-input/>-->
            <!--</div>-->
            <!--<div class="filters-container">-->
                <!--<select-input/>-->
                <!--<select-input/>-->
            <!--</div>-->
            <!--<div class="downloads-container">-->

                    <!--<div class="download-invoices">-->
                        <!--<font-awesome-icon class="file-download-icon" icon="file-download"/>-->
                        <!--<div class="label">-->
                            <!--Download invoice-->
                        <!--</div>-->
                    <!--</div>-->
                    <!--<div class="export-to-csv">-->
                        <!--<font-awesome-icon class="export-to-csv-icon" icon="file-excel"/>-->
                        <!--<div class="label">-->
                            <!--Export to csv-->
                        <!--</div>-->
                    <!--</div>-->

            <!--</div>-->
        <!--</div>-->
        <table class="billing-orders__table">
            <thead class="billing-orders__table__head">
            <th>Date Billed</th>
            <th>Transaction ID</th>
            <th>Payment Method</th>
            <th>Amount Billed</th>
            <th>Payment <br media="only screen and (max-width: 768px)"> Status</th>
            </thead>
            <tbody class="billing-orders__table__body">
            <tr v-for="invoice in invoices" class="row">
                <td class="order-date">{{formatDate(invoice.expire)}}</td>
                <td class="transaction-id">
                    <div class="transaction-id-value">
                       <div>
                           {{invoice.id || ""}}
                       </div>
                    </div>
                    <!--<div class="transaction-id-orders-count">-->
                        <!--{{invoice.ordersCount || "92 orders"}}-->
                    <!--</div>-->

                </td>
                <td class="order-payment-method">
                    <div class="order-payment-method-label">
                        {{(invoice.paid_with && invoice.paid_with.brand) || ""}}
                    </div>
                    <div class="order-payment-method-value">
                        {{(invoice.paid_with && invoice.paid_with.last4) || ""}}
                    </div>
                </td>
                <td class="order-amount-billed">
                    ${{invoice.sum.toFixed(2)}}
                </td>
                <td class="order-payment-status">
                    <div class="status-with-circle">
                        <div :class="['order-payment-status-circle', invoice.status === 'rejected' ? 'red' : 'green']">

                        </div>
                        <div class="invoice-stauts">{{invoice.status}}</div>
                    </div>

                    <base-button
                            small
                            gray
                            :onClick="() => onBillPay(invoice.id)"
                            v-if="invoice.status==='rejected'">
                        Retry
                    </base-button>
                </td>
            </tr>
            <tr class="billing-table-total">
                <td>
                    Total
                </td>
                <td>
                    {{invoices.length}} transactions
                </td>
                <td>

                </td>
                <td>
                   ${{invoices.reduce((a,b)=>a+b.sum, 0).toFixed(2)}}
                </td>
                <td>

                </td>
            </tr>
            </tbody>
        </table>
    </container>

</template>

<script>
    // import Info from "app/js/common/Info"
    import { mapActions, mapState, mapGetters } from "vuex";
    import BaseButton from "app/js/common/BaseButton";
    import Container from "app/js/common/Container";
    import SelectInput from "app/js/common/SelectInput";
    import SearchInput from "app/js/common/SearchInput";
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import moment from "moment"

    export default {
        name: "Billing",
        components: {
            BaseButton,
            Container,
            SelectInput,
            SearchInput
        },
        created() {
            this.getInvoices();
            this.getCards();

        },
        mounted() {
            const that = this;
            this.handler = StripeCheckout.configure({
                key: 'pk_live_j7JTPXRWTjY3naJO6FnbimmM',
                image: '/img/dropwow-logo.svg',
                locale: 'auto',
                source: function(source) {
                    // You can access the token ID with `token.id`.
                    // Get the token ID to your server-side code for use.
                    let body =
                        'source=' + encodeURIComponent(source.id) +
                        '&brand=' + encodeURIComponent(source.card.brand) +
                        '&last4=' + encodeURIComponent(source.card.last4) +
                        '&exp_month=' + encodeURIComponent(source.card.exp_month) +
                        '&exp_year=' + encodeURIComponent(source.card.exp_year)

                    ;

                    let xhr = new XMLHttpRequest();
                    xhr.open('POST', '/api/user/cards');
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                    // xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                    xhr.onreadystatechange = () => {

                        // Process the server response here.
                        if (xhr.readyState === XMLHttpRequest.DONE) {
                            if (xhr.status === 200) {
                                that.getCards();
                            } else {

                            }
                        }
                    };
                    xhr.send(body);
                }
            });

            window.addEventListener('popstate', function() {
                this.handler.close();
            });
        },
        methods: {
            ...mapActions({
                getInvoices: "getInvoices",
                getCards: "getCards",
                onCardRemove: "onCardRemove",
                onBillPay: "onBillPay",
                setCardAsPrimary: "setCardAsPrimary"
            }),
            formatDate(date) {
                return moment(date).format("MMM DD, YYYY")
            },
            onPaymentMethodAdd() {
                this.handler.open({
                    name: 'DROPWOW PTE LTD .',
                    description: 'Enter your payment details',
                    panelLabel: 'Save card',
                    allowRememberMe: false,
                    email: '{{ $user->email }}'
                });
                e.preventDefault();
            }
        },
        computed: {
            ...mapState({
                cards: state => state.billing.cards,
                invoices: state => state.billing.invoices
            }),

            ...mapGetters({
                getNextBillData: "getNextBillData"
            })
        }
    }
</script>

<style lang="scss" scoped>
    @import "app/styles/_variables.module.scss";

    .bill__payments {
        display: flex;
        border-bottom: 1px solid $gray-light;
        @media only screen and (max-width: 768px) {
            flex-direction: column;
        }
        .next-bill {
            padding: 0 $base-gutter*2 $base-gutter*2 0;
            flex: 1;
            display: flex;
            border-right: 1px solid $gray;
            width: 100%;
            flex-direction: column;
            @media only screen and (max-width: 768px) {
                border-right: none;
                border-bottom: 1px solid $gray;
                margin-bottom: $base-gutter;
            }
            &__header {
                display: flex;
                justify-content: space-between;
                font-size: 24px;
                color: $gray-dark;
                margin-bottom: $base-gutter*2;
            }
            &__body {
                display: flex;
                .date {
                    background-color: #FAFAFA;
                    padding: $base-gutter*2;
                    display: flex;
                    flex-direction: column;
                    flex: 3;
                    .title {
                        font-size: 22px;
                        font-weight: bold;

                    }

                    .body {
                        color: $gray-dark;
                    }
                }

                .price {
                    flex: 1;
                    display: flex;
                    flex-direction: column;
                    margin: $base-gutter;
                    justify-content: center;
                    align-items: center;
                    .amount {
                        font-size: 22px;
                        font-weight: bold;
                    }

                    .amount-due {
                        color: $gray-dark;
                    }

                    .pay-now {
                        cursor: pointer;
                        text-decoration: underline;
                        color: $brand-green;
                    }
                }
            }
        }
        .payment-methods {
            flex: 1;
            padding: 0 $base-gutter*2 $base-gutter*2 $base-gutter*2;
            display: flex;
            width: 100%;
            flex-direction: column;
            &__header {
                display: flex;
                justify-content: space-between;
                font-size: 24px;
                color: $gray-dark;
            }
            &__cards {
                display: flex;
                flex-direction: column;

                .payment-methods__card {
                    align-items: center;
                    display: flex;
                    justify-content: space-between;

                    .card-info {
                        flex: 2;
                        display: flex;


                        & > * {
                            padding: $base-gutter/2;
                        }

                        .card-image {
                            max-height: 60px;
                        }

                        .info {
                            align-items: center;
                            display: flex;
                            flex-direction: column;
                            .cr-card {
                                font-size: 14px;
                                color: $gray-dark;
                            }

                            .expires {
                                font-size: 11px;
                                color: $gray-dark;
                            }
                        }

                        .primary {
                            color: $brand-green;
                            font-size: 14px;
                            font-weight: bold;
                            display: flex;
                            align-items: center;
                        }
                    }

                    .card-actions {
                        display: flex;
                        flex:1;
                        justify-content: flex-end;
                        @media only screen and (max-width: 768px) {
                            flex-direction: column;

                        }

                        & > * {

                           &:not(:last-child) {
                               margin-right: 10px;
                           }
                        }
                    }
                }

            }
        }


    }

    .billing-orders__header {
        font-size: 24px;
        color: $gray-dark;
    }
    .ontable {
        flex: 1;
        height: 100%;
        align-items: center;
        display: flex;
        justify-content: center;
        .search-container {
            flex: 2;
            display: flex;
            & > * {
                margin-right: $base-gutter;
            }
        }
        .filters-container {
            flex: 3;
            display: flex;
            & > * {
                margin-right: $base-gutter;
            }
        }
        .downloads-container {
            flex: 2;
            display: flex;
            .download-invoices {
                display: flex;
                align-items: center;
                margin: 0 $base-gutter;
                font-weight: bold;
                .label {
                    text-decoration: underline;
                    color: $gray-dark;
                    font-size: 14px;
                    font-weight: 500;
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
                    font-size: 14px;
                    font-weight: 500;
                }
                .export-to-csv-icon {
                    color: $brand-green;
                    margin-right: $base-gutter/2;
                }
            }
        }



    }
    .billing-orders__table {
        table-layout: fixed;
        width: 100%;
        border-collapse: collapse;
        margin: $base-gutter*2 0;
        &__head {
            width: 100%;
            height: $base-gutter*2;
            background-color: #f1f1f1;
            color: $gray-dark;
            font-weight: 600;
            & > th {
                padding: $base-gutter;
                @media only screen and (max-width: 768px) {
                    font-size: 8px !important;
                    padding: 0;

                }
            }
        }

        &__body {
            @media only screen and (max-width: 768px) {
                font-size: 11px !important;

            }
            .row {

                border-bottom: 1px solid $gray-light;
                & > td {
                    padding: $base-gutter;
                    @media only screen and (max-width: 768px) {
                        font-size: 11px !important;
                        padding: 0;
                    }
                }
                .order-date {
                    font-weight: 500;
                    color: $gray-dark;
                    font-size: 14px;
                    @media only screen and (max-width: 768px) {
                        font-size: 11px !important;
                    }

                }
                .transaction-id {

                    height: 100%;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;

                    &-value {
                        color: $brand-green;
                        font-weight: bold;
                        text-decoration: underline;
                        font-size: 14px;

                    }

                    &-orders-count {
                        color: $gray-dark;
                        font-size: 11px;
                    }
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
                    flex-direction: column;
                    .status-with-circle {
                        display: flex;
                        align-items: center;
                        .invoice-stauts {
                            margin-right: $base-gutter;

                        }

                        .order-payment-status-circle {
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
            .billing-table-total {
                background-color: #FAFAFA;

                & > * {
                    padding: $base-gutter;
                    font-size: 14px;
                    font-weight: 500;
                }
            }
        }
    }
</style>
