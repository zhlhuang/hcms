<div class="page-container" v-cloak>
    <el-card>
        <div slot="header" class="breadcrumb">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>日志配置</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div>
            <el-form size="small" label-width="120px">
                <el-form-item label="日志记录">
                    <el-switch
                            v-model="setting.is_open"
                            :active-value="1"
                            :inactive-value="0"
                            inactive-color="#C0CCDA"
                            active-text="开启"
                            inactive-text="关闭">
                    </el-switch>
                </el-form-item>
                <el-form-item>
                    <el-button @click="submitEvent" type="primary">保存</el-button>
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
                setting: {
                    is_open: 1,
                }
            },
            mounted() {
                this.getInfo()
            },
            methods: {
                getInfo() {
                    this.httpGet("{:url('admin/log/setting/info')}", {}).then(res => {
                        if (res.status) {
                            let {setting = {}} = res.data
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
                submitEvent() {
                    this.httpPost("{:url('admin/log/setting')}", {setting: this.setting}).then(res => {
                        if (res.status) {
                            this.$message.success(res.msg)
                        }
                    })
                }
            }
        })
    })
</script>