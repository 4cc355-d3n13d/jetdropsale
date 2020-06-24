<template>

    <div


            class="user-alerts__container">
        <transition-group tag="div" name="fade">
            <div
                    :key="index"
                    v-for="(notification, index) in userNotifications"
                    :class="['user-alert', notification.type]">
                <div class="user-notification-text">
                    {{notification.text}}
                </div>
                <div
                        @click="()=>removeUserNotification(notification.id)"
                        :class="{'user-alert__close-icon__container': true}">
                    <font-awesome-icon icon="times"
                                       :class="['user-alert__close-icon', notification.type+'-icon']"></font-awesome-icon>
                </div>

            </div>
        </transition-group>

    </div>

</template>

<script>
    import {mapState, mapMutations} from "vuex";
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

    export default {
        name: "UserAlert",
        computed: {
            ...mapState({
                userNotifications: state => state.userNotifications
            })
        },
        methods: {
            ...mapMutations({
                removeUserNotification: "removeUserNotification",
            })
        }
    }
</script>

<style lang="scss" scoped>
    @import "app/styles/_variables.module.scss";



    .fade-enter-active, .fade-leave-active {
        transition: all .5s;
    }
    .fade-enter, .fade-leave-to /* .list-leave-active below version 2.1.8 */ {
        opacity: 0;

    }

    .user-alerts__container {
        opacity: .8;
        display: flex;
        flex-direction: column;
        position: fixed;
        top: 20px;
        right: 0;
        z-index: 11;
        .user-alert {

            min-height: 75px;
            width: 400px;
            position: relative;
            margin-top: 10px;
            color: white;
            display: flex;
            padding: $base-gutter $base-gutter * 3;
            align-items: center;
            border-radius: 5px;


            .user-notification-text {
                display: inline-block;
                width: 100%;
                height: auto;
                word-wrap: break-word;
            }

            &__close-icon__container {
                position: absolute;
                right: 10px;
                top: 10px;
                height: 16px;
                width: 16px;
                background-color: white;
                display: flex;
                justify-content: center;
                align-items: center;
                border-radius: 50%;
                cursor: pointer;

                .user-alert__close-icon {

                    font-size: 10px;
                }

                .error-icon {
                    color: #C62434;
                }

                .success-icon {
                    color: #1F8D5F;

                }
            }
        }

        .error {
            background-color: #C62434;
        }

        .success {
            background-color: #1F8D5F;

        }

    }


</style>