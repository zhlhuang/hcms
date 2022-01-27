window.__vueList = {
    data() {
        return {
            dataList: [],
            perPage: 20,
            lastPage: 0,
            currentPage: 1,
            isInitLoading: true
        }
    },
    mounted() {
        if (this.isInitLoading) {
            this.GetList()
        }
    },
    methods: {
        currentChangeEvent(e) {
            console.log('currentChangeEvent', e)
            this.currentPage = e
            this.GetList()
        },
        handRes({data, current_page, last_page, per_page}) {
            this.dataList = data
            this.perPage = per_page;
            this.lastPage = last_page
            this.currentPage = current_page
        }
    }
};