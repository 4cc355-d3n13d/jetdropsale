<template>
    <div @click="onChange" :class="['checkbox', {'checked': checked}]">
        <div v-if="checked" class="checkbox__checked">
            <font-awesome-icon icon="check" class="checkbox__checked__icon"/>
        </div>
    </div>
</template>

<script>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
export default {
  components: { FontAwesomeIcon },
  props: {
      checked: {
          type: Boolean,
          default: false
      },
      onChange: {
          type: Function,
          default: f=>f
      }
  }
};
</script>

<style lang="scss" scoped>
@import "app/styles/_variables.module.scss";
    .checkbox{
        position: relative;
        display: block;
        width: 20px;
        height: 20px;
        border: 2px solid $gray;
        border-radius: 4px;
        overflow: hidden;
        cursor: pointer;
        background-color: $white;

        &__checked {
            height: 100%;
            width: 100%;
            background-color: $brand-green;
            display: flex;
            justify-content: center;
            align-items: center;
            &__icon {
                color: white;
                width:12px;
                height: 12px;

            }
        }
    }

    .checked {
        border: none;
    }
</style>
