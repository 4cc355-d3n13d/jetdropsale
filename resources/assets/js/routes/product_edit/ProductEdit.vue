<template>
    <div class="container">
        <div class="container__header">
            Edit description
        </div>
        <div class="container__title-edit">
            <div class="container__title-edit__label">
                Title
            </div>
            <div class="container__title-edit__input">
                <input v-model="title" class="text-input"/>
            </div>
        </div>


        <div class="container__filters-row__container">
            <div class="container__filters-row__container__title">
                Tags
            </div>
            <div class="container__filters-row__container__input">
                <tags-input
                    :tags="tags"
                    :onTagAdd="(tag)=>onTagAdd({type:'tags',tag})"
                    :onTagDelete="(index)=>onTagDelete({type:'tags',index})"
                />
            </div>
        </div>
        <!--<div class="container__filters-row__container">-->
            <!--<div class="container__filters-row__container__title">-->
                <!--Collections-->
            <!--</div>-->
            <!--<div class="container__filters-row__container__input">-->
                <!--<tags-input-->
                        <!--:tags="collections"-->
                        <!--:onTagAdd="(tag)=>onTagAdd({type:'collectionArray',tag})"-->
                        <!--:onTagDelete="(index)=>onTagDelete({type:'collectionArray',index})"-->
                <!--/>-->
            <!--</div>-->
        <!--</div>-->


        <div class="container__filters-row">
            <div class="container__filters-row__container">
                <div class="container__filters-row__container__title">
                    Product type
                </div>
                <div class="container__filters-row__container__input">

                    <input type="text" class="text-input" v-model="productType"/>
                </div>
            </div>
            <div class="container__filters-row__container">
                <div class="container__filters-row__container__title">
                    Vendors
                </div>
                <div class="container__filters-row__container__input">
                    <input type="text" class="text-input" v-model="vendor"/>
                </div>
            </div>



        </div>
        <div class="texteditor-container">
            <quill-editor v-model="description"
                          scrollingContainer=".ql-editor"
                          ref="myQuillEditor"
                          :options="{
                          scrollingContainer: '.ql-editor'
                          }"
                          @blur="onEditorBlur($event)"
                          @focus="onEditorFocus($event)"
                          @ready="onEditorReady($event)">
            </quill-editor>
        </div>

        <variants/>
        <images/>
        <!--<div class="container__sales">-->
            <!--<div class="container__sales__header">-->
                <!--Manage sales channel availability-->
            <!--</div>-->
            <!--<div class="container__sales__checks">-->
                <!--<div class="check">-->
                    <!--<base-checkbox/>-->
                    <!--<div class="label">Online store</div>-->
                <!--</div>-->
                <!--<div class="check">-->
                    <!--<base-checkbox/>-->
                    <!--<div class="label">Google shopping</div>-->
                <!--</div>-->
                <!--<div class="check">-->
                    <!--<base-checkbox/>-->
                    <!--<div class="label">Buy button</div>-->
                <!--</div>-->
            <!--</div>-->
        <!--</div>-->
        <!--<div class="container__other-settings">-->
            <!--<div class="container__other-settings__header">-->
                <!--Other settings-->
            <!--</div>-->
            <!--<div class="container__other-settings__content">-->
                <!--<div class="setting">-->
                    <!--<div class="setting-title">Boards</div>-->
                    <!--<input class="text-input" type="text"/>-->
                <!--</div>-->
                <!--<div class="setting">-->
                    <!--<div class="setting-title">Explore to</div>-->
                    <!--<select-input/>-->
                <!--</div>-->
            <!--</div>-->
        <!--</div>-->
        <div class="container__edit-options">
            <base-button :onClick="()=>saveProductToShopify({id: $route.params.id, callback: (url)=>this.$router.push(url)})" filled v-if="productCanBeSavedToShopify">
                <font-awesome-icon icon="file-import"/>
                Save to shopify
            </base-button>
            <base-button :onClick="()=>{saveProductForLater({id: $route.params.id})}" filled>
                <font-awesome-icon icon="save"/>
                Save for later
            </base-button>
            <!--<base-button filled>More options</base-button>-->
            <base-button :onClick="cancelProductEditChanges">Cancel</base-button>
        </div>
    </div>
</template>

