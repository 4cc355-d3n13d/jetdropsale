<template>
    <div

            class="product-mock">
        <div class="product-mock__upper_child">
            <div class="image-mock">

            </div>
        </div>
        <div class="product-mock__bottom_child">
            <div class="title-mock">

            </div>
            <div class="price-mock">

            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "MockProduct"
    }
</script>


<style lang="scss">
    @import "app/styles/_variables.module.scss";

    .product-mock {
        border: 2px dashed $gray-light;
        background-color: #fff;
        border-radius: $base-gutter / 2;
        position: relative;
        float: left;
        margin: $base-gutter;
        padding-bottom: $base-gutter * 2;
        height: 400px;
        flex-wrap: wrap;

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

        &__upper_child {
            height: 70%;
            width: 100%;
            padding: $base-gutter*2;
            border-bottom: 2px dashed $gray-light;
            .image-mock {
                display: flex;
                width: 100%;
                height: 100%;
                border: 2px dashed $gray-light;

            }
        }
        &__bottom_child {
            height: 30%;
            width: 100%;
            display: flex;
            flex-direction: column;
            padding: $base-gutter;
            .title-mock {
                margin-top: 20px;
                width: 70%;
                background-color: $gray-light;
                border-radius: 10px;
                height: 10px;
            }
            .price-mock {
                background-color: $gray-light;
                border-radius: 10px;
                height: 10px;
                width: 25%;
                margin-top: 20px;
            }
        }
    }
</style>