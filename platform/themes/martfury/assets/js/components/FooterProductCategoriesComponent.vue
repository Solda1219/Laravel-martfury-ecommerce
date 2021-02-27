<template>
    <div>
        <div v-if="isLoading" class="col-12">
            <div class="half-circle-spinner">
                <div class="circle circle-1"></div>
                <div class="circle circle-2"></div>
            </div>
        </div>
        <p v-if="data.length">
            <strong>{{ name }}:</strong>
            <a v-if="!isLoading" :href="item.url" v-for="item in data" :title="item.name">{{ item.name }}</a>
        </p>
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
            name: {
                type: String,
                default: () => null,
                required: true
            },
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
            getData() {
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
