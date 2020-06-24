<template>
    <div class="category__header">
        <h3 class="category__title">My products
        </h3>
        <div class="category__tabs">
            <a href="#" title="" class="category__tabs_item is-active"><i class="fas fa-award"></i>All products({{count=0}})</a>
            <a href="#" title="" class="category__tabs_item"><i class="fas fa-chart-bar"></i>Connected</a>
            <a href="#" title="" class="category__tabs_item"><i class="fas fa-flag"></i>Non connected</a>
        </div>
    </div>
</template>

<script>
export default {
    name: "Header"
}
</script>

<style lang="scss">
    // @import "../../assets/_variables.module.scss";
    // @import "../../assets/_variables.module.scss";
    @import "app/styles/_variables.module.scss";

    .category__header {
        align-items: center;
        display: flex;
        justify-content: space-between;
        @media only screen and (max-width: 1200px)  {
            align-items: flex-start;
            justify-content: flex-start;
            flex-direction: column;
            & > * {
                margin: $base-gutter 0;
                &:first-child { margin-top: 0; }
                &:last-child { margin-bottom: -$base-gutter; }
            }
        }
    }

     .category__title {
        font-size: 24px;
    }
    .category__tabs {
        justify-content: flex-end;
        display: flex;
        flex: 1;
        width: 100%;
        @media only screen and (max-width: 1200px)  {
            justify-content: flex-start;
        }
        &_item {
            color: #666;
            cursor: pointer;
            font-size: 16px;
            line-height: 20px;
            margin-left: $base-gutter * 5;
            position: relative;
            white-space: nowrap;
            @media only screen and (max-width: 768px)  {
                font-size: 13px;
                margin-left: $base-gutter;
            }
            i {
                color: #999;
                @media only screen and (max-width: 768px)  {
                    display: none;
                }
            }
            &:before {
                content: "";
                background-color: transparent;
                bottom: -$base-gutter / 2;
                height: 2px;
                left: 0;
                position: absolute;
                width: 100%;
            }
            &:first-child { margin-left: 0; }
            i { margin-right: $base-gutter; }
            &.is-active {
                color: #1f8d5f;
                cursor: default;
                &:before { background-color: #1f8d5f; }
                i { color: inherit; }
            }
        }
    }
</style>
