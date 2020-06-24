<template>
    <div @click="closeGusetPopup" v-show="shown" class="popup-dark-background">

        <div @click="e => e.stopPropagation()" class="base-popup">
            <div @click="closeGusetPopup" class="close-popup">
                <font-awesome-icon icon="times" class="close-icon"/>
            </div>
            <div class="appealing-text">
                Sell Top Dropshipping Products With Fast Shipping Today!
            </div>
            <a href="https://apps.shopify.com/dropwow/?utm_medium=internal&utm_source=dropwownew&utm_term=left_bar"
               target="_blank" class="orange_button">
                Add Dropwow to <img class="shopify_logo" src="/img/shopify_logo_white.png"/>
            </a>
            <div class="already_have_account">
                Already have an account?
                <a href="/login" class="log_in">
                    Log In
                </a>
            </div>
        </div>
    </div>
</template>

<script>
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import {mapMutations} from "vuex";


    export default {
        name: "GuestPopup",
        methods: {
            ...mapMutations({
                closeGusetPopup: "closeGusetPopup"
            })
        },


        data() {
            return {
                shown: true
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import "app/styles/_variables.module.scss";

    .popup-dark-background {
        position: fixed;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.53);
        margin: auto;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 6;

        .base-popup {
            .close-popup {
                position: absolute;
                background-color: $white;
                width: 30px;
                height: 30px;
                border-radius: 15px;
                top: 10px;
                right: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                .close-icon {
                    color: black;
                    font-size: 14px;
                    color: #2e2b33;

                }
            }
            position: relative;
            justify-content: center;
            align-items: center;
            background-color: #2e2b33;
            width: 400px;
            height: 300px;
            color: white;
            /*border: 1px solid #eeeeef;*/
            border-radius: $base-gutter / 2;
            box-shadow: 0 0 4px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            .appealing-text {
                display: flex;
                justify-content: center;
                align-items: center;
                width: 70%;
                margin-bottom: $base-gutter*3;

                text-align: center;
                font-size: 18px;
            }

            .orange_button {
                background-color: #ffa300;
                height: $base-gutter * 4;
                padding: $base-gutter $base-gutter*2;
                color: white;
                border-radius: $base-gutter / 2;
                display: flex;
                justify-content: center;
                align-items: center;
                cursor: pointer;
                margin-bottom: $base-gutter*3;
                font-weight: bold;
                &:hover {
                    background-color: #ff9500;;
                }

                .shopify_logo {
                    width: 70px;
                    height: 20px;
                    margin: 0 $base-gutter/2;
                }

            }
            .already_have_account {
                display: flex;
                justify-content: center;
                align-items: center;
                width: 70%;
                .log_in {
                    text-decoration: underline;
                    margin-left: $base-gutter;
                    cursor: pointer;
                }
            }
        }
    }
</style>