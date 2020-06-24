<template>
    <div
            @click="onButtonClick"
            v-bind:class="{
		'btn': true, 
		'btn-fill': filled,
		'btn-wide': wide,
		'btn-small': small,
		'btn-gray': gray,
		'btn-disabled':disabled
		}">
        <slot></slot>
    </div>
</template>

<script>
    export default {
        name: "BaseButton",
        props: {
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
            small: {
                type: Boolean,
                default: false
            },
            gray: {
                type: Boolean,
                default: false
            },
            disabled: {
               type: Boolean,
               default: false,
            },
            onClick: {
                type: Function,
                default: f => f
            },

        },
        methods: {
            onButtonClick() {
                if(this.disabled) return;
                this.onClick();
            }

        }
    }
</script>

<style scoped lang="scss">
    @import "app/styles/_variables.module.scss";

    .btn {
        border: 1px solid #1f8d5f;
        border-radius: $base-gutter / 2;
        color: #666666;
        cursor: pointer;
        display: inline-block;
        font-size: 16px;
        font-weight: 400;
        line-height: 20px;
        padding: $base-gutter * 1.5 $base-gutter * 2;
        text-align: center;
        white-space: nowrap;
        @media only screen and (max-width: 1368px) {
            font-size: 13px;
            padding: $base-gutter $base-gutter * 2;
        }
        &:hover {
            background-color: #24a56f;
            color: #fff;
        }
        &-wide {
            width: 100%;
        }
        &-small{
            padding: $base-gutter/2 $base-gutter*1.5;
            font-size: .7em;
            max-height: 34px;
            @media only screen and (max-width: 768px) {
                font-size: 12px;
                padding: $base-gutter/4 $base-gutter/2;
            }
        }
        &-fill {
            background-color: #1f8d5f;
            color: #fff;
        }
        &-disabled {
            color: $gray !important;
            border-color: $gray !important;
            background-color: transparent;
            &:hover {
                color: $gray !important;
                border-color: $gray !important;
                background-color: transparent;
                cursor: default;
            }
        }
        &-gray {
            border: 1px solid $gray;
            &:hover {
                background-color: $gray
            }
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
    }
</style>
