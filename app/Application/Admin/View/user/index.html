<div class="page-container" v-cloak>
    <el-card>
        <div slot="header" class="breadcrumb">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>管理员列表</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div>
            <el-form size="small" :inline="true">
                <el-form-item>
                    <el-input v-model="where.real_name" placeholder="管理员姓名"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-input v-model="where.username" placeholder="用户名"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-select v-model="where.role_id" placeholder="所属角色">
                        <el-option label="不限" :value="0"></el-option>
                        <el-option v-for="(item,index) in role_list" :value="item.role_id"
                                   :label="item.role_name"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-link>
                        <el-button type="primary" @click="searchEvent">查询</el-button>
                    </el-link>
                    <el-link href="{:url('admin/user/edit')}">
                        <el-button type="primary">新增</el-button>
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
                        prop="admin_user_id"
                        label="ID"
                        min-width="80">
                </el-table-column>
                <el-table-column
                        prop="real_name"
                        label="管理员姓名"
                        min-width="180">
                </el-table-column>
                <el-table-column
                        prop="username"
                        min-width="180"
                        label="用户名">
                </el-table-column>
                <el-table-column
                        prop="role_name"
                        min-width="100"
                        label="所属角色">
                </el-table-column>
                <el-table-column
                        prop="created_at"
                        width="140"
                        label="创建时间">
                </el-table-column>
                <el-table-column
                        align="center"
                        min-width="180"
                        label="操作">
                    <template slot-scope="{row}">
                        <el-link :href="'{:url('admin/user/edit')}?admin_user_id='+row.admin_user_id">
                            <el-button size="small" type="primary">编辑</el-button>
                        </el-link>
                        <el-link>
                            <el-button @click="deleteEvent(row)" size="small" type="danger">删除</el-button>
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
</div>

<script>
    $(function () {
        new Vue({
            el: ".page-container",
            data: {
                is_init_list: true,
                role_list: [],
                where: {},
            },
            methods: {
                GetList() {
                    this.httpGet("{:url('admin/user/index/lists')}", {
                        page: this.current_page,
                        ...this.where
                    }).then(res => {
                        let {lists = {}, role_list = []} = res.data
                        this.role_list = role_list
                        this.handRes(lists)
                    })
                },
                deleteEvent({admin_user_id}) {
                    this.$confirm('是否确认删除该管理员？', '提示').then(() => {
                        this.httpPost("{:url('admin/user/delete')}", {
                            admin_user_id,
                        }).then(res => {
                            if (res.status) {
                                this.$message.success(res.msg)
                                this.GetList()
                            }
                        })
                    }).catch(err => {
                    })
                },
                searchEvent() {
                    this.GetList()
                }
            }
        })
    })
</script>