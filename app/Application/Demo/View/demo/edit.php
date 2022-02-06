<div class="page-container" v-cloak>
    <el-card>
        <div slot="header" class="breadcrumb">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><a href="/admin/main/index">首页</a></el-breadcrumb-item>
                <el-breadcrumb-item><a href="/demo/demo/lists">列表示例</a></el-breadcrumb-item>
                <el-breadcrumb-item>{$title}</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div>
            <el-form size="small" label-width="80px">
                <el-form-item label="上级菜单">
                    <div>
                        <el-select v-model="form.parent_access_id">
                            <el-option :value="0" label="一级菜单"></el-option>
                        </el-select>
                    </div>
                    <div class="form-small">
                        <small>上级菜单，菜单最多三级</small>
                    </div>
                </el-form-item>
                <el-form-item label="名称">
                    <el-input v-model="form.access_name"></el-input>
                </el-form-item>
                <el-form-item label="Uri">
                    <el-input v-model="form.uri"></el-input>
                </el-form-item>
                <el-form-item label="参数">
                    <el-input v-model="form.params"></el-input>
                </el-form-item>
                <el-form-item label="排序">
                    <el-input v-model="form.sort" type="number"></el-input>
                    <div class="form-small">
                        <small>数值越小，约靠前</small>
                    </div>
                </el-form-item>
                <el-form-item label="是否菜单">
                    <el-radio-group v-model="form.is_menu" size="small">
                        <el-radio :label="1">菜单</el-radio>
                        <el-radio :label="0">仅权限</el-radio>
                    </el-radio-group>
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
                form: {
                    is_menu: 1,
                    sort: 100,
                    parent_access_id: 0,
                }
            },
            mounted() {
                this.getInfo()
            },
            methods: {
                /**
                 * 获取编辑所需信息
                 */
                getInfo() {
                    // this.httpGet("{:url('admin/access/edit/info')}", {
                    //     ...this.getUrlQuery()
                    // }).then(res => {
                    // })
                },
                /**
                 * 提交信息
                 */
                submitEvent() {
                    // this.httpPost("{:url('admin/access/edit')}", {
                    //     ...this.form,
                    // }).then(res => {
                    //
                    // })
                },
            }
        })
    })
</script>

<style>
</style>
