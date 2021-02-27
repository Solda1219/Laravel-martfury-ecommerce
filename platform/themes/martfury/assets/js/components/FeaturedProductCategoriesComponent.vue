<template>
    <div class="row">
        <div v-if="isLoading" class="col-12">
            <div class="half-circle-spinner">
                <div class="circle circle-1"></div>
                <div class="circle circle-2"></div>
            </div>
        </div>
        <div v-if="!isLoading" class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6" v-for="item in data">
            <div class="ps-block--category"><a class="ps-block__overlay" :href="item.url"></a><img :src="item.image" :alt="item.name"/>
                <p>{{ item.name }}</p>
            </div>
        </div>
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
          this.getCategories();
        },
        methods: {
            getCategories() {
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
