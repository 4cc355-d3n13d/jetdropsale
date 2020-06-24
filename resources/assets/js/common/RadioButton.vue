<template>
    <div class="radio-button">
        <label
                @click="()=>onOptionClick(option.value)"
                v-for="option in options"
                :class="{'radio-button__option':true, 'active': false}">
             <div class="circle">
                 <div class="inner-circle" v-if="option.value === active">

                 </div>
             </div>
            <div class="name-container">
                {{option.name}}
            </div>
        </label>
    </div>
</template>

<script>
    export default {
        name: "RadioButton",
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
        },
        methods: {
            onOptionClick(value) {
                this.active = value;
            }
        }
    }
</script>

<style scoped lang="scss">
    @import "app/styles/_variables.module.scss";
    .radio-button {
        display: flex;
        .radio-button__option {
            display: flex;
            cursor: pointer;
            margin: $base-gutter $base-gutter*3;
            align-items: center;
            justify-content: center;
            .circle {
                margin: 0 $base-gutter/2;
                display: flex;
                justify-content: center;
                align-items: center;
                border-radius: 50%;
                height: 20px;
                width: 20px;
                border: 1px solid $gray;
                cursor: pointer;
                .inner-circle {
                    background-color: $brand-green;
                    height: 10px;
                    width: 10px;
                    border-radius: 50%;
                }
            }

            .name-container {

            }
        }

    }

</style>