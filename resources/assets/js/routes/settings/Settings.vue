<template>
    <div class="settings-container">
        <breadcrumps/>
        <container>
            <import/>
            <orders/>
            <div class="save-dialog">
                <base-button :onClick="saveAllSettings" filled>
                    <font-awesome-icon icon="save" class="save-all-button"/>
                    Save settings
                </base-button>
                <div class="cancel-all" @click="cancelAllSettings">
                    Cancel all
                </div>
            </div>

        </container>

    </div>
</template>

<script>
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import Breadcrumps from "app/js/common/Breadcrumps";
    import Container from "app/js/common/Container";
    import Orders from "./Orders"
    import Import from "./Import"
    import BaseButton from "app/js/common/BaseButton";
    import {mapActions, mapMutations} from "vuex";
    export default {
        name: "Settings",
        components: {
            Breadcrumps,
            Container,
            Import,
            Orders,
            BaseButton
        },
        methods: {
            ...mapActions({
                saveAllSettings: "saveAllSettings",
                getAllSettings: "getAllSettings"
            }),
            ...mapMutations({
                cancelAllSettings: "cancelAllSettings"
            })
        },
        created() {
            this.getAllSettings()
        }
    }
</script>

<style lang="scss" scoped>
    @import "app/styles/_variables.module.scss";



    .settings-container {
        & > * {
            margin-bottom: $base-gutter * 2;
        }

        .save-dialog {
            margin-top: $base-gutter * 4;
            display: flex;
            align-items: center;
            & > * {
                margin-right: $base-gutter * 2;
            }

            .save-all-button {
                margin-right: $base-gutter;
            }

            .cancel-all {
                color: $gray-dark;
                font-size: 14px;
                text-decoration: underline;
                cursor: pointer;
                &:hover {
                    color: $brand-green;
                }
            }
        }
    }
</style>