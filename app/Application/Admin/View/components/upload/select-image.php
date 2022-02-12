<script type="text/x-template" id="select-image">
    <div class="select-image">
        <div style="height: 400px;">
            <el-dialog title="选择图片" @close="$emit('close')" width="668px" :visible.sync="dialogVisible">
                <div>
                    <el-row>
                        <el-col :span="6">
                            <div class="menu-container">
                                <ul role="menubar" class="el-menu">
                                    <li class="el-menu-item-group">
                                        <ul class="group_list">
                                            <li class="el-menu-item" style="padding: 0 8px;" @click="selectGroup('all')"
                                                :class="{'group_active' : now_group == '-1'}">
                                                全部
                                            </li>
                                            <li class="el-menu-item" style="padding: 0 8px;" @click="selectGroup(0)"
                                                :class="{'group_active' : now_group == 0}">
                                                未分组
                                            </li>
                                            <template v-for="(item,index) in group_list">
                                                <li :key="index" class="el-menu-item"
                                                    style="padding: 0 8px;position: relative;"
                                                    :class="{'group_active' : now_group == item.group_id }">
                                            <span @click="selectGroup(item.group_id)"
                                                  style="word-break:break-all; white-space:normal; width:75%;line-height: 20px;vertical-align:middle;display:inline-block;">{{item.group_name}}</span>
                                                    <div style="position: absolute;right: 10px;top: 0;">
                                                        <el-dropdown @command="(c)=>handleGroup(c,item)" size="small"
                                                                     trigger="click">
                                                            <div class="el-dropdown-link" style="line-height: 20px;">
                                                                <i style="line-height: 20px;"
                                                                   class="el-input__icon el-icon-more-outline group_edit_icon"></i>
                                                            </div>
                                                            <el-dropdown-menu slot="dropdown">
                                                                <el-dropdown-item command="edit">
                                                                    编辑
                                                                </el-dropdown-item>
                                                                <el-dropdown-item command="delete">
                                                                    删除
                                                                </el-dropdown-item>
                                                            </el-dropdown-menu>
                                                        </el-dropdown>
                                                    </div>
                                                </li>
                                            </template>
                                        </ul>
                                        <div class="grid-content" style="padding: 19px;">
                                            <el-button type="primary" @click="editGroup()" size="mini">新增分组</el-button>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </el-col>

                        <el-col :span="18">
                        </el-col>
                    </el-row>
                </div>
                <div>
                    <div solt="footer" style="text-align: right;padding-top: 10px;">
                        <el-button size="small" type="primary" @click="confirm">确定</el-button>
                        <el-button size="small" type="default" @click="$emit('close')">关闭</el-button>
                    </div>
                </div>
            </el-dialog>
        </div>
    </div>
</script>


<script>
    $(function () {
        Vue.component('select-image', {
            template: '#select-image',
            props: {
                show: false,
            },
            data() {
                return {
                    group_list: [],
                    now_group: -1,
                    dialogVisible: false,
                    file_type: 'image'
                }
            },
            watch: {
                show(value) {
                    if (value) {
                        //获取分组列表
                        // this.getGalleryGroup();
                    }
                    this.dialogVisible = value
                }
            },
            computed: {},
            mounted() {
                this.getGroupList()
            },
            methods: {
                getGroupList() {
                    this.httpGet("{:url('admin/upload/group/lists')}", {
                        file_type: this.file_type,
                    }).then(res => {
                        if (res.status) {
                            let {group_list = []} = res.data
                            this.group_list = group_list
                        }
                    })
                },
                handleGroup(c, {group_id, group_name}) {
                    console.log(c, {group_id, group_name})
                    if (c === 'edit') {
                        this.editGroup(group_name, group_id)
                    }
                    if (c === 'delete') {
                        this.deleteGroup(group_id)
                    }
                },
                deleteGroup(group_id) {
                    this.$confirm('是否确认删除该分组？', '提示').then(() => {
                        return this.httpPost("{:url('admin/upload/group/delete')}", {
                            group_id
                        })
                    }).then(res => {
                        if (res.status) {
                            this.$message.success(res.msg);
                            this.getGroupList()
                        }
                    }).catch(err => {
                    })
                },
                editGroup(inputValue = '', group_id = 0) {
                    var that = this;
                    that.$prompt('请输入分组名称', '新增分组', {
                        confirmButtonText: '保存',
                        cancelButtonText: '取消',
                        inputValue,
                        roundButton: true,
                        closeOnClickModal: false,
                        beforeClose: (action, instance, done) => {
                            if (action === 'confirm') {
                                this.httpPost("{:url('admin/upload/group')}", {
                                    group_name: instance.inputValue,
                                    file_type: this.file_type,
                                    group_id
                                }).then(res => {
                                    if (res.status) {
                                        this.$message.success(res.msg);
                                        this.getGroupList()
                                        done()
                                    }
                                })
                            } else {
                                done()
                            }
                        }
                    }).catch(err => {
                    });
                },
                selectGroup(group_id) {

                },
                confirm() {
                }
            }
        });
    })

</script>

<style>
    .menu-container {
        max-height: 400px;
        overflow-y: scroll;
    }

    /* for Chrome */
    .menu-container::-webkit-scrollbar {
        display: none;
    }
</style>