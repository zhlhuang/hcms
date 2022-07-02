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
            setTimeout(() => {
                //设置一定的延迟，比页面mounted稍微慢一些
                this.GetList()
            }, 200)
        }
    },
    methods: {
        //重置数组，一般搜索、更换状态等筛选会调用
        refreshList() {
            this.current_page = 1
            this.GetList()
        },
        //切换页码
        currentChangeEvent(e) {
            this.current_page = e
            this.GetList()
        },
        //处理分页数据
        handRes({data, current_page = 1, last_page = 0, per_page = 20, total = 0}) {
            this.data_list = data
            this.per_page = per_page;
            this.last_page = last_page
            this.current_page = current_page
            this.total_num = total
        },
    }
};
