<div class="page-container" v-cloak>
    <el-card>
        <div slot="header" class="breadcrumb">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>队列状态</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div>
            <div>
                <el-radio-group size="small" v-model="where.type" @change="refreshList" style="margin-bottom: 30px;">
                    <el-radio-button label="delayed">延迟</el-radio-button>
                    <el-radio-button label="reserved">执行中</el-radio-button>
                    <el-radio-button label="waiting">等待</el-radio-button>
                    <el-radio-button label="failed">失败</el-radio-button>
                    <el-radio-button label="timeout">超时</el-radio-button>
                </el-radio-group>
            </div>
            <el-form size="small" :inline="true">
                <el-form-item label="等待">{{count_list.waiting_count}}</el-form-item>
                <el-form-item label="失败">{{count_list.failed_count}}</el-form-item>
                <el-form-item label="超时">{{count_list.timeout_count}}</el-form-item>
            </el-form>
        </div>
        <div>
            <el-table
                    size="small"
                    :data="lists"
                    style="width: 100%">
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
                        prop="time"
                        label="执行时间"
                        min-width="100">
                </el-table-column>
                <el-table-column
                        prop="max_attempts"
                        label="最大执行次数"
                        min-width="100">
                </el-table-column>
            </el-table>
        </div>
    </el-card>
</div>

<script>
    $(function () {
        new Vue({
            el: ".page-container",
            data: {
                count_list: {},
                lists: [],
                where: {
                    type: "delayed"
                },
            },
            mounted() {
                this.GetList()
            },
            methods: {
                GetList() {
                    this.httpGet("{:url('admin/queue/status/lists')}", {
                        page: this.current_page,
                        ...this.where
                    }).then(res => {
                        if (res.status) {
                            let {lists = [], count_list = {}} = res.data
                            this.lists = lists
                            this.count_list = count_list
                        }
                    })
                },
                deleteEvent({setting_id}) {
                    this.$confirm("是否确认删除该记录？", '提示', {setting_id}).then(() => {
                        // this.httpPost("{:url('admin/setting/delete')}", {}).then(res => {
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