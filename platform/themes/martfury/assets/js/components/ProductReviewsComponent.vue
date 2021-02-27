<template>
    <div class="block__content">
        <div v-if="isLoading">
            <div class="half-circle-spinner">
                <div class="circle circle-1"></div>
                <div class="circle circle-2"></div>
            </div>
        </div>
        <div class="block--review" v-if="!isLoading && data.length" v-for="item in data" :key="item.id">
            <div class="block__header">
                <div class="block__image"><img :src="item.user_avatar" :alt="item.user_name" width="60" /></div>
                <div class="block__info">
                    <div class="rating_wrap">
                        <div class="rating">
                            <div class="product_rate" :style="{width: item.star * 20 + '%'}"></div>
                        </div>
                    </div>
                    <p><strong>{{ item.user_name }}</strong> | {{ item.created_at }}</p>

                    <div class="block__content">
                        <p>{{ item.comment }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="ps-pagination" v-if="!isLoading && meta.last_page > 1">
            <nav>
                <ul class="pagination">
                    <li class="page-item">
                        <a @click="getData(meta.current_page > 1 ? meta.current_page - 1 : 1)" aria-hidden="true" rel="previous" aria-label="« Previous" class="page-link">‹</a>
                    </li>
                    <li v-for="n in meta.last_page" :class="n === meta.current_page ? 'page-item active': 'page-item'" v-if="Math.abs(n - meta.current_page) < 3 || n === meta.last_page || n === 1">
                        <span class="first-page" v-if="(n === 1 && Math.abs(n - meta.current_page) > 3)">...</span>
                        <span v-if="n === meta.current_page" class="page-link">{{ n }}</span>
                        <span class="last-page" v-if="n === meta.last_page && Math.abs(n - meta.current_page) > 3">...</span>
                        <a v-if="n !== meta.current_page && !(n === 1 && Math.abs(n - meta.current_page) > 3) && !(n === meta.last_page && Math.abs(n - meta.current_page) > 3)" @click="getData(n)" class="page-link">{{ n }}</a>
                    </li>
                    <li class="page-item">
                        <a @click="getData(meta.current_page + 1)" rel="next"
                           aria-label="Next »" class="page-link">›</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</template>

<script>
    export default {
        data: function() {
            return {
                isLoading: true,
                data: [],
                meta: {},
            };
        },
        props: {
            url: {
                type: String,
                default: () => null,
                required: true
            },
        },
        mounted() {
          this.getData();
        },
        methods: {
            getData(page = 1) {
                this.data = [];
                this.isLoading = true;
                axios.get(this.url + '?page=' + page)
                    .then(res => {
                        this.data = res.data.data;
                        this.meta = res.data.meta;
                        this.isLoading = false;
                    })
                    .catch(res => {
                        this.isLoading = false;
                        console.log(res);
                    });
            },
        }
    }
</script>
