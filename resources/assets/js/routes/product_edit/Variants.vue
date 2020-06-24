<template>
    <div class="variants">
        <div class="variants__header">
            Variants
        </div>
        <div class="variants__selectors">
            <div v-for="(value, key) in features" :key="key" class="variants__selectors__feature-container">
                <div class="name">
                    {{getOptionNameById(key)}}:
                </div>
                <div v-for="option in value" :key="option"
                     :class="['options']">
                    <base-button :onClick="()=>toggleCombinationsByOptionValue({key, value: option})"
                                 :gray="excludedOptionValues.includes(option)"
                                 :filled="!excludedOptionValues.includes(option)" small>
                        {{getOptionValueById(option)}}
                        <font-awesome-icon
                                :icon="excludedOptionValues.includes(option) ? 'plus' : 'times'"
                                class="remove_option_icon"/>
                    </base-button>
                </div>
            </div>
        </div>
        <div class="table-container">
            <table class="variants__table">
                <thead class="variants__table__header">
                <td v-if="features && allCombinations.length !== 0" v-for="(feature_value_id, feature_id) in features"
                    :key="feature_id">
                    {{feature_id && getOptionNameById(feature_id)}}
                </td>
                <td>
                    Price
                </td>
                <td>
                    Amount
                </td>
                <td>

                </td>
                </thead>
                <tbody v-if="allCombinations.length !== 0" class="variants__table__body">
                <tr :class="{'variants__table__body__row' : true, 'disabled': combination.disabled || false}"
                    v-for="(combination, index) in combinations"

                    :key="index">
                    <td class="option-td" v-if="features" v-for="(feature_value_id, feature_id) in features"
                        :key="feature_id">
                        <!--<input-->

                        <!--:disabled="combination.disabled"-->
                        <!--v-if="feature_id && combination.variant && combination.variant[feature_id]"-->
                        <!--type="text" class="text-input"-->
                        <!--:value="getValueBySKU(combination.variant[feature_id])"/>-->

                        {{getValueBySKU(combination.variant[feature_id])}}


                    </td>


                    <td><input type="number"
                               @input="(e)=>onVariantPriceChange({id:combination.id, value: e.target.value})"
                               :disabled="combination.disabled || false"
                               :value="combination.price"
                               class="text-input price-input">
                    </td>
                    <td>
                        <input type="number"
                               @input="(e)=>onVariantAmountChange({id: combination.id, value: e.target.value})"
                               :disabled="combination.disabled"
                               :value="combination.amount"
                               class="text-input"/>

                    </td>
                    <td class="close-button-container">
                        <div class="button-container">
                            <base-button
                                    small
                                    :filled="!combination.disabled"
                                    :gray="combination.disabled"
                                    :onClick="()=>excludeCombination({id: combination.id, variant: combination.variant})">
                                <font-awesome-icon
                                        :icon="combination.disabled ? 'plus' : 'times'"/>
                            </base-button>
                        </div>
                    </td>
                </tr>

                </tbody>
                <tbody v-if="allCombinations.length === 0">
                <tr class="variants__table__body__row">
                    <td>
                        <input type="number"
                               @input="(e)=>onProductPriceChange({value: e.target.value})"
                               v-model="price"
                               class="text-input price-input">
                    </td>
                    <td>
                        <input type="number"
                               @input="(e)=>onProductAmountChange({value: e.target.value})"
                               v-model="amount"
                               class="text-input"/>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <popup v-if="popup.show"
               :onOkPressed="popup.onOkPressed"
               :onCancelPressed="popup.onCancelPressed"
        >
            {{this.popup.text}}
        </popup>

    </div>

</template>

