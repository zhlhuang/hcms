<div class="page-container" v-cloak>
    <el-card>
        <div slot="header" class="breadcrumb">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><a href="{:url('admin/main/index')}">首页</a></el-breadcrumb-item>
                <el-breadcrumb-item>列表示例</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div>
            <el-form size="small" :inline="true">
                <el-form-item>
                    <el-input v-model="where.user" placeholder="审批人"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-select v-model="where.region" placeholder="活动区域">
                        <el-option label="区域一" value="shanghai"></el-option>
                        <el-option label="区域二" value="beijing"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="searchEvent">查询</el-button>
                </el-form-item>
            </el-form>
        </div>
        <div>
            <el-table
                    size="small"
                    :data="data_list"
                    style="width: 100%">
                <el-table-column
                        prop="date"
                        label="日期"
                        min-width="180">
                </el-table-column>
                <el-table-column
                        prop="name"
                        label="姓名"
                        min-width="180">
                </el-table-column>
                <el-table-column
                        prop="address"
                        min-width="380"
                        label="地址">
                </el-table-column>
                <el-table-column
                        align="center"
                        min-width="180"
                        label="操作">
                    <template slot-scope="{row}">
                        <el-button size="small" type="primary">编辑</el-button>
                        <el-button size="small" type="danger">删除</el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="pagination-container">
                <el-pagination
                        background
                        layout="prev, pager, next"
                        :total="total_num"
                        :current-page="current_page"
                        :page-size="per_page"
                        @current-change="currentChangeEvent"
                >
                </el-pagination>
            </div>
        </div>
    </el-card>
</div>

<script>
    $(function () {
        new Vue({
            el: ".page-container",
            data: {
                is_init_list: true,
                where: [],
            },
            methods: {
                GetList() {
                    this.handRes({
                        current_page: 1,
                        last_page: 2,
                        total: 25,
                        data: [{
                            date: '2016-05-02',
                            name: '王小虎',
                            address: '上海市普陀区金沙江路 1518 弄'
                        }, {
                            date: '2016-05-04',
                            name: '王小虎',
                            address: '上海市普陀区金沙江路 1517 弄'
                        }, {
                            date: '2016-05-01',
                            name: '王小虎',
                            address: '上海市普陀区金沙江路 1519 弄'
                        }, {
                            date: '2016-05-03',
                            name: '王小虎',
                            address: '上海市普陀区金沙江路 1516 弄'
                        }]
                    })
                    // this.httpGet('/admin/access/index/lists', {
                    //     page: this.current_page,
                    //     ...this.where
                    // }).then(res => {
                    //     let {lists = {}} = res.data
                    //     this.handRes(lists)
                    // })
                },
                searchEvent() {
                }
            }
        })
    })
</script>