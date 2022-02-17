<div class="page-container" v-cloak>
    <el-card>
        <div slot="header" class="breadcrumb">
            <el-breadcrumb separator="/">

                <el-breadcrumb-item>上传配置</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div>
            <el-form size="small" label-width="120px">
                <el-form-item required label="上传驱动">
                    <el-select v-model="setting.upload_drive" placeholder="请选择上传驱动">
                        <el-option v-for="(item,index) in driver_list" :value="item.value"
                                   :label="item.name"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item required label="本地上传目录">
                    <el-input v-model="setting.upload_file_dir" placeholder="本地上传文件存储的目录"></el-input>
                    <div class="form-small">
                        <small> 为了方便访问文件都是放在public目录，填写upload会存放在public/upload目录下</small>
                    </div>
                </el-form-item>
                <el-form-item required label="文件访问域名">
                    <el-input v-model="setting.upload_domain" placeholder="本地上传文件存储的目录"></el-input>
                    <div class="form-small">
                        <small>文件访问的域名，注意需要以 / 结尾，本地上传驱动可以使用 /，如果使用对象存储的镜像回源，直接填写对象存储的访问域名</small>
                    </div>
                </el-form-item>
                <el-form-item required label="上传格式">
                    <el-input v-model="setting.upload_allow_ext" placeholder="请输入允许上传格式"
                              style="width: 600px"></el-input>
                </el-form-item>
                <el-form-item required label="上传文件大小">
                    <el-input v-model="setting.upload_file_size" placeholder="请输入允许上传文件的大小">
                        <template slot="append">KB</template>
                    </el-input>
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
                driver_list: [],
                setting: {
                    upload_drive: "local",
                    upload_file_dir: "public/upload",
                    upload_domain: "/",
                    upload_allow_ext: "jpg|jpeg|gif|bmp|png|doc|docx|xls|xlsx|ppt|pptx|pdf|txt|rar|zip|mp4",
                    upload_file_size: 2048
                }
            },
            mounted() {
                this.getInfo()
            },
            methods: {
                getInfo() {
                    this.httpGet("{:url('admin/upload/setting/info')}", {}).then(res => {
                        if (res.status) {
                            let {setting = {}, driver_list = []} = res.data
                            this.driver_list = driver_list
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