<script>
    import {quillEditor} from "vue-quill-editor";
    // import {Editor} from 'tiptap'
    // import { VueEditor } from 'vue2-editor'
    import "quill/dist/quill.core.css";
    import "quill/dist/quill.snow.css";
    import "quill/dist/quill.bubble.css";
    import BaseCheckbox from "app/js/common/BaseCheckbox";
    import TextInput from "app/js/common/TextInput";
    import SelectInput from "app/js/common/SelectInput";
    import BaseButton from "app/js/common/BaseButton"
    import Variants from "./Variants"
    import Images from "./Images"
    import {mapActions, mapMutations, mapState, mapGetters} from "vuex";
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import TagsInput from "app/js/common/TagsInput";

    export default {
        data() {
            return {
                // content: "",
                editorOption: {
                    placeholder: "Product description",

                },
                content: ""
            };
        },

        components: {
            TextInput,
            BaseCheckbox,
            SelectInput,
            quillEditor,
            Variants,
            Images,
            BaseButton,
            FontAwesomeIcon,
            TagsInput
            // VueEditor,
            // Editor
        },
        methods: {
            ...mapActions({
                getProductInfo: "getProductInfo",
                saveProductForLater: "saveProductForLater",
                saveProductToShopify: "saveProductToShopify"
            }),
            ...mapMutations({
                onTagAdd: "onTagAdd",
                onTagDelete: "onTagDelete",
                clearNestedOptions: "clearNestedOptions",
                editProductField: "editProductField",
                cancelProductEditChanges: "cancelProductEditChanges"
            }),
            onEditorBlur(quill) {
                console.log("editor blur!", quill);
            },
            onEditorFocus(quill) {
                console.log("editor focus!", quill);
            },
            onEditorReady(quill) {
                console.log("editor ready!", quill);
            },
            onEditorChange({quill, html, text}) {
                console.log("editor change!", quill, html, text);
                this.content = html;
            },

        },
        computed: {
            ...mapState({
                status: state => state.product.status,
                tags: state => state.product.tags,
                collections: state => state.product.collectionArray

            }),







            vendor: {
                get() {
                    return this.$store.state.product.vendor
                },
                set(value) {
                    this.editProductField({field: "vendor", value})
                }
            },
            productType: {
                get() {
                    return this.$store.state.product.productType
                },
                set(value) {
                    this.editProductField({field: "productType", value})
                }
            },

            title: {
                get() {
                    return this.$store.state.product.title
                },
                set(value) {
                    this.editProductField({field: "title", value})
                }
            },
            description: {
                get() {
                    return this.$store.state.product.description
                },
                set(value) {
                    this.editProductField({field: "description", value})
                }
            },
            nestedOptions() {
                return this.$store.getters.nestedProductOptions;
            },
            productCanBeSavedToShopify() {
                return !(this.status === 10 || this.status === 30)
            }

        },
        created() {
            this.getProductInfo({id: this.$route.params.id})

        },
        updated() {


        },
        destroyed() {

            this.clearNestedOptions();
        }
    };
</script>

<style lang="scss" scoped>
    @import "app/styles/_variables.module.scss";

   .quill-editor {
       height: 500px;
       margin-bottom: 40px;
       overflow: hidden;
       border-bottom: 1px solid $gray;
   }

    .container {
        &__header {
            font-size: 1.6em;
            //   margin: $base-gutter * 1;
        }

        &__title-edit {
            &__label {
                font-size: 0.9em;
                margin-bottom: $base-gutter;
            }
            &__input {
                height: 40px;
                input {
                    font-size: 0.9em;
                }
            }
        }

        &__filters-row {
            display: flex;
            &__container {
                &:not(:last-child) {
                    margin-right: $base-gutter * 2;
                }
                display: flex;
                flex-direction: column;
                flex: 1;

                &__title {
                    font-size: 0.9em;
                    margin-bottom: $base-gutter / 2;
                }
            }
        }

        // .texteditor-container {
        //   position: relative;
        //   height: 400px;
        // }

        &__sales {
            margin-bottom: $base-gutter * 4;
            &__header {
                font-size: 1.6em;
                margin-bottom: $base-gutter*2;
            }
            &__checks {
                display: flex;

                .check {
                    display: flex;
                    margin-right: $base-gutter * 2;
                    & > * {
                        &:first-child {
                            margin-right: $base-gutter;
                        }
                    }
                }
            }
        }

        &__other-settings {
            margin-bottom: $base-gutter;
            &__header {
                font-size: 1.6em;
                margin-bottom: $base-gutter;
            }

            &__content {
                display: flex;
                .setting {
                    width: 200px;
                    display: flex;
                    flex-direction: column;
                    margin-right: $base-gutter * 2;
                    .setting-title {
                        .text-input {
                            border: 1px solid #eaeaea;
                            border-radius: 2px;
                            height: 40px;
                            width: 100%;
                            color: gray;
                            padding: 10px;
                        }
                    }
                }
            }
        }

        &__edit-options {
            & > * {
                margin-right: $base-gutter * 2;
            }
        }

        & > * {
            margin-bottom: $base-gutter * 4;
        }

        width: auto;
        height: auto;
        background-color: $white;
        border-radius: $round-corners;
        padding: $base-gutter * 2;
        margin: $base-gutter * 2;
        box-shadow: 0 0 4px rgba(0, 0, 0, 0.1);
    }
</style>
