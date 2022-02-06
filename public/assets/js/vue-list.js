window.__vueList = {
    data() {
        return {
            total_num: 0,
            data_list: [],
            per_page: 20,
            last_page: 0,
            current_page: 1,
            is_init_list: false
        }
    },
    mounted() {
        if (this.is_init_list) {
            this.GetList()
        }
    },
    methods: {
        currentChangeEvent(e) {
            console.log('currentChangeEvent', e)
            this.current_page = e
            this.GetList()
        },
        handRes({data, current_page = 1, last_page = 0, per_page = 20, total = 0}) {
            this.data_list = data
            this.per_page = per_page;
            this.last_page = last_page
            this.current_page = current_page
            this.total_num = total
        },
    }
};

(function (vue) {
    //引入vue mixin
    vue.mixin(window.__vueList);
})(window.Vue);