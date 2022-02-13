<script>
    window.__vue_upload_mixin = {
        data() {
            return {
                move_group_id: -1,
                upload_url: "{:url('admin/upload/file')}",
                group_list: [],
                now_group: -1,
            }
        },
        mounted() {
            this.getGroupList()
        },
        computed: {
            selected_file_ids() {
                let selected_file_ids = []
                this.selected_file_list.forEach(file => {
                    selected_file_ids.push(file.file_id)
                })
                return selected_file_ids
            },
            selected_file_list() {
                let result = [];
                this.data_list.forEach((file) => {
                    if (file.is_select) {
                        result.push(file);
                    }
                })
                return result;
            },
            uploadData() {
                return {
                    group_id: this.now_group > 0 ? this.group_id : 0
                }
            }
        },
        methods: {
            moveGroup() {
            },
            clickDeleteSelected() {
                this.$confirm('确认删除？', {
                    type: 'warning'
                }).then(() => {
                    console.log('clickDeleteSelected', this.selected_file_ids)
                    return this.httpPost("{:url('admin/upload/file/delete')}", {selected_file_ids: this.selected_file_ids})
                }).then(res => {
                    if (res.status) {
                        this.$message.success(res.msg)
                        this.GetList()
                    }
                }).catch(err => {
                })
            },
            clickCancelSelected() {
                let data_list = this.data_list
                for (var i = 0; i < data_list.length; i++) {
                    data_list[i].is_select = false
                }
                this.data_list = [...data_list]
            },
            selectFileEvent(index) {
                let data_list = this.data_list
                data_list[index]['is_select'] = !this.data_list[index].is_select
                this.data_list = [...data_list]
            },
            handleExceed() {
            },
            handleUploadChange() {
            },
            handleUploadSuccess(e) {
                console.log('handleUploadSuccess', e)
                this.GetList()
            },
            handleUploadProgress() {
            },
            beforeUploadEvent() {
            },
            selectGroup(group_id) {
                this.now_group = group_id
            },
            getGroupList() {
                this.httpGet("{:url('admin/upload/group/lists')}", {
                    file_type: this.file_type,
                }).then(res => {
                    if (res.status) {
                        let {group_list = []} = res.data
                        this.group_list = group_list
                        this.GetList()
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
        }
    }
</script>