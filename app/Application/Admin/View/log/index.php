<div class="page-container" v-cloak>
    <el-card>
        <div slot="header" class="breadcrumb">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>日志配置</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div>
            <div style="margin-bottom: 10px;">
                <el-alert
                        :closable="false"
                        title="记录后台操作日志"
                        description="只记录post请求的数据，内容包含请求数、结果数据。"
                        type="success">
                </el-alert>
            </div>
            <el-form size="small" label-width="80px">
                <el-form-item label="日志记录">
                    <el-switch
                            @change="submitEvent"
                            v-model="setting.is_open"
                            :active-value="1"
                            :inactive-value="0"
                            inactive-color="#C0CCDA"
                            active-text="开启"
                            inactive-text="关闭">
                    </el-switch>
                </el-form-item>
            </el-form>
        </div>
        <div>
            <el-table
                    size="small"
                    :data="file_list"
                    style="width: 100%">
                <el-table-column
                        fixed
                        prop="file_name"
                        label="文件名称"
                        min-width="180">
                </el-table-column>
                <el-table-column
                        prop="file_size"
                        label="大小"
                        width="100">
                </el-table-column>
                <el-table-column
                        prop="file_path"
                        min-width="380"
                        label="文件路径">
                </el-table-column>
                <el-table-column
                        fixed="right"
                        align="center"
                        min-width="180"
                        label="操作">
                    <template slot-scope="{row}">
                        <el-button @click="downloadEvent(row)" size="small" type="primary">下载</el-button>
                        <el-button @click="deleteEvent(row)" size="small" type="danger">删除</el-button>
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
                file_list: [],
                setting: {
                    is_open: 1,
                }
            },
            mounted() {
                this.getInfo()
            },
            methods: {
                downloadEvent({file_name}) {
                    location.href = "{:url('admin/log/index/download')}" + "/" + file_name
                },
                getInfo() {
                    this.httpGet("{:url('admin/log/index/setting/info')}", {}).then(res => {
                        if (res.status) {
                            let {setting = {}, file_list = []} = res.data
                            this.file_list = file_list
                            for (let key in setting) {
                                //为了输入框显示，这里将对象转成字符串
                                if (typeof setting[key] == "object") {
                                    setting[key] = JSON.stringify(setting[key])
                                }
                            }
                            this.setting = {
                                ...setting
                            }
                        }
                    })
                },
                deleteEvent({file_name}) {
                    this.$confirm("是否确认删除该记录？", '提示').then(() => {
                        this.httpPost("{:url('admin/log/index/delete')}/" + file_name, {}).then(res => {
                            if (res.status) {
                                this.$message.success(res.msg)
                                this.getInfo()
                            }
                        })
                    })
                },
                submitEvent() {
                    this.httpPost("{:url('admin/log/index/setting')}", {setting: this.setting}).then(res => {
                        if (res.status) {
                            this.$message.success(res.msg)
                        }
                    })
                }
            }
        })
    })
</script>