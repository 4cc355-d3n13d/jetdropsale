<template>
    <div class="mobile_pagination_container">
        <div class="mobile_pagination">
            <div class="back_arrow">
                <font-awesome-icon class="angle-left" icon="angle-down"/>
            </div>

            <div class="mobile_page">
                {current}
            </div>
            <div class="forward_arrow">
                <font-awesome-icon class="angle-right" icon="angle-down"/>
            </div>
        </div>

    </div>
</template>

<script>
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome"
    export default {
        name: "MobilePagination",
        props: ["total", "current"],
        computed: {

        }
    }
</script>

<style lang="scss" scoped>
    .mobile_pagination_container {
        width: auto;
        display: flex;

        .mobile_pagination {
            .back_arrow{
                .angle-left {

                }
            }
            .forward_arrow{
                .angle-right {

                }
            }
        }
    }
</style>