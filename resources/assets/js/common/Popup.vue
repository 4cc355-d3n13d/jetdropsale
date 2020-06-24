x<template>
    <div class="popup-dark-background">
        <div class="base-popup">
            <div @click="onCancelPressed"
                 class="close-icon-container">
                <font-awesome-icon icon="times" class="close-icon"></font-awesome-icon>
            </div>
            <slot></slot>
            <div class="buttons-holder">
                <base-button :onClick="onOkPressed" small filled>
                    {{okText}}
                </base-button>
                <base-button :onClick="onCancelPressed" small>
                    {{cancelText}}
                </base-button>
            </div>
        </div>
    </div>
</template>

<script>
    import BaseButton from "app/js/common/BaseButton"
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

    export default {
        name: "Popup",
        components: {BaseButton},
        props: {
            onOkPressed: {
                type: Function,
                default: f => f
            },
            onCancelPressed: {
                type: Function,
                default: f => f
            },
            okText: {
                type: String,
                default: "Ok"
            },
            cancelText: {
                type: String,
                default: "Cancel"
            },
            okButtonClass: {
                type: String,
                default: "btn"
            },
            cancelButtonClass: {
                type: String,
                default: "btn"
            }

        }
    };
</script>

<style lang="scss" scoped>
    @import "app/styles/_variables.module.scss";

    .popup-dark-background {
        position: fixed;
        width: auto;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.53);
        margin: auto;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        display: flex;
        z-index: 3;
        .base-popup {
            background-color: #fff;
            border: 1px solid #eeeeef;
            border-radius: $base-gutter / 2;
            box-shadow: 0 0 4px rgba(0, 0, 0, 0.1);
            color: #666;
            display: flex;
            flex-direction: column;

            .close-icon-container {
                background-color: #1F8D5F;
                position: absolute;
                right: 8px;
                top: 8px;
                height: 16px;
                width: 16px;

                display: flex;
                justify-content: center;
                align-items: center;
                border-radius: 50%;
                cursor: pointer;
                .close-icon {
                    height: 10px;
                    width: 10px;
                    color: white;
                }
            }

            padding: $base-gutter * 2.5;
            position: relative;
            margin: auto;
            max-height: fit-content;
            .close-popup-button {
                position: absolute;
                right: $base-gutter;
                top: $base-gutter;
            }
            .buttons-holder {
                width: auto;
                display: flex;
                justify-content: center;
                margin-top: $base-gutter*2;
                & > * {
                    margin: 0 $base-gutter;
                }
            }
        }
    }

</style>
