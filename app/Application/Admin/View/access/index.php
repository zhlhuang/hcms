<div class="page-container" v-cloak>
    <el-card>
        <div slot="header" class="breadcrumb">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><a href="{:url('admin/main/index')}">首页</a></el-breadcrumb-item>
                <el-breadcrumb-item>权限列表</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div>
            <el-form size="small" :inline="true">
                <el-form-item>
                    <el-link href="{:url('admin/access/edit')}">
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
                    row-key="access_id"
                    border
                    default-expand-all
                    :tree-props="{children: 'children'}">
                <el-table-column
                        prop="access_id"
                        label="ID"
                        min-width="100">
                </el-table-column>
                <el-table-column
                        prop="access_name"
                        label="名称"
                        min-width="180">
                </el-table-column>
                <el-table-column
                        prop="uri"
                        min-width="300"
                        label="uri">
                    <template slot-scope="{row}">
                        <div>{{row.uri}}<span v-if="row.params!==''">?{{row.params}}</span></div>
                    </template>
                </el-table-column>
                <el-table-column
                        align="center"
                        prop="sort"
                        min-width="80"
                        label="排序">
                    <template slot-scope="{row}">
                        <div>
                            <el-link @click="sortEvent(row)" :underline="false" type="primary">
                                {{row.sort}} <i class="el-icon-edit"></i>
                            </el-link>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column
                        align="center"
                        prop="is_menu"
                        min-width="60"
                        label="类型">
                    <template slot-scope="{row}">
                        <el-tag v-if="row.is_menu==1" size="mini" type="primary">菜单</el-tag>
                        <el-tag v-else size="mini" type="success">权限</el-tag>
                    </template>
                </el-table-column>
                <el-table-column
                        align="center"
                        prop="menu_icon"
                        min-width="60"
                        label="图标">
                    <template slot-scope="{row}">
                        <i :class="row.menu_icon"></i>
                    </template>
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
                        <el-link :href="'{:url('admin/access/edit')}?access_id='+row.access_id">
                            <el-button size="small" type="primary">编辑</el-button>
                        </el-link>
                        <el-link>
                            <el-button @click="deleteEvent(row.access_id)" size="small" type="danger">删除</el-button>
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
                sortEvent(access) {
                    this.$prompt('请输入排序', '提示', {
                        inputValue: access.sort,
                        confirmButtonText: '确定',
                        cancelButtonText: '取消',
                    }).then(({value}) => {
                        this.httpPost("{:url('admin/access/index/sort')}", {
                            access_id: access.access_id,
                            sort: value
                        }).then(res => {
                            if (res.status) {
                                this.$message.success(res.msg)
                                this.GetList()
                            } else {
                                this.$message.error(res.msg)
                            }
                        })
                    }).catch(err => {
                    })
                },
                GetList() {
                    this.httpGet("{:url('admin/access/index/lists')}", {
                        ...this.where
                    }).then(res => {
                        let {lists = {}} = res.data
                        this.lists = lists
                    })
                },
                deleteEvent(access_id) {
                    this.$confirm('是确认删除？', '提示').then(() => {
                        this.httpPost("{:url('admin/access/delete')}", {access_id}).then(res => {
                            if (res.status) {
                                this.$message.success(res.msg)
                                this.GetList()
                            } else {
                                this.$message.error(res.msg)
                            }
                        })
                    }).catch(err => {
                    })
                }
            }
        })
    })
</script>