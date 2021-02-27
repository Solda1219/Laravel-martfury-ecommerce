/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

'use strict';

import FeaturedProductCategoriesComponent from './components/FeaturedProductCategoriesComponent';
import FeaturedProductsComponent from './components/FeaturedProductsComponent';
import FeaturedBrandsComponent from './components/FeaturedBrandsComponent';
import ProductCollectionsComponent from './components/ProductCollectionsComponent';
import RelatedProductsComponent from './components/RelatedProductsComponent';
import ProductReviewsComponent from './components/ProductReviewsComponent';
import ProductCategoryProductsComponent from "./components/ProductCategoryProductsComponent";
import FooterProductCategoriesComponent from "./components/FooterProductCategoriesComponent";
import FlashSaleProductsComponent from "./components/FlashSaleProductsComponent";
import Vue from 'vue';

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('featured-product-categories-component', FeaturedProductCategoriesComponent);
Vue.component('featured-products-component', FeaturedProductsComponent);
Vue.component('featured-brands-component', FeaturedBrandsComponent);
Vue.component('product-collections-component', ProductCollectionsComponent);
Vue.component('related-products-component', RelatedProductsComponent);
Vue.component('product-reviews-component', ProductReviewsComponent);
Vue.component('product-category-products-component', ProductCategoryProductsComponent);
Vue.component('footer-product-categories-component', FooterProductCategoriesComponent);
Vue.component('flash-sale-products-component', FlashSaleProductsComponent);

/**
 * This let us access the `__` method for localization in VueJS templates
 * ({{ __('key') }})
 */
Vue.prototype.__ = (key) => {
    return window.trans[key] !== 'undefined' ? window.trans[key] : key;
};

Vue.directive('carousel', {
    inserted: function (element) {
        const el = $(element);
        const
            dataAuto = el.data('owl-auto'),
            dataLoop = el.data('owl-loop'),
            dataSpeed = el.data('owl-speed'),
            dataGap = el.data('owl-gap'),
            dataNav = el.data('owl-nav'),
            dataDots = el.data('owl-dots'),
            dataAnimateIn = el.data('owl-animate-in')
                ? el.data('owl-animate-in')
                : '',
            dataAnimateOut = el.data('owl-animate-out')
                ? el.data('owl-animate-out')
                : '',
            dataDefaultItem = el.data('owl-item'),
            dataItemXS = el.data('owl-item-xs'),
            dataItemSM = el.data('owl-item-sm'),
            dataItemMD = el.data('owl-item-md'),
            dataItemLG = el.data('owl-item-lg'),
            dataItemXL = el.data('owl-item-xl'),
            dataNavLeft = el.data('owl-nav-left')
                ? el.data('owl-nav-left')
                : "<i class='icon-chevron-left'></i>",
            dataNavRight = el.data('owl-nav-right')
                ? el.data('owl-nav-right')
                : "<i class='icon-chevron-right'></i>",
            duration = el.data('owl-duration'),
            datamouseDrag = el.data('owl-mousedrag') === 'on';

        el.addClass('owl-carousel').owlCarousel({
            animateIn: dataAnimateIn,
            animateOut: dataAnimateOut,
            margin: dataGap,
            autoplay: dataAuto,
            autoplayTimeout: dataSpeed,
            autoplayHoverPause: true,
            loop: dataLoop,
            nav: dataNav,
            mouseDrag: datamouseDrag,
            touchDrag: true,
            autoplaySpeed: duration,
            navSpeed: duration,
            dotsSpeed: duration,
            dragEndSpeed: duration,
            navText: [dataNavLeft, dataNavRight],
            dots: dataDots,
            items: dataDefaultItem,
            responsive: {
                0: {
                    items: dataItemXS,
                },
                480: {
                    items: dataItemSM,
                },
                768: {
                    items: dataItemMD,
                },
                992: {
                    items: dataItemLG,
                },
                1200: {
                    items: dataItemXL,
                },
                1680: {
                    items: dataDefaultItem,
                },
            },
        });
    },
});

new Vue({
    el: '#app',
});

if ($('#products').length) {
    new Vue({
        el: '#products',
    });
}

if ($('#footer-links').length) {
    new Vue({
        el: '#footer-links',
    });
}
