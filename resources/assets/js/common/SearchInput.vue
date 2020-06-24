<template>
    <div class="search-container">
        <input type="text" class="search"/>
        <font-awesome-icon class="icon" icon="search"/>
    </div>

</template>

<script>
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    export default {
        name: "SearchInput"
    }
</script>

<style lang="scss" scoped>
    @import "app/styles/_variables.module.scss";

    .search-container {
        position: relative;
        display: flex;
        align-items: center;
        .search {
            background-color: #fafafa;
            border: 2px solid #f0f0f0;
            border-radius: 3px;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            height: 40px;
            padding-left: 10px;
            width: 100%;
            padding-right: 24px;
        }

        .icon {
            position: absolute;
            right: $base-gutter;
            color: #666666;
            height: 14px;
            width: 14px;

        }
    }

</style>
