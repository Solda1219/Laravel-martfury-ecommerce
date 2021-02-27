<template>
        <div class="ps-container">
            <div class="ps-section__header">
                <h3>{{ category.name }}</h3>
                <ul class="ps-section__links">
                    <li v-for="item in productCategories" :key="item.id">
                        <a :class="productCategory.id === item.id ? 'active': ''" :id="item.slug + '-tab'" data-toggle="tab" :href="'#' + item.slug" role="tab" :aria-controls="item.slug" aria-selected="true" @click="getData(item)">{{ item.name }}</a>
                    </li>
                    <li><a :href="all">{{ __('View All') }}</a></li>
                </ul>
            </div>
            <div class="ps-section__content">
                <div class="half-circle-spinner" v-if="isLoading">
                    <div class="circle circle-1"></div>
                    <div class="circle circle-2"></div>
                </div>
                <div class="tab-pane fade show active" v-if="!isLoading" :id="productCategory.slug" role="tabpanel" :aria-labelledby="productCategory.slug + '-tab'" :key="productCategory.id">
                    <div v-carousel class="ps-carousel--nav owl-slider" data-owl-auto="false" data-owl-loop="false" data-owl-speed="10000" data-owl-gap="0" data-owl-nav="true" data-owl-dots="true" data-owl-item="7" data-owl-item-xs="2" data-owl-item-sm="2" data-owl-item-md="3" data-owl-item-lg="4" data-owl-item-xl="6" data-owl-duration="1000" data-owl-mousedrag="on">
                        <div class="ps-product" v-for="item in data" :key="item.id" v-if="data.length" v-html="item"></div>
                    </div>
                </div>
            </div>
        </div>
</template>

<script>
    export default {
        data: function() {
            return {
                isLoading: true,
                data: [],
                productCategory: {},
                productCategories: []
            };
        },

        mounted() {
            if (this.category) {
                this.productCategory = this.category;
                this.productCategories = this.children;
                this.getData(this.productCategory);
            }
        },

        props: {
            category: {
                type: Object,
                default: () => {},
                required: true
            },
            children: {
                type: Array,
                default: () => [],
            },
            url: {
                type: String,
                default: () => null,
                required: true
            },
            all: {
                type: String,
                default: () => null,
                required: true
            },
        },

        methods: {
            getData(category) {
                this.productCategory = category;
                this.data = [];
                this.isLoading = true;
                axios.get(this.url + '?category_id=' + category.id)
                    .then(res => {
                        this.data = res.data.data;
                        this.isLoading = false;
                    })
                    .catch(res => {
                        this.isLoading = false;
                        console.log(res);
                    });
            },
        },
    }
</script>
