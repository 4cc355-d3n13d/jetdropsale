<template>
    <div class="product__preview_nav-wrapper">
        <span v-if="showArrows" @click="slideBackward" class="product__preview_nav-arr is-left"><i class="fas fa-chevron-left"></i></span>
        <div
                @click="(e)=>click(image,index)"
                v-if="index < startImage + max && index >= startImage"
                :class="{'product__preview_nav-item_is-active': check(index)}"
             class='product__preview_nav-item'
             v-for="(image, index) in images">
            <img :src="image" alt="">
        </div>
        <span v-if="showArrows" @click="slideForward" class="product__preview_nav-arr is-right"><i class="fas fa-chevron-right"></i></span>
    </div>
</template>

<script>
    export default {
        name: "ImageCarousel",
        props: {
            images: {
                type: Array,
                default: f => []
            },
            click: {
                type: Function,
                default: f => f
            },
            main: {
                type: Number,
                default: 0
            },
            max: {
                type: Number,
                default: 4
            }
        },
        data() {
            return {
                startImage: 0
            }
        },
        computed: {
            showArrows() {
                return this.images.length > this.max;
            }
        },
        methods: {
            check(index) {
                return index === this.main
            },

            slideForward() {
                if (this.startImage + this.max >= this.images.length) {
                    return false;
                }
                this.startImage++
            },
            slideBackward() {
                if (this.startImage <= 0) {
                    return false;
                }
                this.startImage--
            }
        }

    }
</script>

<style lang="scss" scoped>



</style>