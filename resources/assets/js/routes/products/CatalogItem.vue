<template>

    <li class="catalogue__item">
        <div v-if="pending" class="catalogue__item__pending">
            <v-icon name="spinner" class="spinner" pulse></v-icon>
        </div>
        <div v-if="!pending" v-on:click="checkProduct(id)" class="catalogue__item__checkbox_container">
            <base-checkbox
                    :checked="checked"
            />
        </div>
        <span class="catalogue__item_img"><img v-on:click="navigateToEditor" :src="image" alt=""></span>
        <span class="catalogue__item_title" :title="title"><a v-on:click="navigateToEditor" :title="title">{{title}}</a></span>
        <div class="catalogue__item_info">
            <span class="catalogue__item_cost">$ {{price}}</span>
        </div>
        <span class="catalogue__item_btn">
						<dropdown-button
                                :menuItems="getMenuItems"
                                filled
                                :onClick="navigateToEditor"
                        >
						Edit in import list
						</dropdown-button>
            <!--<div class="catalogue__item_btn__info">-->
            <!--<font-awesome-icon icon="info"/>-->
            <!--</div>-->
				
            </span>
    </li>
</template>

<script>
    import DropdownButton from "app/js/common/DropdownButton";
    import BaseCheckbox from "app/js/common/BaseCheckbox";
    import {mapState, mapMutations, mapActions} from "vuex";
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import Icon from 'vue-awesome/components/Icon';
    import 'vue-awesome/icons/spinner';

    export default {
        name: "CatalogItem",
        data() {
            return {
                menuItems: [
                    {
                        id: 2,
                        value: "Original product",
                        href: "/product/" + this.product_id,
                        target: ""
                    },
                    {
                        id: 3,
                        value: "Delete",
                        handler: () => {
                            if (this.status === 10) {
                                this.showRemovalPopup({
                                    "okHandler": () => this.deleteMyProduct(this.id),
                                    "cancelHandler": () => this.hideRemovalPopup(),
                                    "text": "This product will be deleted from Shopify as well"
                                })
                            } else {
                                this.deleteMyProduct(this.id)
                            }
                        }
                    },
                    {
                        id: 4,
                        value: "Duplicate",
                        handler: () => {
                            this.duplicateProduct(this.id)
                        }
                    }
                ]
            }
        },
        computed: {
            ...mapState({
                shop: state => state.products.shop
            }),
            getMenuItems() {
                if (this.status === 10) {
                    this.menuItems.unshift({
                        id: 1,
                        value: "Edit in Shopify",
                        href: "http://" + this.shop + "/admin/products/" + this.shopify_id,
                        target: "_blank",
                    });
                }
                return this.menuItems;
            }
        },
        components: {
            DropdownButton,
            BaseCheckbox,
            FontAwesomeIcon,
            'v-icon': Icon
        },
        methods: {
            ...mapActions({
                deleteMyProduct: "deleteMyProduct",
                duplicateProduct: "duplicateProduct"
            }),
            ...mapMutations({
                checkProduct: "checkProduct",
                hideRemovalPopup: "hideRemovalPopup",
                showRemovalPopup: "showRemovalPopup",
            }),
            navigateToEditor(e) {
                e.stopPropagation();
                this.$router.push("/my/product_edit/" + this.id);
            }
        },
        props: {
            checked: {
                type: Boolean
            },
            id: {
                type: Number
            },
            product_id: {
                type: Number
            },
            shopify_id: {
                type: Number
            },
            price: {
                type: String
            },
            amount: {
                type: Number
            },
            title: {
                type: String
            },
            image: {
                type: String
            },
            pending: {
                type: Boolean
            },
            status: {
                type: Number
            }
        }
    };
</script>

