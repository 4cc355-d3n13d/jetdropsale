<template>
    <div class="page">
        <mobile-sidebar
            :isExpanded="menuExpanded"
        />
        <page-sidebar
            :connectedCount="connected"
            :nonConnectedCount="non_connected"
            :allCount="all"
            :menuExpanded="menuExpanded"
            :userName="userName"
        />
        <page-body/>
    </div>
</template>

<script>
    import MobileSidebar from "app/js/components/MobileSidebar"
    import PageSidebar from "app/js/components/PageSidebar.vue";
    import PageBody from "app/js/components/PageBody";
    import {mapState, mapActions, mapMutations} from "vuex";

    export default {
        name: "App",
        components: {
            PageSidebar,
            PageBody,
            MobileSidebar
        },
        computed: {
            ...mapState({
                menuExpanded: state => state.menuExpanded,
                userName: state => state.user.name,
                all: state => state.products.count.total,
                connected: state => state.products.count.connected,
                non_connected: state => state.products.count.non_connected,
            })
        },
        methods: {
            ...mapActions({
                getUserInfo: "getUserInfo",
                getProducts: "getProducts",
                getProductsCount: "getProductsCount"
            }),
            ...mapMutations({
                setCurrentProductStatus: "setCurrentProductStatus",
            })
        },
        created() {

            if (-1 !== ["connected", "non_connected"].indexOf(this.$route.name)) {
                this.setCurrentProductStatus(this.$route.name);
            } else {
                this.setCurrentProductStatus("all");
            }

            this.getUserInfo();
            // this.getProducts();
            this.getProductsCount();
        }

    };
</script>

<style lang="scss">

    @import "app/styles/_variables.module.scss";
    @import "app/styles/_defaults.module.scss";

    .page {
        background-color: $test;
        display: flex;
        min-height: 100%;
        padding-left: $sidebar-size;
        width: 100%;
        @media only screen and (max-width: 1200px) {
            padding-left: 0;
        }

        &__logo {
            display: inline-block;
            padding: $base-gutter * 2;

        }

    }

</style>





