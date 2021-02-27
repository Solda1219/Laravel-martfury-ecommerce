<template>
    <div class="ps-shop-brand">
        <div v-if="isLoading" class="col-12">
            <div class="half-circle-spinner">
                <div class="circle circle-1"></div>
                <div class="circle circle-2"></div>
            </div>
        </div>
        <a v-if="!isLoading" :href="item.website" v-for="item in data" :title="item.name">
            <img :src="item.logo" alt=":item.name"/>
        </a>
    </div>
</template>

<script>
    export default {
        data: function() {
            return {
                isLoading: true,
                data: []
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
          this.getFeaturedBrands();
        },
        methods: {
            getFeaturedBrands() {
                this.data = [];
                this.isLoading = true;
                axios.get(this.url)
                    .then(res => {
                        this.data = res.data.data;
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
