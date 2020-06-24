<template>
    <div class="page-footer">
        <div class="content-menu__item">
            <a href="https://support.dropwow.com/about-us" title="" class="footer-link">About Us</a>
        </div>
        <div class="content-menu__item">
            <a href="https://support.dropwow.com/privacy-policy" title="" class="footer-link">Privacy Policy</a>
        </div>
        <div class="content-menu__item">
            <a href="https://support.dropwow.com/terms-of-use" title="" class="footer-link">Terms of Use</a>
        </div>
        <div class="content-menu__item">
            <a href="https://support.dropwow.com/refund-policy" title="" class="footer-link">Refund Policy</a>
        </div>
    </div>
</template>

<script>
    export default {
        name: "PageFooter.vue"
    }
</script>

<style lang="scss" scoped>
    .page-footer {
        width: 100%;
        height: 100px;
        background-color: white;
        margin: 0 !important;
        display: flex;
        justify-content: space-evenly;
        align-items: center;

        .footer-link {
            text-decoration: underline;
            font-size: 12px;
        }
    }
</style>