<template>
<div class="mobile-category-action-bar" v-click-outside="() => {expandedPopup = 0}">
    <div class="buttons">
        <div class="filters">
            <font-awesome-icon icon="filter"/>
        </div>
        <div @click="e => changePopup(e, 2)" :class="['ships-from', expandedPopup === 2 ? 'active' : '']">
            Ships from
        </div>
        <div @click="e => changePopup(e, 3)" :class="['price', expandedPopup === 3 ? 'active' : '']">
            Price
        </div>
        <div @click="e => changePopup(e, 4)" :class="['subcategories', expandedPopup === 4 ? 'active' : '']">
            Subcategories
        </div>
    </div>
    <form ref="mobileFiltersForm" v-show="expandedPopup !== 0" class="popups">
        <input ref="ship_country" :value="ship_country || ''" name="ship_country" hidden type="text"/>
        <div v-show="expandedPopup === 2" class="ships-from">
            <div @click="option.handler" v-for="option in shipsFromOption" class="item">
                {{option.value}}
            </div>
        </div>
        <div v-show="expandedPopup === 3" class="price">
            <div class="from">
                <div class="label">
                    Min
                </div>
                <input name="pmin" class="filter-input" type="number"/>
            </div>
            <div class="to">
                <div class="label">
                    Max
                </div>
                <input name="pmax" class="filter-input" type="number"/>
            </div>
            <div @click="submitForm" class="btn submit">
                Submit
            </div>
        </div>
        <div v-show="expandedPopup === 4" class="subcategories">
            <a :href="category.path" v-for="category in categories" class="subcategory">
                {{category.title}}
            </a>
        </div>
    </form>


</div>

</template>

<script>
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import {ClickOutside} from "app/js/directives";
    export default {
        name: "MobileCategoryActionBar",
        methods: {
            submitForm() {

                this.$refs.mobileFiltersForm.submit()
            },
            submitShipsFrom(country) {
                this.$refs.ship_country.value = country;
                this.submitForm()
            },
            changePopup(e, index) {
                this.expandedPopup = index;
                return false;
            }
        },
        data() {
            return {
                shipsFrom: "",
                expandedPopup: 0,
                shipsFromOption: [
                    {
                        id: 1,
                        value: 'USA',
                        handler: () => {
                            this.submitShipsFrom('usa')
                        }
                    },
                    {
                        id: 2,
                        value: 'China',
                        handler: () => this.submitShipsFrom('china')
                    }

                ]
            }
        },
        props: ["categories", "minPrice", "maxPrice", "ship_country"],
        directives: {
            "click-outside": ClickOutside
        }
    }
</script>

<style lang="scss" scoped>
    @import "app/styles/_variables.module.scss";
    .mobile-category-action-bar {
        margin: 10px;
        display: none;
        width: auto;
        height: 60px;
        position: relative;
        background-color: white;
        cursor: pointer;
        margin-bottom: 20px;
        box-shadow: 0 0 4px rgba(0, 0, 0, 0.1);
        border-radius: 5px;
        @media only screen and (max-width: 768px) {
            display: flex;

        }

        .buttons {
            display: flex;
            width: 100%;
            justify-content: space-evenly;
            padding: 10px;

            & > * {
                display: flex;
            }

            .active {
                color: $brand-green;
            }
            .filters {
                justify-content: center;
                align-items: center;
            }

            .ships-from {
                justify-content: center;
                align-items: center;
            }

            .price {
                justify-content: center;
                align-items: center;
            }

            .subcategories {
                justify-content: center;
                align-items: center;
            }
        }


        .popups {
            position: absolute;
            left: 0;
            top: 100%;
            z-index: 10;
            background-color: white;
            width: 100%;
            border: 1px solid $gray-light;
            border-radius: 5px;
            padding: 10px;

            & > * {


                display: flex;
                width: 100%;

            }
            .filters {

            }

            .filter-input {
                background-color: #fff;
                border: 2px solid #f0f0f0;
                border-radius: 3px;
                display: flex;
                height: $base-gutter * 4;
                padding-left: $base-gutter;
                width: 100%;
                position: relative;
            }

            .ships-from {

                flex-direction: column;
                .item {
                    padding: 5px;

                    &:hover {
                        color: $brand-green;
                    }
                }
            }

            .price {
                flex-direction: column;
                .from {
                    display: flex;
                    .label {
                        margin-right: 10px;
                    }
                }

                .to {
                    display: flex;
                    margin-top: 10px;
                    .label {
                        margin-right: 10px;
                    }
                }

                .submit {
                    margin-top: 10px;
                }
            }

            .subcategories {
                flex-direction: column;
                .subcategory {
                    display: flex;
                    padding: 5px;
                    &:hover {
                        color: $brand-green !important;
                    }
                }
            }
        }


    }
</style>