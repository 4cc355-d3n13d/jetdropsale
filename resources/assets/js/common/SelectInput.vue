<template>
    <div class="select" @click="dropdownToggle">
        <input type="text" hidden v-if="ssr && val" :value="val" :name="ssr_name"/>
        <div class="value">
            {{valueName}}
        </div>
        <div class="arrow">
            <font-awesome-icon v-if="!ssr" icon="angle-down"/>
        </div>
        <div v-if="expanded" v-click-outside="() => {expanded = false}" class="dropdown">
            <div
                    v-if="default_val && default_val !== ''"
                    @click="select(0,0)"
                    class="menu-item">
                <div class="value">
                    {{default_val}}
                </div>
            </div>
            <div
                    :key="option.id"
                    v-for="option in options"
                    @click="select(option.id, option.value)"
                    class="menu-item">
                <div class="value">{{option.value}}</div>
            </div>
        </div>
    </div>
</template>

<script>
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import {ClickOutside} from "app/js/directives";
    export default {
        components: {
            FontAwesomeIcon
        },
        computed: {
            valueName: function () {
                if (this.value === 0) {
                    return this.default_val
                }
                const index = this.options.findIndex(option => {
                    if (this.ssr) return option.id === this.val;
                    return option.id === this.value;
                });

                if (index !== -1) {
                    return this.options[index].value
                }

                return this.default_val;
            }
        },
        data() {
            return {
                expanded: false,
                val: ""
            }
        },
        created() {
            if(this.ssr) {
              this.val = this.ssr_value || ""
            }
        },
        props: {
            options: {
                type: Array,
                default: () => []
            },
            value: {
                type: String,
                default: ""
            },
            onSelect: {
                type: Function,
                default: f => f
            },
            ssr: {
                type: Boolean,
                default: false
            },
            ssr_name: {
                type: String,
                default: ""
            },
            ssr_value: {
                type: String,
                default: ""
            },
            default_val: {
                type: String,

            }
        },

        methods: {
            dropdownToggle() {
                this.expanded = !this.expanded;
            },
            select(id, value) {
                if (this.ssr) {
                    this.val = id;

                } else {
                    this.onSelect(id);
                }

            }
        },
        directives: {
            "click-outside": ClickOutside
        }
    };
</script>

<style lang="scss" scoped>
    @import "app/styles/_variables.module.scss";

    .select {
        border: 1px solid $gray-light;
        border-radius: $round-corners;
        height: 40px;
        width: 100%;
        color: gray;
        padding: 10px;
        display: flex;
        position: relative;
        cursor: pointer;
        .value {
            display: flex;
            flex: 9;
            align-items: center;
        }

        .arrow {
            flex: 1;
            display: flex;
            justify-content: center;
            align-self: center;
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
            z-index: 6;
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

                .value {

                }
                min-height: $base-gutter*4;
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
        }
    }
</style>
