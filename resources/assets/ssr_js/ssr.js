import Vue from "vue";
import store from "./store";
import {mapActions, mapMutations, mapState} from "vuex";
import uuid from "uuid";
import slider from "vue-slider-component";
import ImageCarousel from "../js/common/ImageCarousel"
import dropdown from "../js/common/SelectInput";
import PageSidebar from "../js/components/PageSidebar"
import ProductDescription from "../js/common/ProductDescription";
import MobileCategoriesMenu from "../js/common/MobileCategoriesMenu";
import MobileCategoryActionBar from "../js/common/MobileCategoryActionBar";
import bootstrap from "bootstrap";
import "bootstrap/dist/css/bootstrap.css";
import VueMq from 'vue-mq';
import '../js/configs/fa-config';
import GuestPopup from "../js/common/GuestPopup"


Vue.use(VueMq, {
    breakpoints: {
        sm: 768,
        md: 1200,
        lg: Infinity,
    }
})

var vueApp = new Vue({
    store,
    el: '#vue-app',
    components: {
        'slider': slider,
        'images-carousel':ImageCarousel,
        'page-sidebar':PageSidebar,
        'product-description': ProductDescription,
        'guest-popup':GuestPopup,
        'mobile-categories-menu':MobileCategoriesMenu,
        'mobile-categories-action-bar':MobileCategoryActionBar
    },
    computed: {
        ...mapState({
            "userNotifications": state => state.userNotifications,
            "showGuest": state => state.showGuest
        })
    },

    data: {
        ship_country:"",
        dropdownMenuExpanded: false,
        sliderValues: [0, 1000],
        sliderOptions: {
            min: 0,
            max: 1000,
            // interval: "100",
            bgStyle: {
                "backgroundColor": "#dedede",

            },
            tooltipStyle: {
                "alignItems": "center",
                "display":"flex",
                "height":"20px",
                "backgroundColor": "black",
                "borderColor": "black"
            },
            processStyle: {
                "backgroundColor": "#24a56f"
            },
            sliderStyle:
                {
                    "backgroundColor": "#24a56f",
                    "boxShadow": "none"
                }

        },
        main: "",
        variants_info: {},
        options: {},
        price: 0,
        product_id: 0,
        mainImgIndex: 0,
        optionAmount: "",
        description_expanded: false,
    },
    watch: {
        sliderValues: val => {
            console.log(val)
        },
        options: {
            handler(val) {

                let string = "";
                const valuesArray = [];
                for (let key in val) {
                    valuesArray.push(+val[key])
                }
                string = valuesArray.sort((a, b) => {
                    return a - b
                }).join("-");
                if (this.variants_info[string]) {
                    this.price = this.variants_info[string].price;
                    this.optionAmount.innerHTML = this.variants_info[string].amount;
                }
                return ""
            },
            deep: true
        },
        main: {
            handler(val) {
                console.log(val.src)
            },
            deep: true
        }
    },
    methods: {
        ...mapActions({
            addProduct: "addProduct",
            addUserNotificationWithRemovalTimeout: "addUserNotificationWithRemovalTimeout"
        }),
        ...mapMutations({
            removeUserNotificationById: "removeUserNotificationById",
            addNotification: "addNotification",
            showGuestPopup: "showGuestPopup",
            closeGusetPopup: "closeGusetPopup"
        }),
        onAddToImportListClick(e) {
            e.stopPropagation();
            const id = e.target.getAttribute("id");
            this.addProduct({id});
        },
        onProductCardImport(id) {
            this.addProduct({id});
        },

        onImageClick(image,index) {
            this.main.src = image;
            this.mainImgIndex = index;
        },
        onOptionClick(e) {
            const imgSrc = e.currentTarget.children[0].tagName === "IMG" ? e.currentTarget.children[0].src : "";
            if (imgSrc) {
                this.main.src = imgSrc;
            }
            const valueId = e.target.getAttribute("data-sku") || e.target.parentNode.getAttribute("data-sku");
            const optionId = e.target.getAttribute("data-option_id") || e.target.parentNode.getAttribute("data-option_id");

            this.options = {
                ...this.options,
                [optionId]: valueId
            };
        },
        fetchVariants() {
            fetch(`/api/products/${this.product_id}/variants`)
                .then(res => res.json())
                .then(data => this.variants_info = data.product_variants_info)
        },
        onPriceInputChange(value, type) {
            if (+this.sliderValues[0] > +this.sliderValues[1]) {
                const inbetween = +this.sliderValues[1];
                this.sliderValues[1] = +this.sliderValues[0];
                this.sliderValues[0] = inbetween;
                if (type === 0) {
                    this.$refs.max.focus()
                } else {
                    this.$refs.min.focus()
                }
            }
        },
        toggleDropdownMenu() {
         this.dropdownMenuExpanded = !this.dropdownMenuExpanded;
        },
        toggle() {
            this.description_expanded = !this.description_expanded;
        }
    },
    mounted() {
        try {
            this.optionAmount = this.$refs.optionAmount;
            this.product_id = this.$refs.mainDiv.getAttribute("data-product-id");
            this.fetchVariants();
            this.main = this.$refs.productMainImage;
        } catch (err) {
            console.log(err)
        }
    },
    created() {

        const url = new URL(window.location.href);
        const pmin = url.searchParams.get("pmin");
        const pmax = url.searchParams.get("pmax");
        const ship_country = url.searchParams.get("ship_country");
        this.ship_country = ship_country || "";
        this.sliderValues[0] = pmin || 0;
        this.sliderValues[1] = pmax || 1000;
    }


});



Vue.component('images-carousel', ImageCarousel);
Vue.component('slider', slider);
Vue.component('dropdown', dropdown);
Vue.component('page-sidebar', PageSidebar);
Vue.component('product-description', ProductDescription);
Vue.component('guest-popup',GuestPopup);
Vue.component('mobile-categories-menu', MobileCategoriesMenu);
Vue.component('mobile-categories-action-bar', MobileCategoryActionBar);




