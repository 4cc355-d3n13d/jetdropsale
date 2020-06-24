<template>
    <div @click="toggleExpanded" v-click-outside="() => {expanded = false}" class="mobile-categories-menu">
        All categories
        <div v-show="expanded" class="categories-list">
            <a :href="category.path" class="categories-list-item" v-for="category in categories">
                {{category.title}}
            </a>
        </div>
    </div>

</template>

<script>
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome"
    import {ClickOutside} from "app/js/directives";
    export default {
        name: "MobileCategoriesMenu",
        props: ["categories"],
        data() {
            return {
                expanded: false
            }
        },
        methods: {
            toggleExpanded() {
                this.expanded = !this.expanded;
            }
        },
        directives: {
            "click-outside": ClickOutside
        }
    }
</script>

<style lang="scss" scoped>
    @import "app/styles/_variables.module.scss";
    .mobile-categories-menu {
        display: none;
        background-color: white;
        box-shadow: 0 0 4px rgba(0, 0, 0, 0.1);
        border-radius: 5px;
        position: relative;
        cursor: pointer;
        padding: 10px;
        @media only screen and (max-width: 620px) {
            display: flex;

        }
        .categories-list {
            top: 100%;
            z-index: 10;
            position: absolute;
            display: flex;
            flex-direction: column;
            background-color: white;
            box-shadow: 0 0 4px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            .categories-list-item {
                padding: 10px;
                cursor: pointer;
                &:hover {
                    color: $brand-green !important;
                }

            }
        }
    }
</style>