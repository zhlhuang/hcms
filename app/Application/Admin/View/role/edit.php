<div class="page-container" v-cloak>
    <el-card>
        <div slot="header" class="breadcrumb">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><a href="/admin/main/index">首页</a></el-breadcrumb-item>
                <el-breadcrumb-item><a href="{:url('admin/role/index')}">角色列表</a></el-breadcrumb-item>
                <el-breadcrumb-item>{$title}</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div>
            <el-form size="small" label-width="80px">
                <el-form-item label="父级角色">
                    <el-cascader
                            @change="changeParentEvent"
                            v-model="cascader_value"
                            filterable
                            :options="role_list"
                            :props="{ checkStrictly: true,value:'role_id',label:'role_name',expandTrigger: 'hover' }"
                            clearable></el-cascader>
                    <div class="form-small">
                        <small>父级角色，角色最多三级</small>
                    </div>
                </el-form-item>
                <el-form-item label="角色名称">
                    <el-input v-model="form.role_name"></el-input>
                </el-form-item>
                <el-form-item label="角色描述">
                    <el-input style="width: 300px" :rows="4" v-model="form.description" type="textarea"></el-input>
                </el-form-item>
                <el-form-item label="权限">
                    <div v-if="cascader_value.length===0" style="color: #F56C6C;">
                        请选择父级角色
                    </div>
                    <div v-else>
                        <el-tree
                                ref="tree"
                                :data="access_list"
                                show-checkbox
                                node-key="access_id"
                                :default-expanded-keys="[]"
                                :default-checked-keys="[]"
                                :props="{children:'children',label:'access_name'}">
                        </el-tree>
                    </div>
                </el-form-item>
                <el-form-item>
                    <el-button @click="submitEvent" type="primary" size="small">
                        提交
                    </el-button>
                </el-form-item>
            </el-form>
        </div>
    </el-card>
</div>

<script>
    $(function () {
        new Vue({
            el: ".page-container",
            data: {
                cascader_value: [],
                role_list: [],
                access_list: [],
                form: {
                    role_name: '',
                    description: '',
                }
            },
            computed: {
                parent_role_id() {
                    if (this.cascader_value.length > 0) {
                        return this.cascader_value[this.cascader_value.length - 1]
                    } else {
                        return 0
                    }
                }
            },
            mounted() {
                this.getInfo()
            },
            methods: {
                changeParentEvent() {
                    this.getInfo(true)
                },
                getCheckedKeys() {
                    let all_checked = [
                        ...this.$refs.tree.getHalfCheckedKeys(),
                        ...this.$refs.tree.getCheckedKeys(),
                    ]
                    console.log(all_checked);
                    return all_checked
                },
                /**
                 * 获取联动组件的渲染值
                 * @param parent_role_id
                 */
                getCascaderValue(parent_role_id) {
                    this.role_list.forEach(item => {
                        if (item.children) {
                            item.children.forEach(it => {
                                if (parseInt(it.role_id) === parseInt(parent_role_id)) {
                                    this.cascader_value = [item.role_id, it.role_id]
                                    return
                                }
                            })

                            if (parseInt(item.role_id) === parseInt(parent_role_id)) {
                                this.cascader_value = [item.role_id]
                                return
                            }
                        }
                    })
                },
                /**
                 * 获取编辑所需信息
                 */
                getInfo(is_change_parent = false) {
                    this.httpGet("{:url('admin/role/edit/info')}", {
                        ...this.getUrlQuery(),
                        parent_role_id: this.parent_role_id,
                    }).then(res => {
                        if (res.status) {
                            let {
                                role_list = [],
                                role = {},
                                access_list = [],
                                role_access_ids = [],
                                admin_role_id = 0
                            } = res.data
                            if (!is_change_parent) {
                                this.role_list = [
                                    {
                                        role_id: admin_role_id,
                                        role_name: '无',
                                        children: []
                                    },
                                    ...role_list
                                ]
                                if (role.role_id) {
                                    this.getCascaderValue(role.parent_role_id)
                                    this.form = {
                                        ...role
                                    }
                                }
                            }
                            this.access_list = access_list

                            setTimeout(() => {
                                if (this.$refs.tree) {
                                    console.log('role_access_ids', role_access_ids)
                                    this.$refs.tree.setCheckedKeys(role_access_ids);
                                }
                            }, 300)
                        }
                    })
                },
                /**
                 * 提交信息
                 */
                submitEvent() {
                    this.httpPost("{:url('admin/role/edit')}", {
                        ...this.form,
                        parent_role_id: this.parent_role_id,
                        access_list: this.getCheckedKeys()
                    }).then(res => {
                        if (res.status) {
                            this.$message.success(res.msg)
                            location.href = "{:url('admin/role/index')}"
                        }
                    })
                }
            }
        })
    })
</script>

<style>
    .icon-item {
        text-align: center;
        font-size: 20px;
        color: #666666;
        line-height: 50px;
        cursor: pointer;
    }
</style>
