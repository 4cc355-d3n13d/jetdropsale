<template>
    <div class="product-container">
        <breadcrumps/>
        <category-navigation/>
        <action-panel
            :product_status="product_status"
        />
        <catalog/>
        <!--<pagination-->
            <!--v-if="totalPage > 1"-->
            <!--:total="totalPage"-->
            <!--:current="currentPage"-->
            <!--:onPageChange="(page)=>onPageChange(page)"-->
        <!--/>-->
        <paginate
             v-if="(totalPage > 1) && $mq !=='sm'"
            :page-count="totalPage"
            :click-handler="(page)=>onPageChange(page)"
            :page-range="7"
            :container-class="'pagination'"
            :page-class="'pages-container__page'"
            :active-class="'active-page'"
            :prev-link-class="'arrow'"
            :next-link-class="'arrow'"
            :margin-pages="1"
            :prev-text="'<i class=\'fas fa-chevron-left\'></i>'"
            :next-text="'<i class=\'fas fa-chevron-right\'></i>'"
        />
        <paginate
                v-if="(totalPage > 1) && $mq ==='sm'"
                :page-count="totalPage"
                :click-handler="(page)=>onPageChange(page)"
                :page-range="1"
                :container-class="'pagination'"
                :page-class="'pages-container__page'"
                :active-class="'active-page'"
                :prev-link-class="'arrow'"
                :next-link-class="'arrow'"
                :margin-pages="1"
                :prev-text="'<i class=\'fas fa-chevron-left\'></i>'"
                :next-text="'<i class=\'fas fa-chevron-right\'></i>'"
        />

        <!--<mobile-pagination-->
            <!--total="totalPage"-->
            <!--current=""-->
        <!--/>-->
        <popup
                v-if="popup.shown"
                :onOkPressed="popup.okHandler"
                :onCancelPressed="popup.cancelHandler"
                :okText="'Delete'"
        >
            {{popup.text}}
        </popup>
    </div>

</template>

<script>
    import Breadcrumps from "./Breadcrumps";
    import Pagination from "app/js/common/Pagination";
    import Paginate from 'vuejs-paginate'
    import Header from "./Header";
    import ActionPanel from "./ActionPanel"
    import Catalog from "./Catalog"
    import CategoryNavigation from "./CategoryNavigation";
    import {mapActions, mapMutations, mapState} from "vuex";
    import MobilePagination from "app/js/common/MobilePagination"
    // import Button from "@/common/Button";
    import Popup from "app/js/common/Popup";
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

    export default {
        components: {
            ActionPanel,
            Catalog,
            CategoryNavigation,
            Breadcrumps,
            Pagination,
            Popup,
            Paginate,
            MobilePagination
        },
        data() {
            return {
                left: "<i class=\"fas fa-angle-left\"></i>"
            }
        },
        props: {
            product_status: {
                type: String,
                default: "all"
            }
        },
        watch: {
            product_status(val) {
                this.setCurrentProductStatus(val);
                this.getProducts();
            }
        },
        computed: {
            ...mapState({
                totalPage: state => state.products.pagination[state.products.product_status].total,
                currentPage: state => state.products.pagination[state.products.product_status].current,
                popup: state => state.products.popup
            })
        },
        methods: {
            ...mapMutations({
                showRemovalPopup: "showRemovalPopup",
                setCurrentPage: "setCurrentPage",
                setCurrentProductStatus: "setCurrentProductStatus"
            }),
            ...mapActions({
                getProducts: "getProducts"
            }),
            onPageChange(page) {
                this.setCurrentPage(page);
                this.getProducts();
            }
        },
        created() {
            this.setCurrentProductStatus(this.product_status);
            this.getProducts();
        },
    };
</script>

<style lang="scss" scoped>
    @import "app/styles/_variables.module.scss";
    .product-container {
        & > * {
            margin-bottom: $base-gutter * 2;
        }
    }

</style>
