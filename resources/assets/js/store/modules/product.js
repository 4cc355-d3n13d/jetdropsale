import axios from "axios"
import HTTPRequest from "app/js/utils/HTTPRequest";
import _ from "lodash";

const product = {
    state: {
        price: "",
        amount: "",
        options: [],
        images: [],
        description: "",
        title: "",
        vendor: "",
        productType: "",
        combinations: [],
        excludedCombinations: [],
        excludedOptionValues: [],
        excludedImages: [],
        status: 0,
        defaultFields: {
            title: "",
            description: "",
            combinations: [],
            collectionArray: [],
            tags: [],
            vendor: "",
            productType: ""
        },
        tags: [],
        collections: {},
        collectionArray: []
    },
    mutations: {
        onTagAdd(state, {tag, type}) {

            state[type].push(tag);
        },
        onTagDelete(state, {index, type}) {
            state[type].splice(index, 1)
        },
        setProductInfo(state, product) {

            if (product.my_product.options) {
                state.options = product.my_product.options;
            }

            state.status = product.my_product.status;

            state.vendor = product.my_product.vendor;
            state.productType = product.my_product.type;

            state.defaultFields.vendor = product.my_product.vendor;
            state.defaultFields.productType = product.my_product.type;

            state.title = product.my_product.title;
            state.defaultFields.title = product.my_product.title;

            state.price = product.my_product.price;
            state.defaultFields.price = product.my_product.price;

            state.amount = product.my_product.amount;
            state.defaultFields.amount = product.my_product.amount;

            state.description = product.my_product.description;
            state.defaultFields.description = product.my_product.description;

            state.images = product.my_product.images;

            const tagTitles = product.my_product.tags;

            state.tags = tagTitles;
            state.defaultFields.tags = tagTitles.slice();

            state.collections = product.my_product.collections;

            state.collectionArray = [];
            Object.keys(state.collections).map(key => {
                state.collectionArray.push(state.collections[key])
            });

            state.defaultFields.collectionArray = state.collectionArray.slice();
            let parsedCombinations;
            if (product.my_product.combinations) {
                parsedCombinations = product.my_product.combinations.map(el => {
                    return {
                        variant: JSON.parse(el.combination),
                        amount: el.amount,
                        price: el.price,
                        id: el.id
                    }
                });
            }


            state.combinations = parsedCombinations || state.combinations;
            state.defaultFields.combinations = parsedCombinations ? _.cloneDeep(parsedCombinations) : state.combinations;
        },

        clearNestedOptions(state) {
            state.options = []
        },

        editProductField(state, {field, value}) {
            state[field] = value;
        },

        excludeCombination(state, {id, variant}) {
            let combinationWasEnabled = false;
            Object.values(variant).map(el => {
                if (state.excludedOptionValues.includes(+el)) {
                    state.excludedOptionValues.splice(state.excludedOptionValues.indexOf(+el), 1);
                    combinationWasEnabled = true;
                }
            });
            if (!(state.excludedCombinations.includes(+id) || combinationWasEnabled)) {
                state.excludedCombinations.push(id)
            } else {
                state.excludedCombinations.splice(state.excludedCombinations.indexOf(id), 1)
            }
        },

        toggleCombinationsByOptionValue(state, {key, value}) {
            if (!state.excludedOptionValues.includes(value)) {
                state.excludedOptionValues.push(value);
            } else {
                state.excludedOptionValues.splice(state.excludedOptionValues.indexOf(value), 1)
            }
            state.combinations.map(combination => {
                if (combination.variant
                    && Object.values(combination.variant)
                    && Object.values(combination.variant).includes(value.toString())) {

                    if (!state.excludedCombinations.includes(+combination.id)) {
                        state.excludedCombinations.push(combination.id)
                    }
                }
            })
        },

        onVariantPriceChange(state, {id, value}) {

            const index = state.combinations.findIndex(combination => {
                return combination.id === id
            });

            state.combinations[index].price = value;
        },

        onVariantAmountChange(state, {id, value}) {

            const index = state.combinations.findIndex(combination => {
                return combination.id === id
            });

            state.combinations[index].amount = +value;
        },
        onProductPriceChange(state, {value}) {
            state.price = value;
        },
        onProductAmountChange(state, {value}) {
            state.amount = value;
        },
        setVariants(state, combinations) {

            const parsedCombinations = combinations.map(el => {
                return {
                    variant: JSON.parse(el.combination),
                    amount: el.amount,
                    price: el.price,
                    id: el.id
                }
            });

            state.combinations = parsedCombinations;
            state.defaultFields.combinations = _.cloneDeep(parsedCombinations);
        },
        cancelProductEditChanges(state) {
            state.excludedCombinations = [];
            state.excludedOptionValues = [];
            state.excludedImages = [];
            state.title = state.defaultFields.title;
            state.description = state.defaultFields.description;
            state.price = state.defaultFields.price;
            state.amount = state.defaultFields.amount;
            state.combinations = _.cloneDeep(state.defaultFields.combinations);
            state.tags = _.cloneDeep(state.defaultFields.tags);
            state.collectionArray = _.cloneDeep(state.defaultFields.collectionArray);
            state.vendor = state.defaultFields.vendor;
            state.productType = state.defaultFields.productType;


        },
        setDefaultFields(state, fields) {
            _.forIn(fields, (value, key) => {
                if (state.defaultFields[key]) {
                    state.defaultFields[key] = value;
                }
            })
        },
        excludeImage(state, image) {
            if (!state.excludedImages.includes(image)) {
                state.excludedImages.push(image)
            }
        }
    },
    actions: {
        getProductInfo({commit}, {id}) {
            axios.get(`/api/my-products/${id}`)
                .then(res => {
                    commit("setProductInfo", res.data);
                })
        },
        saveProductToShopify({commit, state, dispatch}, {id, callback}) {

            if (state.title === "") {
                dispatch("addUserNotificationWithRemovalTimeout", {type: "error", text: "Title cannot be blank"});
                return;
            }

            const prepareCollectionsForServer = (state) => {

                const collectionsForServer = [];

                state.collectionArray.map(collection => {
                    if (!Object.values(state.collections).includes(collection)) {
                        collectionsForServer.push({0: collection});
                    }
                });

                for (let id in state.collections) {
                    if (state.collectionArray.includes(state.collections[id])) {
                        collectionsForServer.push({[id]: state.collections[id]});
                    }
                }

                return collectionsForServer;
            };

            const deleteImages = HTTPRequest.delete(`/api/my-products/${id}/images`, {params: state.excludedImages}, {notShowSuccessMessage: true});
            const changeFields = HTTPRequest.put(`/api/my-products/${id}`, {
                title: state.title,
                description: state.description,
                price: state.price,
                amount: state.amount,
                tags: state.tags.join(","),
                combination: state.combinations,
                vendor: state.vendor,
                type: state.productType,
                collections: prepareCollectionsForServer(state)

            }, {notShowSuccessMessage: true});
            const deleteCombinations = HTTPRequest.delete(`/api/my-products/${id}/variants/`, {data: state.excludedCombinations}, {notShowSuccessMessage: true})

            const combinationsObject = {};
            state.combinations.forEach(combination => {
                if (combination.id && combination.price && combination.amount) {
                    combinationsObject[combination.id] = {
                        price: combination.price,
                        amount: combination.amount
                    }
                }
            });

            const editCombinations = HTTPRequest.put(`/api/my-products/${id}/variants/`, combinationsObject, {notShowSuccessMessage: true})

            Promise.all([deleteCombinations, editCombinations, changeFields, deleteImages]).then(values => {
                HTTPRequest.post("/api/my-products/shopify/send", {
                    "ids": [id]
                }).then(res => {
                    callback('/my/products/connected');
                })
            }).catch(err => {
                console.log(err)
            })
        },
        saveProductForLater({commit, dispatch, state}, {id}) {

            if (state.title === "") {
                dispatch("addUserNotificationWithRemovalTimeout", {type: "error", text: "Title cannot be blank"});
                return;
            }

            const prepareCollectionsForServer = (state) => {

                const collectionsForServer = [];

                state.collectionArray.map(collection => {
                    if (!Object.values(state.collections).includes(collection)) {
                        collectionsForServer.push({0: collection});
                    }
                });

                for (let id in state.collections) {
                    if (state.collectionArray.includes(state.collections[id])) {
                        collectionsForServer.push({[id]: state.collections[id]});
                    }
                }

                return collectionsForServer;
            };

            if (state.excludedImages.length !== 0) {
                HTTPRequest.delete(`/api/my-products/${id}/images`, {params: state.excludedImages})
            }
            if (state.excludedCombinations.length && state.excludedCombinations.length) {
                dispatch("deleteCombinations", {productId: id})
            }

            if (!_.isEqual(state.combinations, state.defaultFields.combinations)) {
                dispatch("editCombinations", {productId: id})
            }


            if (state.title !== state.defaultFields.title
                || state.description !== state.defaultFields.description
                || state.vendor !== state.defaultFields.vendor
                || state.productType !== state.defaultFields.productType
                || !_.isEqual(state.tags, state.defaultFields.tags)
                || !_.isEqual(state.collectionArray, state.defaultFields.collectionArray)) {

                HTTPRequest.put(`/api/my-products/${id}`, {
                    title: state.title,
                    description: state.description,
                    price: state.price,
                    amount: state.amount,
                    tags: state.tags.join(","),
                    combination: state.combinations,
                    vendor: state.vendor,
                    type: state.productType,
                    collections: prepareCollectionsForServer(state)
                }).then(res => {
                    // commit("setDefaultFields", {
                    //     price: (res.data.my_product && res.data.my_product.price) ? res.data.my_product.price : state.price,
                    //     amount: (res.data.my_product && res.data.my_product.amount) ? res.data.my_product.amount : state.amount,
                    //     title: (res.data.my_product && res.data.my_product.title) ? res.data.my_product.title : state.title,
                    //     description: (res.data.my_product && res.data.my_product.description) ? res.data.my_product.description : state.description
                    // })
                    dispatch("getProductInfo", {id: res.data.my_product.id})
                })
            }


        },
        editCombinations({state, dispatch, commit}, {productId}) {
            const combinationsObject = {};
            state.combinations.forEach(combination => {
                if (combination.id && combination.price && combination.amount) {
                    combinationsObject[combination.id] = {
                        price: combination.price,
                        amount: combination.amount
                    }
                }
            });

            HTTPRequest.put(`/api/my-products/${productId}/variants/`, combinationsObject).then(res => {
                if (res.data && res.data.my_product_variants) {
                    commit("setVariants", res.data.my_product_variants);
                }
            })
        },

        deleteCombinations({state, dispatch, commit}, {productId}) {
            HTTPRequest.delete(`/api/my-products/${productId}/variants/`, {data: state.excludedCombinations}).then(res => {
                if (res.data && res.data.my_product_variants) {
                    commit("setVariants", res.data.my_product_variants);
                }
            })
        }

    },
    getters: {
        // build a tree of options and values
        nestedProductOptions: state => {
            const options = {};
            state.options.forEach(option => {
                if (!options[option.ali_option_id]) {
                    options[option.ali_option_id] = []
                }
                options[option.ali_option_id].push(option.ali_sku);
            });

            return options;
        },
        arrayOfCollections(state) {

        },

        tagTitles: state => state.tags.map(tag => tag.title),

        //get combinations taking off ones that are in excluded array
        getCombinationsExceptExcluded: state => {
            return state.combinations
                .map(combination => {
                    return state.excludedCombinations.includes(combination.id)
                        ? {...combination, disabled: true}
                        : {...combination, disabled: false}
                })

            // .map(combination => {
            //     if (combination.disabled) return combination;
            //     let optionDisabled = false;
            //     state.excludedOptionValues.forEach(excludedOption => {
            //
            //         if (combination.variant
            //             && Object.values(combination.variant)
            //             && Object.values(combination.variant).includes(excludedOption.toString())) {
            //             optionDisabled = true;
            //         }
            //     });
            //     return {...combination, disabled: optionDisabled};
            // })
        },

        // get all possible combinations of values
        allOptionCombinations: (state, getters) => {
            const combinations = [];
            const recoursive = (obj, keyIndex, accum, concat) => {
                if (!Object.keys(obj)[keyIndex]) return;
                obj[Object.keys(obj)[keyIndex]].map((el, ind) => {
                    let string = concat + ((keyIndex !== 0 ? "," : "") + el);
                    if (keyIndex < Object.keys(obj).length - 1) {
                        recoursive(obj, keyIndex + 1, accum, string)
                    } else {
                        if (!accum.includes(concat)) {
                            accum.push(string.split(","))
                        }
                    }
                })
            };
            recoursive(getters.nestedProductOptions, 0, combinations, "");
            return combinations;
        },
        getImages(state) {
            if (Array.isArray(state.images)) {
                return state.images.filter(image => {
                    return !state.excludedImages.includes(image)
                })
            } else {
                return []
            }

        }

    }
};

export default product;
