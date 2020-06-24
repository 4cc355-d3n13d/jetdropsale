<template>
    <aside class="page__sidebar"
           :class="$mq === 'md' ? 'page__sidebar-middle-screen' + (menuExpanded ? '-expanded' : '') : 'page__sidebar'">
        <div class="page__sidebar_item">
            <a href="/" class="page__logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="168" height="39" viewBox="0 0 168 39">
                    <g fill="none" fill-rule="evenodd">
                        <g fill-rule="nonzero">
                            <path fill="#127C56"
                                  d="M1.583 36h34.834C37.29 36 38 35.3 38 34.435v-6.261c0-.865-.709-1.565-1.583-1.565H1.583c-.874 0-1.583.7-1.583 1.565v6.26C0 35.3.709 36 1.583 36zm1.584-6.26h31.666v3.13H3.167v-3.13z"/>
                            <path fill="#EAB64D"
                                  d="M23.75 17.217h9.5v9.392h-9.5v-9.392zm9.5 12.522c1.748 0 3.167-1.402 3.167-3.13v-9.392c0-1.728-1.419-3.13-3.167-3.13H22.167c-.875 0-1.584.7-1.584 1.565v12.522c0 .864.71 1.565 1.584 1.565H33.25z"/>
                            <path fill="#127C56"
                                  d="M13.545 15.915a1.599 1.599 0 0 0-1.757 0L9.5 17.423v-4.901h6.333v4.901l-2.288-1.508zm-.878 3.184l3.871 2.551c1.053.694 2.462-.052 2.462-1.302v-9.391c0-.865-.709-1.566-1.583-1.566h-9.5c-.875 0-1.584.701-1.584 1.566v9.39c0 1.251 1.41 1.997 2.462 1.303l3.872-2.551z"/>
                            <path fill="#127C56"
                                  d="M13.545 15.915a1.599 1.599 0 0 0-1.757 0L9.5 17.423v-4.901c0-.865-.709-1.565-1.583-1.565H4.75c-1.748 0-3.167 1.402-3.167 3.13v12.522c0 1.728 1.419 3.13 3.167 3.13h17.417c.874 0 1.583-.7 1.583-1.565V14.087c0-1.728-1.418-3.13-3.167-3.13h-3.166c-.875 0-1.584.7-1.584 1.565v4.901l-2.288-1.508zM19 14.087h1.583v12.522H4.75V14.087h1.583v6.26c0 1.251 1.41 1.997 2.462 1.303l3.872-2.551 3.871 2.551c1.053.694 2.462-.052 2.462-1.302v-6.261z"/>
                            <path fill="#DE3B35"
                                  d="M15.833 12.522c0 .864.71 1.565 1.584 1.565h3.166v1.565c0 .865.71 1.565 1.584 1.565h3.166c1.749 0 3.167-1.402 3.167-3.13V3.13C28.5 1.402 27.082 0 25.333 0h-9.5c-1.748 0-3.166 1.402-3.166 3.13v7.827c0 .864.709 1.565 1.583 1.565h1.583zm7.917 1.565c0-1.728-1.418-3.13-3.167-3.13H19c0-.865-.709-1.566-1.583-1.566h-1.584v-6.26h9.5v10.956H23.75z"/>
                        </g>
                        <path v-if="menuExpanded || $mq !== 'md'" fill="#fff"
                              d="M48.222 10.4h6.814c1.379 0 2.588.16 3.627.482 1.04.32 1.94.75 2.704 1.289a8.044 8.044 0 0 1 1.903 1.855 10 10 0 0 1 1.2 2.152c.294.737.506 1.473.635 2.21.128.736.193 1.416.193 2.039 0 1.284-.23 2.525-.69 3.724a9.436 9.436 0 0 1-2.041 3.2c-.902.935-2.019 1.686-3.352 2.252-1.333.567-2.874.85-4.62.85h-6.373V10.4zm2.648 17.504h3.338c1.122 0 2.184-.156 3.186-.467a7.65 7.65 0 0 0 2.635-1.402 6.687 6.687 0 0 0 1.793-2.337c.441-.935.662-2.025.662-3.271 0-.642-.101-1.393-.304-2.252a6.82 6.82 0 0 0-1.144-2.45c-.561-.774-1.338-1.43-2.331-1.968-.993-.538-2.28-.808-3.862-.808H50.87v14.955zm17.6-10.876h2.483v2.068h.055a3.64 3.64 0 0 1 .662-.978c.276-.292.584-.542.924-.75.34-.208.713-.373 1.117-.496.405-.123.81-.184 1.214-.184.405 0 .773.057 1.104.17l-.11 2.747a7.044 7.044 0 0 0-.608-.141 3.3 3.3 0 0 0-.607-.057c-1.213 0-2.142.35-2.786 1.048-.643.699-.965 1.784-.965 3.257v6.741H68.47V17.028zm12.22 6.712c0 .68.102 1.308.304 1.884.202.576.487 1.067.855 1.473a3.96 3.96 0 0 0 1.352.963c.533.236 1.13.354 1.793.354.662 0 1.26-.118 1.793-.354a3.96 3.96 0 0 0 1.352-.963c.368-.406.653-.897.855-1.473a5.647 5.647 0 0 0 .303-1.884c0-.68-.1-1.307-.303-1.883a4.271 4.271 0 0 0-.855-1.473 3.96 3.96 0 0 0-1.352-.963c-.533-.236-1.131-.354-1.793-.354-.662 0-1.26.118-1.793.354a3.96 3.96 0 0 0-1.352.963 4.271 4.271 0 0 0-.855 1.473 5.647 5.647 0 0 0-.303 1.883zm-2.648 0c0-.981.18-1.897.538-2.747a7.17 7.17 0 0 1 1.476-2.237 6.878 6.878 0 0 1 2.207-1.516 6.768 6.768 0 0 1 2.731-.552c.975 0 1.885.184 2.731.552a6.878 6.878 0 0 1 2.207 1.516 7.17 7.17 0 0 1 1.476 2.237c.358.85.538 1.766.538 2.747 0 .982-.18 1.903-.538 2.762a6.959 6.959 0 0 1-1.476 2.237 6.993 6.993 0 0 1-2.207 1.502 6.768 6.768 0 0 1-2.731.552 6.768 6.768 0 0 1-2.731-.552 6.993 6.993 0 0 1-2.207-1.502 6.959 6.959 0 0 1-1.476-2.237 7.096 7.096 0 0 1-.538-2.762zm17.02-6.712h2.484v1.926h.055c.478-.718 1.135-1.275 1.972-1.671a5.96 5.96 0 0 1 2.58-.595c1.01 0 1.926.18 2.744.538.819.359 1.517.86 2.097 1.501a6.785 6.785 0 0 1 1.338 2.252c.312.86.469 1.78.469 2.761 0 1.001-.157 1.931-.47 2.79a6.627 6.627 0 0 1-1.337 2.238 6.157 6.157 0 0 1-2.097 1.487c-.818.358-1.733.538-2.745.538-.938 0-1.82-.203-2.648-.61-.827-.405-1.462-.957-1.903-1.656h-.055v9.913h-2.483V17.028zm6.787 2.04c-.662 0-1.26.117-1.793.353a3.96 3.96 0 0 0-1.352.963 4.271 4.271 0 0 0-.855 1.473 5.647 5.647 0 0 0-.303 1.883c0 .68.1 1.308.303 1.884.202.576.487 1.067.855 1.473a3.96 3.96 0 0 0 1.352.963c.533.236 1.13.354 1.793.354.662 0 1.26-.118 1.793-.354a3.96 3.96 0 0 0 1.352-.963c.368-.406.653-.897.855-1.473a5.647 5.647 0 0 0 .303-1.884c0-.68-.1-1.307-.303-1.883a4.271 4.271 0 0 0-.855-1.473 3.96 3.96 0 0 0-1.352-.963c-.533-.236-1.131-.354-1.793-.354zm8.496-2.04h2.787l2.951 10.026h.055l3.118-10.026h2.62l3.31 10.026h.056l2.814-10.026h2.62l-4.22 13.425h-2.51l-3.449-10.026h-.055l-3.117 10.026h-2.676l-4.304-13.425zm24.524 6.712c0 .68.101 1.308.304 1.884.202.576.487 1.067.855 1.473a3.96 3.96 0 0 0 1.352.963c.533.236 1.13.354 1.793.354.662 0 1.26-.118 1.793-.354a3.96 3.96 0 0 0 1.352-.963c.367-.406.652-.897.855-1.473a5.647 5.647 0 0 0 .303-1.884c0-.68-.101-1.307-.303-1.883a4.271 4.271 0 0 0-.855-1.473 3.96 3.96 0 0 0-1.352-.963c-.534-.236-1.131-.354-1.793-.354-.662 0-1.26.118-1.793.354a3.96 3.96 0 0 0-1.352.963 4.271 4.271 0 0 0-.855 1.473 5.647 5.647 0 0 0-.304 1.883zm-2.648 0c0-.981.18-1.897.538-2.747a7.17 7.17 0 0 1 1.476-2.237 6.878 6.878 0 0 1 2.207-1.516 6.768 6.768 0 0 1 2.73-.552c.975 0 1.886.184 2.732.552a6.878 6.878 0 0 1 2.207 1.516 7.17 7.17 0 0 1 1.475 2.237c.36.85.538 1.766.538 2.747 0 .982-.179 1.903-.538 2.762a6.959 6.959 0 0 1-1.475 2.237 6.993 6.993 0 0 1-2.207 1.502 6.768 6.768 0 0 1-2.731.552 6.768 6.768 0 0 1-2.731-.552 6.993 6.993 0 0 1-2.207-1.502 6.959 6.959 0 0 1-1.476-2.237 7.096 7.096 0 0 1-.538-2.762zm15.448-6.712h2.786l2.952 10.026h.055l3.117-10.026h2.621l3.31 10.026h.056l2.813-10.026H168l-4.22 13.425h-2.511l-3.448-10.026h-.055l-3.118 10.026h-2.675l-4.304-13.425z"/>
                    </g>
                </svg>
            </a>
        </div>

        <div class="page__sidebar_item is-priority">
            <div v-if="loggedin" class='sidebar-menu'>
                <div
                        title="All products"
                        :class="['sidebar-menu__item', currentRoute === '/my/products' ? 'is-active' : '']">

                    <a v-if="ssr" href="/my/products">
                        <span class="sidebar-menu__item_icon"><font-awesome-icon
                                icon="cart-arrow-down"/>
                        </span>
                        <span :class="$mq === 'md' && !menuExpanded ? 'sidebar-menu__item-label-hidden' : ''">My products {{"("+allCount+")"}}</span>
                    </a>

                    <router-link v-else to="/my/products">
                        <span class="sidebar-menu__item_icon"><font-awesome-icon
                                icon="cart-arrow-down"/>
                        </span>
                        <span :class="$mq === 'md' && !menuExpanded ? 'sidebar-menu__item-label-hidden' : ''">My products {{"("+allCount+")"}}</span>
                    </router-link>

                </div>


                <a v-if="($mq==='lg' || menuExpanded) && ssr" href="/my/products/connected">
                    <div
                            class="sidebar-menu__item-inner">
                        Connected {{"("+connectedCount+")"}}
                    </div>
                </a>
                <router-link v-if="($mq==='lg' || menuExpanded)&& !ssr" to="/my/products/connected">
                    <div
                            :class="['sidebar-menu__item-inner', currentRoute === '/my/products/connected' ? 'is-active' : '']">
                        Connected {{"("+connectedCount+")"}}
                    </div>
                </router-link>


                <a v-if="($mq==='lg' || menuExpanded) && ssr" href="/my/products/non_connected">
                    <div
                            class="sidebar-menu__item-inner">
                        Non
                        connected {{"("+nonConnectedCount+")"}}
                    </div>
                </a>
                <router-link v-if="($mq==='lg' || menuExpanded)&& !ssr" to="/my/products/non_connected">
                    <div
                            :class="['sidebar-menu__item-inner', currentRoute === '/my/products/non_connected' ? 'is-active' : '']">
                        Non
                        connected {{"("+nonConnectedCount+")"}}
                    </div>
                </router-link>

                <div title="Orders" v-if="ssr" class="sidebar-menu__item">
                    <a href="/my/orders">
                        <span class="sidebar-menu__item_icon"><font-awesome-icon
                                icon="dolly"/>
                        </span>
                        <span :class="$mq === 'md' && !menuExpanded ? 'sidebar-menu__item-label-hidden' : ''">Orders</span>
                    </a>
                </div>
                <div title="Orders" v-else
                     :class="['sidebar-menu__item', currentRoute === '/my/orders' ? 'is-active' : '']">
                    <router-link to="/my/orders">
                        <span class="sidebar-menu__item_icon"><font-awesome-icon
                                icon="dolly"/>
                        </span>
                        <span :class="$mq === 'md' && !menuExpanded ? 'sidebar-menu__item-label-hidden' : ''">Orders</span>
                    </router-link>
                </div>

                <div title="Billing" v-if="ssr" class="sidebar-menu__item">
                    <a href="/my/billing">
                        <span class="sidebar-menu__item_icon"><font-awesome-icon
                                icon="coins"/>
                        </span>
                        <span :class="$mq === 'md' && !menuExpanded ? 'sidebar-menu__item-label-hidden' : ''">Billing</span>
                    </a>
                </div>
                <div title="Billing" v-else
                     :class="['sidebar-menu__item', currentRoute === '/my/billing' ? 'is-active' : '']">
                    <router-link to="/my/billing">
                        <span class="sidebar-menu__item_icon"><font-awesome-icon
                                icon="coins"/>
                        </span>
                        <span :class="$mq === 'md' && !menuExpanded ? 'sidebar-menu__item-label-hidden' : ''">Billing</span>
                    </router-link>
                </div>

                <div title="Settings" v-if="ssr" class="sidebar-menu__item">
                    <a href="/my/settings">
                        <span class="sidebar-menu__item_icon"><font-awesome-icon
                                icon="cog"/>
                        </span>
                        <span :class="$mq === 'md' && !menuExpanded ? 'sidebar-menu__item-label-hidden' : ''">Settings</span>
                    </a>
                </div>
                <div title="Settings" v-else
                     :class="['sidebar-menu__item', currentRoute === '/my/settings' ? 'is-active' : '']">
                    <router-link to="/my/settings">
                        <span class="sidebar-menu__item_icon"><font-awesome-icon
                                icon="cog"/>
                        </span>
                        <span :class="$mq === 'md' && !menuExpanded ? 'sidebar-menu__item-label-hidden' : ''">Settings</span>
                    </router-link>
                </div>
                <div v-if="isadmin" title="Admin panel" class="sidebar-menu__item">
                    <a href="/nova">
                        <span class="sidebar-menu__item_icon"><i class="fas fa-sign-in-alt"></i>
                        </span>
                        <span :class="$mq === 'md' && !menuExpanded ? 'sidebar-menu__item-label-hidden' : ''">Admin panel</span>
                    </a>
                </div>


                <div v-if="impersonate" title="Back to admin" class="sidebar-menu__item">
                    <a href="/user/impersonate/stop">
                        <span class="sidebar-menu__item_icon"><i class="fas fa-arrow-left"></i>
                        </span>
                        <span :class="$mq === 'md' && !menuExpanded ? 'sidebar-menu__item-label-hidden' : ''">Back to admin</span>
                    </a>
                </div>


                <!--<a href="#" title=""-->
                <!--:class="['sidebar-menu__item', currentRoute === '/my/billing' ? 'is-active' : '']">-->
                <!--<router-link to="/my/billing"><span class="sidebar-menu__item_icon"><font-awesome-icon-->
                <!--icon="coins"/></span><span :class="$mq === 'md' && !menuExpanded ? 'sidebar-menu__item-label-hidden' : ''">Billing</span>-->
                <!--</router-link>-->
                <!--</a>-->
                <!--<a href="#" title=""-->
                <!--:class="['sidebar-menu__item', currentRoute === '/my/settings' ? 'is-active' : '']">-->
                <!--<router-link to="/my/settings"><span class="sidebar-menu__item_icon"><font-awesome-icon-->
                <!--icon="cog"/></span><span :class="$mq === 'md' && !menuExpanded ? 'sidebar-menu__item-label-hidden' : ''">Settings</span>-->
                <!--</router-link>-->
                <!--</a>-->
            </div>


            <!--UNREGISTRED-->
            <!--<div v-if="!loggedin && $mq=='lg'" class="sidebar-menu-unregistr">-->
            <!--<a href="/login" title="" class="sidebar-menu-unregistr__trigger">-->
            <!--<i class="fas fa-user"></i>Get started-->
            <!--</a>-->
            <!--</div>-->
            <!--<div v-if="!loggedin && $mq=='md'" title="Get Started" class="sidebar-menu-unregistr-mid">-->
                <!--<a href="/login" title=""-->
                   <!--:class="menuExpanded ? 'sidebar-menu-unregistr__trigger' : 'sidebar-menu-unregistr-mid__trigger-mid'">-->
                    <!--<i class="fas fa-user"></i><span v-if="menuExpanded">Get started</span>-->
                <!--</a>-->
            <!--</div>-->
            <div v-if="$mq==='md'" class="page__sidebar_item">
            <span @click="toggleMenu">
                <font-awesome-icon :class="['toggler', menuExpanded && 'toggler-expanded']" icon="chevron-circle-right">
                </font-awesome-icon>
            </span>
            </div>
            <div v-if="!loggedin && ($mq=='lg' || menuExpanded)" class="appeal-text">

                <div class="appeal-first">
                        <div class="appeal-content">
                            Switch Your Dropshipping<br> To Dropwow Today!
                        </div>


                        <div class="appeal-item">
                            - Fast shipping
                        </div>
                        <div class="appeal-item">
                            - US suppliers
                        </div>
                        <div class="appeal-item">
                            - 400 000 tested products
                        </div>
                        <div class="appeal-item">
                            - Product editor
                        </div>
                        <div class="appeal-item">
                            - Auto order processing
                        </div>

                        <a href="https://news.dropwow.com/dropshipping-by-dropwow" class="learn-more">
                            Learn more
                        </a>
                    </div>
                <div class="appeal-second">
                    <div class="yellow-button-container">
                        <a href="https://apps.shopify.com/dropwow/?utm_medium=internal&utm_source=dropwownew&utm_term=left_bar"
                           target="_blank" class="yellow-button">
                            <div class="add_dropwow_to">
                                Add Dropwow to
                            </div>
                            <img class="shopify-icon" src="/img/shopify_logo_white.png"/>
                        </a>
                    </div>


                    <div class="already-have-account">
                        <div class="question">
                            Already have an account?
                        </div>
                        <a href="/login" class="log-in">
                            Log in
                        </a>

                    </div>
                </div>
                <div class="appeal-third">
                    <div class="start-dropshipping">
                        Start Dropshipping<br> With Dropwow Today!
                    </div>
                </div>
            </div>




            <!--UNREGISTRED-->
        </div>
        <div v-if="loggedin" class="page__sidebar_item">
            <div @click="expandIfNot" class="login">
                <div class="login__item">
                    <div class="login__avatar">
                        <font-awesome-icon icon="user"/>
                    </div>
                </div>
                <div v-if="$mq !=='md' || menuExpanded" class="login__item">
                    <div class="login__name">{{userName}}</div>
                    <div class="login__action">
                        <span class="login__action_icon"><font-awesome-icon icon="sign-in-alt"/></span>
                        <form method="POST" action="/logout" class="login__action_text">
                            <label class="logout-label">
                                Logout
                                <input hidden type="submit"/>
                            </label>
                            <input hidden name="_token" :value="csrf" type="text">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </aside>
