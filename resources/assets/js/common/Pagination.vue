<template>
    <div class="pagination">
        <div class="arrow"
             @click="onPageDecrease"
        >
            <font-awesome-icon class="angle-left" icon="angle-down"/>
        </div>
        <div class="pages-container">
            <div
                    v-if="leftDots"
                    @click="()=>onPageChange(1)"
                    class="pages-container__page">
                1
            </div>
            <div
                    v-if="leftDots"
                    @click="()=>onPageChange(2)"
                    class="pages-container__page">
                2
            </div>
            <div
                    class="pages-container__page"
                    v-if="leftDots">
                ...
            </div>
            <div
                    @click="()=>onPageChange(page)"
                    v-for="page in pagesArray"
                    :class="{'pages-container__page':true, 'active': page === current}"
                    v-if="leftDots && !rightDots && page >= total - 8">
                {{page}}
            </div>
            <div
                    v-if="!(leftDots || rightDots)"
                    @click="()=>onPageChange(page)"
                    v-for="page in pagesArray"
                    :class="{'pages-container__page':true, 'active': page === current}">
                {{page}}
            </div>
            <div
                    v-if="leftDots && rightDots && (page >= current - 3 && page <= current + 3)"
                    v-for="page in pagesArray"
                    @click="()=>onPageChange(total-1)"
                    :class="{'pages-container__page':true, 'active': page === current}">
                {{page}}
            </div>
            <div
                    @click="()=>onPageChange(page)"
                    v-for="page in pagesArray"
                    :class="{'pages-container__page':true, 'active': page === current}"
                    v-if="rightDots && !leftDots && page < 9">
                {{page}}
            </div>
            <div
                    class="pages-container__page"
                    v-if="rightDots">
                ...
            </div>
            <div
                    v-if="rightDots"
                    @click="()=>onPageChange(total-1)"
                    class="pages-container__page">
                {{total-1}}
            </div>
            <div
                    v-if="rightDots"
                    @click="()=>onPageChange(total)"
                    class="pages-container__page">
                {{total}}
            </div>
        </div>
        <div class="arrow"
             @click="onPageIncrease"
        >
            <font-awesome-icon class="angle-right" icon="angle-down"/>
        </div>
    </div>
</template>

<script>
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

    export default {
        name: "Pagination",
        props: {
            total: {
                type: Number,
                default: 5
            },
            current: {
                type: Number,
                default: 1
            },
            onPageChange: {
                type: Function,
                default: f => f
            },
            coreNumber: {
                type: Number,
                default: 7
            },

        },
        methods: {
            onPageIncrease() {
                if (this.current >= this.total) {
                    return false
                }
                this.onPageChange(this.current + 1)
            },
            onPageDecrease() {
                if (this.current <= 1) {
                    return false
                }
                this.onPageChange(this.current - 1)

            }
        },
        computed: {
            pagesArray() {
                const pagesArray = [];
                for (let i = 1; i <= this.total; i++) {
                    pagesArray.push(i)
                }
                return pagesArray;
            },
            leftDots() {
                const {coreNumber, current} = this;
                return current >= coreNumber;
            },
            rightDots() {
                const {coreNumber, total, current} = this;
                return current <= total - coreNumber + 1;
            }

        }

    }
</script>

<style lang="scss" scoped>
    @import "app/styles/_variables.module.scss";

    .pagination {
        display: flex;
        height: 60px;
        max-width: fit-content;
        margin: auto;

        background-color: $white;
        border-radius: $round-corners;

        box-shadow: 0 0 4px rgba(0, 0, 0, 0.1);

        .arrow {
            height: 60px;
            width: 60px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            .angle-left {
                height: 22px;
                width: 22px;
                font-weight: bold;
                transform: rotate(90deg);
                color: $gray;
            }

            .angle-right {
                height: 22px;
                width: 22px;
                color: $gray;
                transform: rotate(-90deg);
                font-weight: bold;
            }
            &:hover {
                background-color: $gray-light;
            }
        }
        .pages-container {
            display: flex;

            &__page {
                user-select: none;
                color: $gray-dark;
                font-weight: bold;
                font-size: 14px;
                cursor: pointer;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 60px;
                width: 60px;

                border-left: 1px solid $gray-light;
                border-right: 1px solid $gray-light;

                &:hover {
                    background-color: $gray-light;
                }
            }

            .active {
                background-color: $brand-green;
                color: $white;
            }
        }
    }
</style>