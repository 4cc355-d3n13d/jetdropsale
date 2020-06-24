<template>
    <div class="page__body">
        <user-alert/>
        <page-header/>
        <!-- <router-link to="/spa/orders">Go to orders</router-link>
        <router-link to="/spa/alerts">Go to alerts</router-link> -->
       <div class="body-container">
           <router-view></router-view>
       </div>
        <page-footer/>


        <!-- <imported-products/> -->
    </div>
</template>

<script>
    import PageHeader from "app/js/components/PageHeader.vue";
    import UserAlert from "app/js/common/UserAlert.vue";
    import PageFooter from "app/js/components/PageFooter.vue"


    export default {
        name: "PageBody",
        components: {
            PageHeader,
            UserAlert,
            PageFooter
            // ImportedProducts
        }
    };
</script>

<style lang="scss">
    @import "app/styles/_variables.module.scss";

    .page__body {
        min-width: 400px;
        background-color: $bg-color;
        color: #333;
        flex: 1;
        max-width: 100%;
        .body-container {
            min-height: calc(100vh - 200px);
        }
        @media only screen and (max-width: 768px) {
            padding-top: $base-gutter * 5;
            margin-left: 0 !important;
        }

        @media only screen and (max-width: 1200px) {
            margin-left: 70px;
        }

        & > * {
            margin: $base-gutter * 3;
            &:first-child {
                margin-top: 0;
            }
            &:last-child {
                margin-bottom: 0;
            }
            @media only screen and (max-width: 900px) {
                margin: $base-gutter * 2;
            }
        }
    }
</style>
