<div class="page-container" v-cloak>
    <el-card>
        <div slot="header" class="breadcrumb">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><a href="{:url('admin/main/index')}">首页</a></el-breadcrumb-item>
                <el-breadcrumb-item>上传配置</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div>
            <el-form size="small" label-width="120px">
                <el-form-item required label="上传驱动">
                    <el-select v-model="setting.upload_drive" placeholder="请选择上传驱动">
                        <el-option value="local" label="本地上传"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item required label="本地上传目录">
                    <el-input v-model="setting.upload_file_dir" placeholder="本地上传文件存储的目录"></el-input>
                    <div class="form-small">
                        <small> 为了方便访问，建议放在public/upload目录下</small>
                    </div>
                </el-form-item>
                <el-form-item required label="允许上传格式">
                    <el-input v-model="setting.upload_allow_ext" placeholder="请输入关于站点的介绍"
                              style="width: 600px"></el-input>
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
                    upload_drive: "local",
                    upload_file_dir: "public/upload",
                    upload_allow_ext: "jpg|jpeg|gif|bmp|png|doc|docx|xls|xlsx|ppt|pptx|pdf|txt|rar|zip|mp4"
                }
            },
            mounted() {
                this.getInfo()
            },
            methods: {
                getInfo() {
                    this.httpGet("{:url('admin/upload/setting/info')}", {}).then(res => {
                        if (res.status) {
                            let {setting = {}} = res.data
                            if (setting.upload_drive) {
                                this.setting = setting
                            }
                        }
                    })
                },
                submitEvent() {
                    this.httpPost("{:url('admin/upload/setting')}", {setting: this.setting}).then(res => {
                        if (res.status) {
                            this.$message.success(res.msg)
                        }
                    })
                }
            }
        })
    })
</script>