<style lang="scss">
    @import "app/styles/_variables.module.scss";

    .catalogue__item {
        background-color: #fff;
        border-radius: $base-gutter / 2;
        position: relative;
        box-shadow: 0 0 4px rgba(0, 0, 0, 0.1);
        float: left;
        margin: $base-gutter;
        padding-bottom: $base-gutter * 2;
        width: calc(#{100% / 5} - #{$base-gutter * 2});

        @media only screen and (max-width: 1700px) {
            width: calc(#{100% / 4} - #{$base-gutter * 2});
        }

        @media only screen and (max-width: 880px) {
            width: calc(#{100% / 2} - #{$base-gutter * 2});
        }
        @media only screen and (max-width: 540px) {
            width: calc(#{100%} - #{$base-gutter * 2});
        }

        &__pending {
            position: absolute;
            background-color: white;
            opacity: 0.8;
            height: 100%;
            width: 100%;
            z-index: 2;
            display: flex !important;
            justify-content: center;
            align-items: center;
            .spinner {
                position: relative;
                width: 50px;
                height: 50px;
                color: $gray-dark;
                opacity: 1;
            }
        }

        &__checkbox_container {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 0px !important;
            // width: 20px!important;
        }

        & > * {
            display: block;
            margin: $base-gutter * 2 0;
            padding: 0 $base-gutter * 2;
            // width: 100%;
            &:first-child {
                margin-top: 0;
            }
            &:last-child {
                margin-bottom: 0;
            }
        }
        &_img {
            align-items: center;
            border-bottom: 1px solid #eaeaea;
            display: flex;
            height: $base-gutter * 27;
            justify-content: center;
            padding: $base-gutter * 2;
            cursor: pointer;
            @media only screen and (max-width: 1368px) {
                height: $base-gutter * 20;
            }

            img {
                height: auto;
                max-height: 250px;
                max-width: 220px;
                @media only screen and (max-width: 1368px) {
                    max-height: 160px;
                    max-width: 160px;
                }
                @media only screen and (max-width: 880px) {
                    max-height: 200px;
                    max-width: 220px;
                }
            }
        }
        &_title {
            font-size: 14px;
            line-height: 20px;
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
            cursor: pointer;
        }
        &_info {
            display: flex;
            justify-content: space-between;
        }
        &_cost {
            font-size: 16px;
            font-weight: 600;
            line-height: 20px;
        }
        &_shiping {
            font-size: 14px;
            line-height: 20px;
        }
        &_feedback {
            border-top: 1px solid #eaeaea;
            padding-top: $base-gutter;
            &_row {
                align-items: center;
                display: flex;
                justify-content: space-between;
                padding: $base-gutter 0;
                @media only screen and (max-width: 1200px) {
                    align-items: flex-start;
                    flex-direction: column;
                    padding: 0;
                    & > * {
                        padding: $base-gutter 0;
                    }
                }
            }
        }
        &_raiting {
            margin-right: $base-gutter * 2;
            &-item {
                color: #f8b044;
            }
        }
        &_reviews {
            color: #999;
            font-size: 12px;
            i {
                color: #1f8d5f;
            }
        }
        &_btn {
            align-items: center;
            display: flex;
            .btn {
                flex: 1;
                width: 100%;
            }
            .info {
                margin-left: $base-gutter;
            }

            &__info {
                background-color: $gray;
                color: $white;
                width: 14px;
                height: 13px;
                border-radius: 50%;
                display: flex;
                justify-content: center;
                align-items: center;
                margin-left: $base-gutter;
                min-height: fit-content;
                min-width: fit-content;
                cursor: pointer;
                & > * {
                    // margin-right: 0.5px;
                    width: 8px;
                    height: 8px;
                }
            }

            &__content {
                // & > * {
                // 	display: inline-block;
                // }
                height: 100%;

                display: flex;
                justify-content: space-between;

                &__dropdown-toggle {
                    height: 100%;
                    border-left: 1px solid $white;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: fit-content;
                    min-width: fit-content;
                }
            }
        }

        .billboard-tile__item & {
            box-shadow: none;
            justify-content: flex-start;
            margin: -$base-gutter;
            text-align: left;
            width: calc(100% + 20px);
            @media only screen and (max-width: 1368px) {
            }
            .catalogue__item_img {
                height: $base-gutter * 24;
                @media only screen and (max-width: 1368px) {
                    height: auto;
                }
            }
        }
    }
</style>
