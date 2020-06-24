<template>
    <div class="text-select__container">
        <div class="text-select">
            <div class="text">
                <input @input="OnTextChange" :value="textValue" class="number-input"/>
            </div>
            <div @click="onSelectClick" class="select">
                <div class="value">
                    {{optionNameByValue}}
                </div>
                <div class="arrow">
                    <font-awesome-icon icon="angle-down"></font-awesome-icon>
                </div>
            </div>
            <div v-show="expanded" class="dropdown">
                <div
                        :key="option.value"
                        v-for="option in options"
                        @click="()=>OnSelect(option.value)"
                        class="menu-item">
                    {{option.name}}
                </div>
            </div>
        </div>
        <info/>
    </div>

</template>

<script>
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import Info from "app/js/common/Info"

    export default {
        name: "TextSelect",
        components: {
            Info
        },
        data() {
            return {
                expanded: false,
            }
        },

        computed: {
            optionNameByValue() {
                const index = this.options.findIndex(el => el.value === this.selectValue);

                if (this.options[index] && this.options[index].name) {
                    return this.options[index].name;
                } else {
                    return ""
                }

            }
        },

        props: {
            options: {
                type: Array,
                default: []
            },
            textValue: {

            },
            selectValue: {
                type: String
            },
            onSelect: {
                type: Function,
                default: f=>f
            },
            onTextChange: {
                type: Function,
                default: f=>f
            },

        },

        methods: {
            onSelectClick() {
                this.expanded = !this.expanded
            },
            OnSelect(value) {
                this.expanded = false;
                this.onSelect(value)
            },
            OnTextChange(e) {
                this.onTextChange(e.target.value)
            }
        }
    }
</script>


<style scoped lang="scss">
    @import "app/styles/_variables.module.scss";

    /*.number-input {*/
        /*input::-webkit-outer-spin-button,*/
        /*input::-webkit-inner-spin-button {*/
            /*!* display: none; <- Crashes Chrome on hover *!*/
            /*-webkit-appearance: none;*/
            /*margin: 0; !* <-- Apparently some margin are still there even though it's hidden *!*/
        /*}*/

        /*&:hover {*/
            /*input::-webkit-outer-spin-button,*/
            /*input::-webkit-inner-spin-button {*/
                /*!* display: none; <- Crashes Chrome on hover *!*/
                /*-webkit-appearance: none;*/
                /*margin: 0; !* <-- Apparently some margin are still there even though it's hidden *!*/
            /*}*/
        /*}*/
    /*}*/

    .text-select__container {
        display: flex;
        align-items: center;

        .text-select {
            margin-right: $base-gutter;
            display: flex;
            border-radius: $round-corners;
            border: 1px solid $gray-light;
            max-width: fit-content;
            /*overflow: hidden;*/
            position: relative;
            .text {
                display: flex;
                align-items: center;
                justify-content: center;
                padding: $base-gutter;
                overflow: hidden;


                & > input {
                    border: none;
                    color: $gray-dark;
                    font-size: 16px;
                    max-width: 60px;


                }
            }

            .select {
                min-width: 120px;
                display: flex;
                background-color: $brand-green;
                color: $white;
                justify-content: center;
                align-items: center;
                padding: $base-gutter;
                cursor: pointer;
                font-weight: bold;
                font-size: 14px;
                border-radius: 0 $base-gutter/2 $base-gutter/2 0;
                .value {
                    margin-right: $base-gutter;
                    width: 80%;
                }
                .arrow {

                }
            }

            .dropdown {
                background-color: #fff;
                border: 1px solid #eeeeef;
                border-radius: $base-gutter / 2;
                box-shadow: 0 0 4px rgba(0, 0, 0, 0.1);
                color: #666;
                font-size: 13px;
                font-weight: 400;
                line-height: 18px;
                z-index: 1;
                // overflow: hidden;
                // padding: $base-gutter * 2;
                position: absolute;
                width: $base-gutter * 20;
                top: 130%;
                right: 0;
                &:before {
                    content: "";
                    position: absolute;
                    top: -4px;
                    left: 90%;
                    width: 0;
                    height: 0;
                    border: $arrow-size + 3px solid transparent;
                    border-bottom-color: rgba(128, 128, 128, 0.123);
                    border-top: 0;
                    margin-left: -$arrow-size;
                    margin-top: -$arrow-size - 1px;
                }
                &:after {
                    content: "";
                    position: absolute;
                    top: -3px;
                    left: 90%;
                    width: 0;
                    height: 0;
                    border: $arrow-size + 3px solid transparent;
                    border-bottom-color: $white;
                    border-top: 0;
                    margin-left: -$arrow-size;
                    margin-top: -$arrow-size;
                }
                &.l-c {
                    left: calc(100% + #{$base-gutter});
                    top: -30px;
                }

                .menu-item {
                    cursor: pointer;
                    padding: $base-gutter;
                    &:not(:last-child) {
                        border-bottom: 1px solid rgba(218, 218, 218, 0.37);
                    }

                    &:first-child {
                        border-radius: 3px 3px 0 0;
                    }
                    &:last-child {
                        border-radius: 0 0 3px 3px;
                    }
                    &:hover {
                        background-color: $gray;
                        color: $white;
                    }
                }
            }
        }
    }
</style>