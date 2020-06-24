<template>
    <div>
        <div class="settings-header">
            Import
        </div>
        <div class="settings-subheader">
            Global pricing rules
        </div>
        <!--<button-select/>-->
        <!--<radio-button/>-->
        <!--<text-select/>-->
        <div class="settings__product-cost">
            <div class="label">
                <div class="label-header">
                    Product cost
                </div>
                <div class="label-description">
                    Your product price
                </div>
            </div>
            <div class="sign">
                <font-awesome-icon class="multiply-icon" :icon="gpr_type === 'm' ? 'times' : 'plus'"/>
            </div>
            <text-select
                    :onTextChange="changeProductCostValue"
                    :textValue="gpr_rate"
                    :onSelect="changeProductCostType"
                    :selectValue="gpr_type"
                    :options="productCostOptions"/>
        </div>
        <!--<div class="settings__assign-cents__title">-->
            <!--<div class="label-header">-->
                <!--Assign cents-->
            <!--</div>-->
            <!--<div class="label-description">-->
                <!--You can set a specific cent value for your retail price.-->
                <!--We will use this value when forming the final price for your items-->
                <!--(e.g., if you want the cost of your product to be XX.99 then add 99 to the fields below).-->
            <!--</div>-->
        <!--</div>-->
        <!--<div class="settings__assign-cents">-->
            <!--<checkbox/>-->
            <!--<div class="label">-->
                <!--Assign cents-->
            <!--</div>-->
            <!--<text-input/>-->
        <!--</div>-->
    </div>
</template>

<script>
    import ButtonSelect from "app/js/common/ButtonSelect";
    import RadioButton from "app/js/common/RadioButton";
    import TextSelect from "app/js/common/TextSelect";
    import Checkbox from "app/js/common/BaseCheckbox";
    import TextInput from "app/js/common/TextInput"
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import {mapState, mapMutations} from "vuex";

    export default {
        name: "Import",
        components: {
            TextSelect,
            ButtonSelect,
            RadioButton,
            Checkbox,
            TextInput
        },
        computed: {
            ...mapState({
                gpr_type: state => state.settings.currentSettings.gpr_type,
                gpr_rate: state => state.settings.currentSettings.gpr_rate
            })
        },
        methods: {
            ...mapMutations({
                changeSettingValue: "changeSettingValue",
            }),
            changeProductCostValue(value) {
                this.changeSettingValue({setting: "gpr_rate", value})
            },
            changeProductCostType(value) {
                this.changeSettingValue({setting: "gpr_type", value})
            }
        },
        data() {
            return {
                productCostOptions: [
                    {
                        name: "Multiplier",
                        value: "m"
                    },
                    {
                        name: "Fixed",
                        value: "f"
                    }
                ]
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import "app/styles/_variables.module.scss";

    .settings-header {
        font-size: 24px;
        width: 100%;
        border-bottom: 1px solid $gray-light;
        padding-bottom: $base-gutter/2;
        margin-bottom: $base-gutter*2;
    }

    .settings-subheader {
        width: 100%;
        font-size: 18px;
        margin-bottom: $base-gutter*2;
    }

    .settings__product-cost {
        display: flex;
        align-items: center;
        margin-bottom: $base-gutter*2;
        .label {
            display: flex;
            flex-direction: column;
            .label-header {
                font-weight: bold;
                color: $gray-dark;
                font-size: 14px;
            }

            .label-description {
                color: $gray-dark;
                font-size: 13px;

            }
        }
        .sign {
            height: 100%;
            padding: 0 $base-gutter*2;
            display: flex;
            justify-content: center;
            align-items: center;
            .multiply-icon {
                height: 14px;
                width: 14px;
                font-weight: bold;
                color: $gray;
            }

        }
    }

    .settings__assign-cents__title {
        margin-bottom: $base-gutter*2;
        width: 60%;
        .label-header {
            font-weight: bold;
            color: $gray-dark;
            font-size: 14px;
        }

        .label-description {
            color: $gray-dark;
            font-size: 13px;
        }
    }

    .settings__assign-cents {
        display: flex;
        align-items: center;
        /*width: 40%;*/
        & > * {
            margin-right: $base-gutter;
        }

        .label {
            font-size: 14px;
            color: $gray-dark;
        }

    }

</style>