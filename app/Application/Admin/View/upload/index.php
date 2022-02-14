<div class="page-container" v-cloak>
    <el-card>
        <div slot="header" class="breadcrumb">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><a href="{:url('admin/main/index')}">首页</a></el-breadcrumb-item>
                <el-breadcrumb-item>文件列表</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div>
            <el-form size="small" :inline="true">
                <el-form-item>
                    <el-input v-model="where.user" placeholder="文件名称"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-select v-model="where.region" placeholder="文件类型">
                        <el-option label="不限" value=""></el-option>
                        <el-option label="图片" value="image"></el-option>
                        <el-option label="视频" value="video"></el-option>
                        <el-option label="文档" value="doc"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-link>
                        <el-button type="primary" @click="searchEvent">查询</el-button>
                    </el-link>
                    <el-link>
                        <el-button @click="show_select_image=true" type="primary" @click="searchEvent">上传文件</el-button>
                    </el-link>
                </el-form-item>
            </el-form>
        </div>
        <div>
            <el-table
                    size="small"
                    :data="data_list"
                    style="width: 100%">
                <el-table-column
                        fixed
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
                        fixed="right"
                        align="center"
                        min-width="180"
                        label="操作">
                    <template slot-scope="{row}">
                        <el-link>
                            <el-button size="small" type="primary">编辑</el-button>
                        </el-link>
                        <el-link @click="deleteEvent">
                            <el-button size="small" type="danger">删除</el-button>
                        </el-link>
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
    <select-image :show="show_select_image" @confirm="selectImageConfirm"
                  @close="show_select_image=false"></select-image>
</div>
{hcmstag:include file="admin@/components/upload/select-image"}
<script>
    $(function () {
        new Vue({
            el: ".page-container",
            data: {
                show_select_image: false,
                is_init_list: true,
                where: {},
            },
            methods: {
                selectImageConfirm(files) {
                    console.log('selectImageConfirm', files)
                },
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
                deleteEvent({setting_id}) {
                    this.$confirm("是否确认删除该记录？", '提示', {setting_id}).then(() => {
                        // this.httpGet("{:url('admin/setting/delete')}", {}).then(res => {
                        //     if (res.status) {
                        //         this.$message.success(res.msg)
                        //     }
                        // })
                    })
                },
                searchEvent() {
                }
            }
        })
    })
</script>