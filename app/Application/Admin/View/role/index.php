<div class="page-container" v-cloak>
    <el-card>
        <div slot="header" class="breadcrumb">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>角色列表</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div>
            <el-form size="small" :inline="true">
                <el-form-item>
                    <el-link href="{:url('admin/role/edit')}">
                        <el-button type="primary">添加</el-button>
                    </el-link>
                </el-form-item>
            </el-form>
        </div>
        <div>
            <el-table
                    size="small"
                    :data="lists"
                    style="width: 100%;margin-bottom: 20px;"
                    row-key="role_id"
                    border
                    default-expand-all
                    :tree-props="{children: 'children'}">
                <el-table-column
                        prop="role_id"
                        label="ID"
                        min-width="100">
                </el-table-column>
                <el-table-column
                        prop="role_name"
                        label="名称"
                        min-width="180">
                </el-table-column>
                <el-table-column
                        prop="description"
                        min-width="300"
                        label="描述">
                </el-table-column>
                <el-table-column
                        prop="created_at"
                        min-width="140"
                        label="创建时间">
                </el-table-column>
                <el-table-column
                        fixed="right"
                        width="160"
                        label="操作">
                    <template slot-scope="{row}">
                        <el-link :href="'{:url('admin/role/edit')}?role_id='+row.role_id">
                            <el-button size="small" type="primary">编辑</el-button>
                        </el-link>
                        <el-link>
                            <el-button @click="deleteEvent(row.role_id)" size="small" type="danger">删除</el-button>
                        </el-link>
                    </template>
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
                lists: [],
                where: [],
            },
            mounted() {
                this.GetList()
            },
            methods: {
                sortEvent(role) {
                    this.$prompt('请输入排序', '提示', {
                        inputValue: role.sort,
                        confirmButtonText: '确定',
                        cancelButtonText: '取消',
                    }).then(({value}) => {
                        this.httpPost("{:url('admin/role/index/sort')}", {
                            role_id: role.role_id,
                            sort: value
                        }).then(res => {
                            if (res.status) {
                                this.$message.success(res.msg)
                                this.GetList()
                            }
                        })
                    }).catch(err => {
                    })
                },
                GetList() {
                    this.httpGet("{:url('admin/role/index/lists')}", {
                        ...this.where
                    }).then(res => {
                        let {lists = {}} = res.data
                        this.lists = lists
                    })
                },
                deleteEvent(role_id) {
                    this.$confirm('是确认删除？', '提示').then(() => {
                        this.httpPost("{:url('admin/role/delete')}", {role_id}).then(res => {
                            if (res.status) {
                                this.$message.success(res.msg)
                                this.GetList()
                            }
                        })
                    }).catch(err => {
                    })
                }
            }
        })
    })
</script>