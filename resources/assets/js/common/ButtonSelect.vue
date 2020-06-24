<template>
    <div class="button-select__body">
        <div v-for="option in options"
             @click="()=>onOptionClick(option.value)"
             :class="{'button-select__button': true, 'active': option.value === active}">
            {{option.name}}
        </div>
    </div>
</template>

<script>
    export default {
        name: "ButtonSelect",
        props: {

        },
        methods: {
            onOptionClick(value) {
                this.active = value;
            }
        },
        data() {
            return {
                options: [
                    {
                        name: "option1",
                        value: "option1"
                    },
                    {
                        name: "option2",
                        value: "option2"
                    },
                    {
                        name: "option3",
                        value: "option3"
                    }
                ],
                active: "option1"
            }
        }
    }

</script>

<style scoped lang="scss">
    @import "app/styles/_variables.module.scss";
    .button-select__body {
        display: flex;
        border-radius: $base-gutter / 2;
        overflow: hidden;
        border: 1px solid $gray;
        .button-select__button {
            &:first-child {

            }
            &:last-child {

            }
            &:not(:last-child) {

                border-right:1px solid $gray;
            }


            flex:1;
            color: #666666;
            cursor: pointer;
            display: inline-block;
            font-size: 16px;
            font-weight: 400;
            line-height: 20px;
            padding: $base-gutter $base-gutter;
            text-align: center;
            white-space: nowrap;
        }
        .active {
            color: $white;
            background-color: $brand-green;
        }
    }
</style>