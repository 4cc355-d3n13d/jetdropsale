<template>
    <div class="content__text" :class="expanded ? '' : 'short'">
        <span v-html="description"></span>
        <div @click="toggle" class="toggler-container">
            <span class="toggler">See more</span>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ProductDescription",
        props: ["description"],
        data() {
            return {
                expanded: false
            }
        },
        methods: {
            toggle() {
                this.expanded = !this.expanded;
            }
        }

    }
</script>

<style scoped lang="scss">
    .content__text {
        position: relative;
        overflow: hidden;
    }
    .short {
        max-height: 300px;
    }
    .toggler-container {
        position: absolute;
        width: 100%;
        height: 20px;
        background-color: white;
        .toggler {
            color: green;
        }
    }
</style>