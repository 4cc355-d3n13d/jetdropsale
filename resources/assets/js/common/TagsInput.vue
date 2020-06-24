<template>
    <div class="tags-container">
        <div class="tags">
            <div v-for="(tag, index) in tags" class="tag">
                <div @click="()=>removeTag(index)" class="remove-container">
                    <font-awesome-icon icon="times" class="remove"></font-awesome-icon>
                </div>

                {{tag}}
            </div>
        </div>
        <div class="tag-input-container">
            <input
                    v-model="currentTag"
                    @input="onInputChange"
                    class="input"
                    type="text">
            <div @click="onTagSubmit" class="submit">
                Add
            </div>
        </div>
    </div>
</template>

<script>
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

    export default {
        name: "TagsInput",
        props: {
            tags: {
                type: Array,
                default: f => []
            },
            onTagAdd: {
                type: Function,
                default: f=>f
            },
            onTagDelete: {
                type: Function,
                default: f=>f
            },
        },
        data() {
            return {
                currentTag: ""
            }
        },

        methods: {
            onInputChange(e) {
                const value = e.target.value;

            },
            onTagSubmit(e) {
                e.stopPropagation();
                if (this.currentTag === "") return;
                this.onTagAdd(this.currentTag);
                this.currentTag = "";
            },
            removeTag(index) {
                this.onTagDelete(index)
            },
            onEnterPressed(e) {
                if (e.keyCode === 13) {
                    this.onTagSubmit(e);
                }
            }
        },
        mounted() {
            document.addEventListener('keypress', this.onEnterPressed)
        },
        destroyed() {
            document.removeEventListener('keypress', this.onEnterPressed)
        }
    }
</script>

<style lang="scss" scoped>
    @import "app/styles/_variables.module.scss";
    .tags-container {
        .tags {
            display: flex;
            padding: 10px 0;
            flex-wrap: wrap;
            margin: -5px;
            .tag {
                height: 40px;
                background-color: $brand-green;
                color: $white;
                display: flex;
                justify-content: center;
                align-items: center;
                border-radius: 5px;
                position: relative;
                max-width: fit-content;
                padding: 0 30px;

                margin: 3px;
                .label {

                }
                .remove-container {
                    cursor: pointer;
                    height: 16px;
                    width: 16px;
                    top: 5px;
                    right: 5px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    background-color: white;
                    position: absolute;
                    border-radius: 50%;
                    .remove {

                        font-size: 10px;
                        color: $brand-green;
                    }
                }

            }

        }

        .tag-input-container {
            display: flex;
            .input {
                border: 1px solid #eaeaea;
                border-radius: 2px;
                height: 40px;
                /*width: 100%;*/
                color: gray;
                padding: 10px;
                margin-right: 15px;

            }

            .submit {
                cursor: pointer;
                border: 1px solid $brand-green;
                padding: 0 15px;
                border-radius: 5px;
                color: $brand-green;
                display: flex;
                justify-content: center;
                align-items: center;

            }
        }
    }
</style>