<script>
    import BaseButton from "app/js/common/BaseButton";
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import Popup from "app/js/common/Popup";
    import {mapState, mapMutations, mapGetters} from "vuex";

    export default {
        components: {
            BaseButton,
            Popup
        },
        data() {
            return {
                popup: {
                    text: "",
                    onOkPressed: f => f,
                    onCancelPressed: f => f,
                    show: false
                }

            }
        },

        computed: {
            ...mapState({
                // amount: state => state.product.amount,
                // price: state => state.product.price,
                allCombinations: state => state.product.combinations,
                options: state => state.product.options,
                excludedOptionValues: state => state.product.excludedOptionValues
            }),

            ...mapGetters({
                combinations: "getCombinationsExceptExcluded"
            }),

            features() {

                return this.$store.getters.nestedProductOptions
            },
            price: {
                get() {
                    return this.$store.state.product.price;
                },
                set(value) {
                    this.onProductPriceChange(value);
                }
            },
            amount: {
                get() {
                    return this.$store.state.product.amount;
                },
                set(value) {
                    this.onProductAmountChange(value);
                }
            }

            // combinations() {
            //     return this.$store.getters.allOptionCombinations
            // },

        },
        methods: {
            ...mapMutations({
                excludeCombination: "excludeCombination",
                toggleCombinationsByOptionValue: "toggleCombinationsByOptionValue",
                onVariantPriceChange: "onVariantPriceChange",
                onVariantAmountChange: "onVariantAmountChange",
                onProductPriceChange: "onProductPriceChange",
                onProductAmountChange: "onProductAmountChange"
            }),
            showWarningPopup(obj) {
                this.popup = {
                    text: `Remove all the products with
                    ${this.getOptionNameById(obj.key)} ${this.getOptionValueById(obj.value)}?`,
                    onOkPressed: () => {
                        this.clearPopup();
                        this.toggleCombinationsByOptionValue(obj)
                    },
                    onCancelPressed: () => this.clearPopup(),
                    show: true
                };
            },
            clearPopup() {
                this.popup = {
                    text: "",
                    onOkPressed: f => f,
                    onCancelPressed: f => f,
                    show: false
                }
            },
            getOptionNameById(id) {
                const index = this.options.findIndex(option => {
                    return option.ali_option_id === +id
                });
                return this.options[index].name;
            },
            getOptionValueById(id) {

                const index = this.options.findIndex(option => {
                    return option.ali_sku === +id
                });
                if (this.options[index] && this.options[index].value) return this.options[index].value;
                return false;


            },
            getValueBySKU(id) {
                const index = this.options.findIndex(option => {
                    return option.ali_sku === +id
                });

                return this.options[index].value;
            }
        }
    }

</script>

<style lang="scss" scoped>
    @import "app/styles/_variables.module.scss";

    .variants {
        width: auto;
        margin-bottom: $base-gutter*4;
        &__header {
            font-size: 1.6em;
            margin-bottom: $base-gutter*2;
        }

        &__selectors {
            display: flex;
            flex-direction: column;
            &__feature-container {
                display: flex;
                flex-wrap: wrap;
                align-items: center;

                .name {
                    padding: $base-gutter;
                }

                .options {
                    & > * {
                        margin-top: 5px;
                        margin-right: $base-gutter;
                    }
                    .remove_option_icon {
                        margin-left: $base-gutter;
                    }
                }

            }
        }

        .table-container {
            max-height: 488px;
            overflow-y: auto;
            width: 100%;
            border: 1px solid $gray-light;
            margin: $base-gutter*2 0;
            .variants__table {
                width: 100%;
                border-collapse: collapse;
                @media only screen and (max-width: 768px) {
                    font-size: 12px;
                }

                &__header {
                    background-color: $gray-light;
                    color: $gray-dark;
                    & > td {
                        padding: $base-gutter;
                    }
                }
                &__body {
                    &__row {
                        border-bottom: 1px solid $gray-light;
                        height: 60px;

                        &:last-child {
                            border-bottom: none !important;
                        }

                        .option-td {
                            flex: 1;
                            color: $gray-dark;
                        }

                        .close-button-container {
                            display: flex;
                            justify-content: center;
                            .button-container {
                                @media only screen and (max-width: 768px) {
                                    padding: $base-gutter*1.5 0;
                                }
                            }
                            & * > {

                            }
                        }

                        & > td {
                            padding: $base-gutter;
                            @media only screen and (max-width: 768px) {
                                padding: 0;
                            }
                            .text-input {
                                border: 1px solid #eaeaea;
                                border-radius: 2px;
                                height: 40px;
                                width: 100%;
                                color: gray;
                                padding: 10px;
                                font-size: .9em;
                                @media only screen and (max-width: 768px) {
                                    padding: 5px;
                                }

                            }
                            .price-input {
                                color: $brand-green;
                                font-weight: bold;
                            }
                        }
                        // background-color: red;
                    }

                    .disabled {
                        background-color: rgba(205, 205, 205, 0.24);
                    }
                }

            }
        }

    }
</style>
