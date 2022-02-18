<div class="page-container" v-cloak>
    <el-card>
        <div slot="header" class="breadcrumb">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>缓存</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div>
            <el-form label-position="left" size="mini" label-width="120px">
                <el-form-item label="缓存驱动">
                    {{config_info.driver}}
                </el-form-item>
                <el-form-item label="redis连接">
                    {{config_info.redis_status==1?'已连接':'未连接'}}
                </el-form-item>
                <template v-if="config_info.redis_status==1">
                    <el-form-item label="redis版本">
                        {{config_info.redis_info.version}}
                    </el-form-item>
                    <el-form-item label="redis连接信息">
                        {{config_info.redis_info.host}}:{{config_info.redis_info.port}}
                    </el-form-item>
                    <el-form-item label="redis可用内存">
                        {{config_info.redis_info.total_system_memory_human}}
                    </el-form-item>
                </template>
            </el-form>
        </div>
        <div>
            <el-table
                    size="small"
                    :data="cache_list"
                    style="width: 100%">
                <el-table-column
                        prop="cache_key"
                        label="cache_key">
                </el-table-column>
                <el-table-column
                        fixed="right"
                        align="center"
                        width="180"
                        label="操作">
                    <template slot-scope="{row}">
                        <el-button @click="detailEvent(row)" size="small" type="primary">查看</el-button>
                        <el-button @click="deleteEvent(row)" size="small" type="danger">删除</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>
    </el-card>

    <el-dialog
            title="详细内容"
            :visible.sync="detail_visible"
            width="800px">
        <div>
            {{detail}}
        </div>
        <div slot="footer" class="dialog-footer">
            <el-button size="small" type="primary" @click="detail_visible = false">确 定</el-button>
        </div>
    </el-dialog>
</div>

<script>
    $(function () {
        new Vue({
            el: ".page-container",
            data: {
                detail_visible: false,
                config_info: {},
                keys: [],
                detail: ""
            },
            mounted() {
                this.getInfo()
            },
            computed: {
                cache_list() {
                    let cache_list = []
                    this.keys.forEach(item => {
                        cache_list.push({
                            cache_key: item
                        })
                    })
                    return cache_list
                }
            },
            methods: {
                getInfo() {
                    this.httpGet("{:url('admin/cache/index/info')}", {}).then(res => {
                        if (res.status) {
                            let {keys = {}, config_info = {}} = res.data
                            this.keys = keys
                            this.config_info = config_info
                        }
                    })
                },
                detailEvent({cache_key}) {
                    this.detail = ''
                    this.httpGet("{:url('admin/cache/index/detail')}/" + cache_key, {}).then(res => {
                        if (res.status) {
                            this.detail_visible = true
                            let {detail = "", format_detail = ''} = res.data
                            this.detail = format_detail
                        }
                    })
                },
                deleteEvent({cache_key}) {
                    this.$confirm("是否确认删除该记录？", '提示').then(() => {
                        this.httpPost("{:url('admin/cache/index/delete')}/" + cache_key, {}).then(res => {
                            if (res.status) {
                                this.$message.success(res.msg)
                                this.getInfo()
                            }
                        })
                    })
                }
            }
        })
    })
</script>