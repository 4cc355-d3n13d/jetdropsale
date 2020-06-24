<template>
    <div>
        <div class="settings-header">
            Orders
        </div>
        <div class="settings-subheader">
            Hold time
        </div>
        <div class="hold_time">
            Set the hold time to
            <div class="select-container">
                <select-input
                        :value="order_hold_time"
                        :onSelect="value => {changeHoldTime(value)}"
                        :options="holdTimeOptions"/>
            </div>

            hours
        </div>
    </div>
</template>

<script>
    import SelectInput from "app/js/common/SelectInput"
    import {mapMutations, mapState} from "vuex";

    export default {
        name: "Orders",
        components: {
            SelectInput
        },
        methods: {
            ...mapMutations({
                changeSettingValue: "changeSettingValue",
            }),
            changeHoldTime(value) {
                this.changeSettingValue({setting: 'order_hold_time', value: value*60})
            }
        },
        computed: {
            ...mapState({
                order_hold_time: state => parseInt(state.settings.currentSettings.order_hold_time/60)
            })
        },
        data() {
            return {
                holdTimeOptions: [
                    {
                        id: 1,
                        value: "1"
                    },
                    {
                        id: 2,
                        value: "2"
                    },
                    {
                        id: 3,
                        value: "3"
                    },
                    {
                        id: 4,
                        value: "4"
                    },
                    {
                        id: 5,
                        value: "5"
                    }
                ]
            }
        }
    }
</script>

<style scoped lang="scss">
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

    .hold_time {
        display: flex;
        align-items: center;
        font-weight: bold;
        color: $gray-dark;
        font-size: 14px;
        .select-container {
            width: 100px;
            margin: 0 $base-gutter;

        }
    }

</style>