</template>

<script>
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import {mapState, mapActions} from "vuex"
    import GuestPopup from "app/js/common/GuestPopup";
    import {ClickOutside} from "app/js/directives";

    export default {
        components: {FontAwesomeIcon, GuestPopup},

        data() {
            return {
                menuExpanded: false,
                currentRoute: this.$route ? this.$route.path : "",
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content') || ""
            }
        },
        watch: {
            '$route'(val) {
                this.currentRoute = val.path;
            }
        },
        methods: {

            toggleMenu() {
                this.menuExpanded = !this.menuExpanded
            },
            expandIfNot() {
                this.menuExpanded = true;
            }

        },
        directives: {
            "click-outside": ClickOutside
        },
        props: {
            ssr: {
                type: Boolean,
                default: false
            },
            connectedCount: {
                type: Number,
                default: 0
            },
            nonConnectedCount: {
                type: Number,
                default: 0
            },
            allCount: {
                type: Number,
                default: 0
            },
            userName: {
                type: String,
                default: ""
            },
            loggedin: {
                type: Boolean,
                default: true
            },
            isadmin: {
                type: Boolean,
                default: false
            },
            impersonate: {
                type: Boolean,
                default: false
            }
        }

    };
</script>

<style lang="scss">
    @import "app/styles/_variables.module.scss";

    .logo-svg {
        @media only screen and (max-width: 1200px) {
            display: none;

        }
    }

    .logout-label {
        cursor: pointer;
    }

    .dropdown-enter {
        transition: 1s;
        max-height: 0px;
    }

    .dropdown-enter-to {
        transition: 1s;
        max-height: 500px;
    }

    .dropdown-leave {
        /*transition: .5s;*/
        max-height: 500px;
    }

    .dropdown-leave-to {
        /*transition: .5s;*/
        max-height: 0;
    }

    .animated-submenu {
        overflow: hidden;
    }

    .page__sidebar {
        background-color: #2e2b33;
        color: #fff;
        display: flex;
        flex-direction: column;
        height: 100%;
        left: 0;
        position: fixed;
        top: 0;
        width: auto;

        transition: 300ms;
        overflow: hidden;
        &-middle-screen {

            z-index: 11;
            display: flex;
            width: 70px;
            overflow: hidden;

            &-expanded {
                width: 240px;
                z-index: 11;
                display: flex;
                .toggler {
                    cursor: pointer;
                    font-size: 30px;
                    color: $gray;
                    margin-left: $base-gutter*2;
                    transition: 500ms;
                    &-expanded {

                        transform: rotate(180deg);
                        color: #ffc75b;

                    }
                }

                .sidebar-menu {
                    padding-top: 50px;
                    border-top: 1px solid #45414d;
                    &-expanded {

                    }

                    .sidebar-menu__item {

                    }

                }

            }

            .toggler {
                cursor: pointer;
                font-size: 30px;
                color: $gray;
                margin-left: $base-gutter*2;
                transition: 500ms;
                &-expanded {

                    transform: rotate(180deg);
                    color: #ffc75b;

                }
            }

            .sidebar-menu {
                border-top: 1px solid #45414d;
                padding-top: 50px;

                &-expanded {

                }

                .sidebar-menu__item {
                    min-height: 40px;
                }

            }
        }

        @media only screen and (max-width: 768px) {
            height: calc(100% - #{$base-gutter * 5});
            margin-left: 0;
            position: fixed;
            top: $base-gutter * 5;
            width: 100%;
            z-index: 20;
            ////////////
            display: none !important;

        }
        &_item {
            position: relative;
            &.is-priority {
                flex: 1;
                height: 100%;
                width: 240px;
            }
        }
        &_title {
            color: rgba(#fff, 0.3);
            font-size: 16px;
            font-weight: 600;
            line-height: 20px;
            padding: $base-gutter * 2;
        }
        &_close {
            align-items: center;
            cursor: pointer;
            display: flex;
            height: $base-gutter * 6;
            justify-content: center;
            position: absolute;
            right: 0;
            top: 0;
            width: $base-gutter * 6;
        }
    }

    .hide {
        &-mobile {
            display: block;
            @media only screen and (max-width: 768px) {
                display: none;
            }
        }
        &-desktop {
            display: none;
            @media only screen and (max-width: 768px) {
                display: block;
            }
        }
    }

    .sidebar-menu {
        border-top: 1px solid #45414d;
        padding: $base-gutter * 5 0;
        width: $sidebar-size;

        /*@media only screen and (max-width: 1200px) {*/
        /*border-top: none;*/
        /*padding-top: 20px;*/
        /*}*/

        &__item {
            color: rgba(255, 255, 255, 0.6);
            display: block;
            font-size: 14px;
            font-weight: 600;
            line-height: 20px;
            padding: $base-gutter 0 $base-gutter $base-gutter * 2;
            position: relative;
            /*min-height: 40px;*/

            &:hover {
                color: $white;
            }
            &:before {
                content: "";
                background-color: #f8ce47;
                left: 0;
                height: $base-gutter * 3;
                opacity: 0;
                position: absolute;
                top: $base-gutter / 2;
                width: 2px;
            }

            &_icon {
                align-content: center;
                color: #ffc75b;
                display: flex;
                float: left;
                font-size: 16px;
                height: $base-gutter * 2;
                justify-content: center;
                margin-right: $base-gutter;
                width: $base-gutter * 2;

            }

            &-label-hidden {
                display: none !important;
            }
            &.is-active {
                color: #fff;
                &:before {
                    opacity: 1;
                }
            }
            &-inner {
                color: rgba(255, 255, 255, 0.6);
                display: block;
                font-size: 12px;
                font-weight: 400;
                margin-left: $base-gutter*6;
                /*@media only screen and (max-width: 768px) {*/
                /*display: none;*/
                /*}*/
                &:first-of-type {
                    padding: $base-gutter 0;
                }
                &:hover {
                    color: $white;
                }
                &.is-active {
                    color: #fff;
                    &:before {
                        opacity: 1;
                        content: "";
                        background-color: #f8ce47;
                        left: 0;
                        height: $base-gutter * 3;
                        /*opacity: 0;*/
                        position: absolute;
                        top: $base-gutter;
                        width: 2px;
                    }
                }
            }
        }
    }

    .sidebar-menu-unregistr {
        text-align: center;
        width: 240px;
        &__item {
            color: #fff;
            display: block;
            margin: $base-gutter * 4 0;
            text-align: center;
        }
        &__icon {
            align-items: center;
            background-color: #34313a;
            border-radius: 50%;
            color: #ffc75b;
            display: flex;
            font-size: 20px;
            height: $base-gutter * 6;
            justify-content: center;
            margin: 0 auto $base-gutter * 1.5 auto;
            width: $base-gutter * 6;
        }
        &__text {
            font-size: 16px;
            font-weight: 600;
            line-height: 20px;
        }
        &__trigger {
            border: 1px solid #1f8d5f;
            border-radius: $base-gutter / 2;
            color: #fff;
            cursor: pointer;
            display: inline-block;
            font-size: 16px;
            font-weight: 600;
            line-height: 20px;
            margin-top: $base-gutter * 4;
            margin-bottom: 20px;
            padding: $base-gutter * 1.5 $base-gutter * 2;
            text-align: center;
            white-space: nowrap;
            width: calc(100% - #{$base-gutter * 6});
            @media only screen and (max-width: 1368px) {
                font-size: 13px;
                padding: $base-gutter $base-gutter * 2;
            }
            &:hover {
                background-color: #1f8d5f;
                color: #fff;
            }
            i {
                color: #ffc75b;
                margin-right: $base-gutter;
            }
        }
    }

    .sidebar-menu-unregistr-mid {
        text-align: center;
        width: 100%;
        display: flex;
        justify-content: center;

        &__item {
            color: #fff;
            display: block;
            margin: $base-gutter * 4 0;
            text-align: center;
        }
        &__icon {
            align-items: center;
            background-color: #34313a;
            border-radius: 50%;
            color: #ffc75b;
            display: flex;
            font-size: 20px;
            height: $base-gutter * 6;
            justify-content: center;
            margin: 0 auto $base-gutter * 1.5 auto;
            width: $base-gutter * 6;
        }
        &__text {
            font-size: 16px;
            font-weight: 600;
            line-height: 20px;
        }
        .sidebar-menu-unregistr-mid__trigger-mid {
            width: 50px;
            height: 40px;
            margin-bottom: 20px;
            /*padding: 0 !important;*/
            margin-top: $base-gutter * 4;
            border: 1px solid #1f8d5f;
            border-radius: $base-gutter / 2;
            color: #fff;
            cursor: pointer;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            font-size: 16px;
            font-weight: 600;

            @media only screen and (max-width: 1368px) {
                font-size: 13px;

            }
            &:hover {
                background-color: #1f8d5f;
                color: #fff;
            }
            i {
                color: #ffc75b;
                align-self: center !important;
                justify-self: center !important;
            }
        }
    }

    .appeal-text {
        display: flex;
        margin-top: $base-gutter * 4;
        flex-direction: column;
        justify-content: space-between;
        height: 85%;
        .appeal-first {

            display: flex;
            flex-direction: column;

            .appeal-content {
                font-weight: bold;
                width: 100%;
                justify-content: center;
                text-align: center;
                font-size: 17px;
                margin-bottom: $base-gutter;
                word-break: normal;
                word-wrap: break-spaces;
            }
            .appeal-item {
                flex-wrap: wrap;
                margin-top: $base-gutter;
                margin-left: $base-gutter*2;
                font-size: 14px;
            }

            .learn-more {
                margin-left: $base-gutter * 10;
                margin-bottom: $base-gutter * 8;
                margin-top: $base-gutter;
                text-decoration: underline;
                font-size: 14px;

            }

        }
        .appeal-second {
            width: 100%;
            display: flex;
            flex-direction: column;
            .shopify-icon {
                width: 70px;
                height: 20px;
            }
            .yellow-button-container {
                width: 100%;
                display: flex;
                justify-content: center;
                margin-bottom: $base-gutter * 1;
                margin-left: 4px;
                .yellow-button {
                    .add_dropwow_to {
                        margin-right: 6px;
                    }

                    background-color: #ffa300;
                    height: $base-gutter * 4;
                    padding: $base-gutter;
                    color: white;
                    border-radius: $base-gutter/2;
                    display: flex;
                    justify-content: center;
                    justify-self: center;
                    align-items: center;
                    cursor: pointer;
                    font-weight: bold;
                    font-size: 14px;

                    &:hover {
                        background-color: #ff9500;
                    }
                }
            }
            .already-have-account {
                margin-bottom: $base-gutter*4;
                width: 100%;
                justify-content: space-around;
                display: flex;
                flex-direction: column;

                .question {
                    font-size: 14px;
                    text-align: center;
                }

                .log-in {
                    font-size: 14px;
                    text-decoration: underline;
                    width: 100%;
                    justify-content: flex-end;
                    text-align: end;
                    padding-right: 30px;
                }
            }
        }
        .appeal-third {
            width: 100%;
            display: flex;
            flex-direction: column;
            .start-dropshipping {
                width: 100%;
                justify-content: center;
                text-align: center;
                display: flex;
                word-wrap: break-spaces;
                justify-self: flex-end;
                font-style: italic;
            }
        }

    }

    .login {
        border-top: 1px solid #45414d;
        display: flex;
        padding: $base-gutter*1.5;
        width: $base-gutter * 24;
        cursor: pointer;
        @media only screen and (max-width: 768px) {
            padding: $base-gutter $base-gutter * 2;
            width: 100%;
        }
        @media only screen and (max-width: 1200px) {
            border-top: none;
        }
        &__item {
            margin: 0 $base-gutter / 2;
            &:first-child {
                margin-left: 0;
            }
            &:last-child {
                margin-right: 0;
            }
        }

        &__avatar {
            align-items: center;
            background-color: #434148;
            border-radius: 50%;
            color: #ffc75b;
            display: flex;
            height: $base-gutter * 4;
            justify-content: center;
            width: $base-gutter * 4;
        }
        &__name {
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            line-height: 20px;
            margin-top: 2px;
            opacity: 0.5;
        }
        &__action {
            color: #fff;
            font-size: 12px;
            line-height: 16px;
            opacity: 0.3;
            &_icon {
                float: left;
                margin-right: $base-gutter / 2;
            }
            &_text {
                display: inline-block;
                font-size: inherit;
                line-height: inherit;

            }
        }
    }


</style>



