<div class="page-container" v-cloak>
    <el-card>
        <div slot="header" class="breadcrumb">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><a href="{:url('admin/main/index')}">首页</a></el-breadcrumb-item>
                <el-breadcrumb-item><a href="{:url('admin/setting/index')}">配置列表</a></el-breadcrumb-item>
                <el-breadcrumb-item>站点配置</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div>
            <el-form size="small" label-width="120px">
                <el-form-item required label="站点名称">
                    <el-input v-model="setting.site_name" placeholder="请输入站点名称"></el-input>
                </el-form-item>
                <el-form-item required label="站点介绍">
                    <el-input v-model="setting.site_description" placeholder="请输入关于站点的介绍" style="width: 400px" :rows="5"
                              type="textarea"></el-input>
                </el-form-item>
                <el-form-item required label="站点访问路径">
                    <el-input v-model="setting.site_dir" placeholder="请输入站点访问路径，默认是 / 不需要修改"></el-input>
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
                    site_name: "Hcms",
                    site_description: "Hcms 是基于Hyperf 框架开发的模块化管理系统，致力于定制化项目模块化开发规范。",
                    site_dir: "/",
                }
            },
            mounted() {
                this.getInfo()
            },
            methods: {
                getInfo() {
                    this.httpGet("{:url('admin/setting/site/info')}", {}).then(res => {
                        if (res.status) {
                            let {setting = {}} = res.data
                            this.setting = setting
                        }
                    })
                },
                submitEvent() {
                    this.httpPost("{:url('admin/setting/site')}", {setting: this.setting}).then(res => {
                        if (res.status) {
                            this.$message.success(res.msg)
                        }
                    })
                }
            }
        })
    })
</script>