<template>
    <div class="images">
        <div class="images__header">
            <div class="images__header__label">Images</div> 
            <div class="images__header__actions">
                <!--<base-button>-->
                <!--<font-awesome-icon icon="download"/>-->
                    <!--Download images-->
                <!--</base-button>-->
                <!--<base-button>-->
                <!--<font-awesome-icon icon="upload"/>-->
                    <!--Upload images-->
                <!--</base-button>-->
                <!--<base-button>More images</base-button>-->
            </div>
        </div>
        <div class="images__pictures">
            <div class="images__pictures__container" v-for="image in images" :key="image">
                <div class="images__pictures__container__cover">
                    <!--<div class="option">-->
                        <!--<font-awesome-icon class="icon" icon="download"/><div class="label">Download</div> -->
                    <!--</div>-->
                    <!--<div class="option">-->
                        <!--<font-awesome-icon class="icon" icon="edit"/><div class="label">Edit</div>-->
                    <!--</div>-->
                    <div @click="()=>excludeImage(image)" class="option">
                        <font-awesome-icon class="icon" icon="trash"/><div class="label">Delete</div>
                    </div>
                </div>
                <img class="images__pictures__container__picture" :src="image"/>
            </div>
        </div>
    </div>
</template>

<script>
import BaseButton from "app/js/common/BaseButton"
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { mapMutations, mapGetters } from "vuex";

export default {
    name: "images",
    components: {
        BaseButton,
        FontAwesomeIcon
    },
    computed: {
        ...mapGetters({
            images: "getImages"
        })

    },
    methods: {
        ...mapMutations({
            excludeImage: "excludeImage"
        })
    }
}
</script>

<style lang="scss" scoped>
@import "app/styles/_variables.module.scss";
.images {
    &__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 1.6em;
        margin-bottom: $base-gutter*2;
        &__actions {
                & div {
                color: $brand-green;
                margin-right: $base-gutter * 2;
            }
        }
       
    }

    &__pictures {
        display: flex;
        &__container {
            position: relative;
            border-radius: $round-corners;
            overflow: hidden;

            &__cover {
                color: $white;
                // padding: $base-gutter;
                cursor: pointer;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: space-evenly;
                height: 100%;
                width: 100%;
                background-color: $brand-green;
                opacity: 0;
                position: absolute;
                &:hover {
                    opacity: 0.8;
                }
                .option {
                    display: flex;
                    width: 80%;
                    
                    .icon {
                        flex:1;
                    }
                    .label {
                        font-size: .9em;
                        flex: 3;
                    }
                }
            }
            &__picture {
                height: 150px;
                width: 150px;
                margin: $base-gutter;
                @media only screen and (max-width: 768px) {
                    height: 80px;
                    width: 80px;
                }
            }
        }
        
    }
}
</style>
