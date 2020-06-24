
<template>
  <!--<div class="breadcrumbs">-->
    <!--<div class="breadcrumbs__item">-->
        <!--<a href="#" title="" class="breadcrumbs__link">Main</a>-->
    <!--</div><div class="breadcrumbs__item">-->
        <!--<a href="#" title="" class="breadcrumbs__link">Catalog</a>-->
    <!--</div><div class="breadcrumbs__item">-->
        <!--<a href="#" title="" class="breadcrumbs__link">Rubric</a>-->
    <!--</div><div class="breadcrumbs__item is-active">-->
        <!--<a href="#" title="" class="breadcrumbs__link">New-5000Lumen-LED-18650-AAA-Flashlight-Zoomable-Torch-Focus-Flashlight-Lamp</a>-->
    <!--</div>-->
  <!--</div>-->
</template>

<script>
export default {
  name: "Breadcrumps"
};
</script>

<style lang="scss">
@import "app/styles/_variables.module.scss";

.breadcrumbs {
  margin-left: 0;
  margin-right: 0;
  overflow: auto;
  position: relative;
  white-space: nowrap;
  @media only screen and (max-width: 768px) {
    padding: 0 $base-gutter * 2;
    width: 100vw;
  }
  &__item {
    display: inline-block;
    font-size: 12px;
    line-height: 20px;
    padding: 0 $base-gutter * 1.5;
    position: relative;
    &:before {
      $separator-size: 6px;
      content: ">";
      color: #9f9fa0;
      height: $separator-size;
      line-height: 6px;
      position: absolute;
      right: -$separator-size / 2;
      top: calc(50% - #{$separator-size / 2});
      width: $separator-size;
    }
    &:first-child {
      padding-left: 0;
    }
    &:last-child {
      padding-right: 0;
      &:before {
        display: none;
      }
    }
    &.is-active {
    }
  }
  &__link {
    color: #9f9fa0;
    &:hover {
      color: #333;
    }
    .is-active & {
      color: #333;
      cursor: default;
    }
  }
}
</style>
