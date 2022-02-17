<div class="page-container" v-cloak>
    <el-card>
        <div slot="header" class="breadcrumb">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>队列执行记录</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div>
            <el-form size="small" :inline="true">
                <el-form-item>
                    <el-input v-model="where.class_name" clearable placeholder="类名"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-input v-model="where.method" clearable placeholder="方法名称"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="searchEvent">查询</el-button>
                </el-form-item>
            </el-form>
            <div>
                <el-radio-group size="small" v-model="where.status" @change="refreshList" style="margin-bottom: 30px;">
                    <el-radio-button :label="-1">全部</el-radio-button>
                    <el-radio-button :label="0">执行中</el-radio-button>
                    <el-radio-button :label="1">成功</el-radio-button>
                    <el-radio-button :label="2">失败</el-radio-button>
                </el-radio-group>
            </div>
        </div>
        <div>
            <el-table
                    size="small"
                    :data="data_list"
                    style="width: 100%">
                <el-table-column
                        fixed
                        prop="queue_id"
                        label="ID"
                        width="80">
                </el-table-column>
                <el-table-column
                        prop="class_name"
                        label="类名"
                        min-width="180">
                </el-table-column>
                <el-table-column
                        prop="method"
                        min-width="100"
                        label="方法">
                </el-table-column>
                <el-table-column
                        prop="params"
                        label="参数"
                        min-width="180">
                </el-table-column>
                <el-table-column
                        align="center"
                        prop="status"
                        label="状态"
                        min-width="80">
                    <template slot-scope="{row}">
                        <div>
                            <el-tag v-if="row.status===0" size="small" type="primary">执行中</el-tag>
                            <el-tag v-if="row.status===1" size="small" type="success">成功</el-tag>
                            <el-tag v-if="row.status===2" size="small" type="danger">失败</el-tag>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column
                        align="center"
                        prop="process_count"
                        label="执行次数"
                        min-width="60">
                </el-table-column>
                <el-table-column
                        prop="error_msg"
                        label="错误信息"
                        min-width="100">
                    <template slot-scope="{row}">
                        <div v-if="row.error_msg">
                            <el-popover
                                    placement="bottom"
                                    title="错误详情"
                                    width="700"
                                    trigger="click">
                                <div>
                                    <pre style="white-space: pre-wrap;">{{row.error_data}}</pre>
                                </div>
                                <div slot="reference">
                                    <span>{{row.error_msg}}</span><i
                                            style="font-size: 14px;cursor: pointer;color: #999999;margin-left: 4px;"
                                            class="el-icon-question"></i>
                                </div>
                            </el-popover>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column
                        align="center"
                        prop="process_time"
                        label="执行时间"
                        width="80">
                </el-table-column>
                <el-table-column
                        prop="created_at"
                        label="创建时间"
                        width="140">
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
                where: {
                    status: -1
                },
            },
            methods: {
                GetList() {
                    this.httpGet("{:url('admin/queue/index/lists')}", {
                        page: this.current_page,
                        ...this.where
                    }).then(res => {
                        let {lists = {}} = res.data
                        this.handRes(lists)
                    })
                },
                searchEvent() {
                    this.refreshList()
                }
            }
        })
    })
</script>