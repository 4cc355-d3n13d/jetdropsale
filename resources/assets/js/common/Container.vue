<template>
    <div :style="{padding: padding}" class="base-container">
        <slot/>
    </div>
</template>

<script>
    export default {
        name: "Container",
        props: ["padding"]
    }
</script>

<style lang="scss" scoped>
    @import "app/styles/_variables.module.scss";
    .base-container {
        width: auto;
        background-color: $white;
        border-radius: $round-corners;
        padding: $base-gutter * 2;
        box-shadow: 0 0 4px rgba(0, 0, 0, 0.1);
    }
</style>