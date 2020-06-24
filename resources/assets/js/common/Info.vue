<template>
    <div class="info-container">
        <font-awesome-icon class="icon" icon="info"/>
    </div>
</template>

<script>
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    export default {
        name: "Info"
    }
</script>


<style scoped lang="scss">
    @import "app/styles/_variables.module.scss";
    .info-container {
        height: 14px;
        width: 14px;
        border-radius: 50%;
        background-color: $gray-light;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        .icon {
            height: 8px;
            width: 8px;
            color: $white;
        }
    }
</style>