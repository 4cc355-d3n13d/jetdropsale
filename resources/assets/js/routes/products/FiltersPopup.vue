<template>
    <div class="filters-popup">
        <div class="filters-popup__header">
            <div class="label">
                Filter products
            </div>
            <span class="close-container">
                <font-awesome-icon @click="$emit('close-filters-popup')" icon="times-circle"/>
            </span>
        </div>
        <div class="body">
            <div class="filter-row">
                <div class="filter-column">
                    <div class="filter-label">
                        Title<br>contains
                    </div>
                    <div class="filter-input">
                        <input type="text" class="text-input" v-model="titleContaines"/>
                    </div>
                </div>
                <div class="filter-column">
                    <div class="filter-label">
                        Product<br>type
                    </div>
                    <div class="filter-input">
                        <select-input
                        :options="productTypeOptions"
                        />
                    </div>
                </div>
            </div>
            <div class="filter-row">
                <div class="filter-column">
                    <div class="filter-label">
                        Vendor
                    </div>
                    <div class="filter-input">
                        <input type="text" class="text-input" v-model="titleContaines"/>
                    </div>
                </div>
                <div class="filter-column">
                    <div class="filter-label">
                        Tags
                    </div>
                    <div class="filter-input">
                        <input type="text" class="text-input" v-model="titleContaines"/>
                    </div>
                </div>
            </div>
            <div class="filter-row">
                <div class="filter-column">
                    <div class="filter-label">
                        Collection
                    </div>
                    <div class="filter-input">
                        <input type="text" class="text-input" v-model="titleContaines"/>
                    </div>
                </div>
                <div class="filter-column">
                    <div class="filter-label">
                        Sort
                    </div>
                    <div class="filter-input">
                        <select-input
                            :options="sortOptions"
                        />
                    </div>
                </div>
            </div>
            <div class="button-container">
                <base-button filled>Filter</base-button>
            </div>
        </div>
    </div>
</template>

<script>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import BaseButton from "app/js/common/BaseButton";
import SelectInput from "app/js/common/SelectInput";

export default {
  name: "Popup",
  components: {
    FontAwesomeIcon,
    BaseButton,
    SelectInput
  },
  data() {
    return {
        sortOptions: [
          { id: 1, value: "Title a-z" }, 
          { id: 2, value: "Price" }
        ],
        productTypeOptions: [
            { id: 1, value: "All types" },
            { id: 2, value: "Type One" },
            { id: 3, value: "Type Two" }
        ],
      titleContaines: ""
    };
  }
};
</script>

<style lang="scss" scoped>
@import "app/styles/_variables.module.scss";
.filters-popup {
  display: block;
  background-color: #fff;
  border: 1px solid #eeeeef;
  border-radius: $base-gutter / 2;
  box-shadow: 0 0 4px rgba(0, 0, 0, 0.1);
  color: #666;
  font-size: 13px;
  font-weight: 400;
  line-height: 18px;
  z-index: 1;
  // overflow: hidden;
  //   padding: $base-gutter * 2;
  position: absolute;
  width: 600px;
  //   height: 200px;
  top: 100%;
  //   right: 0;
  &__header {
    padding: $base-gutter * 2;
    display: flex;
    position: relative;
    justify-content: space-between;
    height: 60px;
    width: 100%;
    border-bottom: 1px solid $gray-light;
    .label {
      font-size: 1.5em;
      display: inline-block;
    }

    .close-container {
      display: inline-block;
      height: 100%;
      & > * {
        cursor: pointer;
        font-size: 18px;
        color: $gray;
      }
    }
  }

  .body {
    display: flex;
    flex-direction: column;
    padding: 20px;
    & div {
      display: flex;
    }
    .filter-row {
      margin: 10px 0;
      display: flex;
      flex: 1;
      .filter-column {
        flex: 1;
        // padding: 10px;
        .filter-label {
          font-size: 0.9em;
        //   flex: 1;
            width: 30%;
          justify-content: center;
          text-align: end;
          align-items: center;
        }
        .filter-input {
          flex: 1;
        //   .text-input {
        //     border: 1px solid $gray-light;
        //     border-radius: 2px;
        //     height: 40px;
        //     width: 200px;
        //     color: gray;
        //     padding: 10px;
        //   }
        }
      }
    }
    .button-container {
      width: 100%;
      height: 70px;
      display: flex;
      justify-content: flex-end;
      padding: 10px 0;
    }
  }
}
</style>