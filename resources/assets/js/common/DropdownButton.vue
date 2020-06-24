<template>
    <div class="container">
        <div

                v-bind:class="{
          'btn': true, 
          'btn-fill': filled,
          'btn-wide': wide
		    }">
            <div
                    @click="onClick"
                    class="slot-container">
                <slot></slot>
            </div>
            <div class="dropdown-toggle" @click="dropdownToggle">

                <font-awesome-icon icon="angle-down"/>
            </div>
        </div>
        <div v-if="expanded" class="dropdown" v-click-outside="() => {expanded = false}">
            <div @click="()=>onMenuElementClick((item.handler && typeof item.handler === 'function') ? item.handler : f=>f)" :key="item.id" v-for="item in menuItems" class="menu-item">
                <a :href="item.href" v-if="item.href" :target="item.target">
                    {{item.value}}
                </a>
                <span v-else>
                    {{item.value}}
                </span>
            </div>
        </div>
    </div>

</template>

<script>
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import {ClickOutside} from "app/js/directives";

    export default {
        name: "BaseButton",
        components: {
            FontAwesomeIcon
        },
        data() {
            return {
                expanded: false,

            };
        },
        methods: {
            dropdownToggle() {
                this.expanded = !this.expanded;
            },
            onMenuElementClick(handler) {
                this.expanded = false;
                handler();
            }
        },
        props: {
            menuItems: {
                type: Array,
                default: ()=>[]
            },
            label: {
                type: String,
                default: ""
            },
            filled: {
                type: Boolean,
                default: false
            },
            wide: {
                type: Boolean,
                default: false
            },

            onClick: {
                type: Function,
                default: f => f
            }
        },
        directives: {
            "click-outside": ClickOutside
        }
    };
</script>

<style scoped lang="scss">
    @import "app/styles/_variables.module.scss";

    .container {
        position: relative;
        width: 100%;

        .btn {
            border: 1px solid #1f8d5f;
            border-radius: $base-gutter / 2;
            color: #666666;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            font-size: 16px;
            font-weight: 400;
            line-height: 20px;
            text-align: center;
            white-space: nowrap;
            @media only screen and (max-width: 1368px) {
                font-size: 13px;

            }

            &-wide {
                width: 100%;
            }
            &-fill {
                background-color: #1f8d5f;
                color: #fff;
            }
            &-link {
                border-color: transparent;
                color: #666666;
                padding-left: 0;
                padding-right: 0;
                text-decoration: underline;
                &:hover {
                    background-color: transparent;
                    color: #666666;
                    text-decoration: none;
                }
            }
            & > * {
                display: flex;
                justify-content: center;
                align-items: center;

                &:last-child {
                    &.icon,
                    &.fas {
                        margin-left: $base-gutter;
                        margin-right: 0;
                    }
                }
                &:first-child {
                    &.icon,
                    &.fas {
                        margin-left: 0;
                        margin-right: $base-gutter;
                    }
                }
            }

            .slot-container {
                padding: $base-gutter;
                flex: 4;
                min-width: 30px;

                &:hover {
                    background-color: #24a56f;
                    color: #fff;
                }
            }

            .dropdown-toggle {
                flex: 1;
                border-left: 1px solid $gray;
                justify-content: center;
                align-items: center;

                &:hover {
                    background-color: #24a56f;
                    color: #fff;
                }
            }
        }

        $arrow-size: 6px;
        .dropdown {
            background-color: #fff;
            border: 1px solid #eeeeef;
            border-radius: $base-gutter / 2;
            box-shadow: 0 0 4px rgba(0, 0, 0, 0.1);
            color: #666;
            font-size: 13px;
            font-weight: 400;
            line-height: 18px;
            z-index: 3;
            position: absolute;
            width: $base-gutter * 20;
            top: 130%;
            right: 0;
            &:before {
                content: "";
                position: absolute;
                top: -4px;
                left: 90%;
                width: 0;
                height: 0;
                border: $arrow-size + 3px solid transparent;
                border-bottom-color: rgba(128, 128, 128, 0.123);
                border-top: 0;
                margin-left: -$arrow-size;
                margin-top: -$arrow-size - 1px;
            }
            &:after {
                content: "";
                position: absolute;
                top: -3px;
                left: 90%;
                width: 0;
                height: 0;
                border: $arrow-size + 3px solid transparent;
                border-bottom-color: $white;
                border-top: 0;
                margin-left: -$arrow-size;
                margin-top: -$arrow-size;
            }
            &.l-c {
                left: calc(100% + #{$base-gutter});
                top: -30px;
            }

            .menu-item {
                cursor: pointer;
                padding: $base-gutter;
                cursor: pointer;
                &:not(:last-child) {
                    border-bottom: 1px solid rgba(218, 218, 218, 0.37);
                }

                &:first-child {
                    border-radius: 3px 3px 0 0;
                }
                &:last-child {
                    border-radius: 0 0 3px 3px;
                }
                &:hover {
                    background-color: $gray;
                    color: $white;
                }
            }
            .menu-item a {
                display: block;
                height: 100%;
            }
        }
    }
</style>
