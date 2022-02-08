<div class="page-container" v-cloak>
    <el-card>
        <div slot="header" class="breadcrumb">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><a href="/admin/main/index">首页</a></el-breadcrumb-item>
                <el-breadcrumb-item><a href="/admin/access/index">菜单与权限</a></el-breadcrumb-item>
                <el-breadcrumb-item>{$title}</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div>
            <el-form size="small" label-width="80px">
                <el-form-item label="上级权限">
                    <el-cascader
                            v-model="cascader_value"
                            filterable
                            :options="access_list"
                            :props="{ checkStrictly: true,value:'access_id',label:'access_name' }"
                            clearable></el-cascader>
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
                <el-form-item v-if="form.is_menu===1" label="菜单图标">
                    <span v-if="form.menu_icon" style="margin-right: 10px;font-size: 20px;">
                        <i :class="form.menu_icon"></i>
                    </span>
                    <el-button @click="show_icon=true" type="default" size="small">
                        选择图标
                    </el-button>
                </el-form-item>
                <el-form-item>
                    <el-button @click="submitEvent" type="primary" size="small">
                        提交
                    </el-button>
                </el-form-item>
            </el-form>
        </div>
    </el-card>

    <!-- 选择图标弹窗 -->
    <el-dialog
            title="选择图标"
            :visible.sync="show_icon"
            width="500px">
        <div style="max-height: 300px;overflow:scroll;">
            <el-row>
                <el-col v-for="(item,index) in icon_list" :key="index" :span="4">
                    <div @click="confirmIconEvent(item)" class="icon-item">
                        <i :class="item"></i>
                    </div>
                </el-col>
            </el-row>
        </div>
        <div slot="footer" class="dialog-footer">
            <el-button size="small" @click="show_icon = false">取 消</el-button>
        </div>
    </el-dialog>
</div>

<script>
    $(function () {
        new Vue({
            el: ".page-container",
            data: {
                cascader_value: [],
                access_list: [],
                show_icon: false,
                icon_list_string: 'el-icon-delete-solid,el-icon-delete,el-icon-s-tools,el-icon-setting,el-icon-user-solid,el-icon-user,el-icon-phone,el-icon-phone-outline,el-icon-more,el-icon-more-outline,el-icon-star-on,el-icon-star-off,el-icon-s-goods,el-icon-goods,el-icon-warning,el-icon-warning-outline,el-icon-question,el-icon-info,el-icon-remove,el-icon-circle-plus,el-icon-success,el-icon-error,el-icon-zoom-in,el-icon-zoom-out,el-icon-remove-outline,el-icon-circle-plus-outline,el-icon-circle-check,el-icon-circle-close,el-icon-s-help,el-icon-help,el-icon-minus,el-icon-plus,el-icon-check,el-icon-close,el-icon-picture,el-icon-picture-outline,el-icon-picture-outline-round,el-icon-upload,el-icon-upload2,el-icon-download,el-icon-camera-solid,el-icon-camera,el-icon-video-camera-solid,el-icon-video-camera,el-icon-message-solid,el-icon-bell,el-icon-s-cooperation,el-icon-s-order,el-icon-s-platform,el-icon-s-fold,el-icon-s-unfold,el-icon-s-operation,el-icon-s-promotion,el-icon-s-home,el-icon-s-release,el-icon-s-ticket,el-icon-s-management,el-icon-s-open,el-icon-s-shop,el-icon-s-marketing,el-icon-s-flag,el-icon-s-comment,el-icon-s-finance,el-icon-s-claim,el-icon-s-custom,el-icon-s-opportunity,el-icon-s-data,el-icon-s-check,el-icon-s-grid,el-icon-menu,el-icon-share,el-icon-d-caret,el-icon-caret-left,el-icon-caret-right,el-icon-caret-bottom,el-icon-caret-top,el-icon-bottom-left,el-icon-bottom-right,el-icon-back,el-icon-right,el-icon-bottom,el-icon-top,el-icon-top-left,el-icon-top-right,el-icon-arrow-left,el-icon-arrow-right,el-icon-arrow-down,el-icon-arrow-up,el-icon-d-arrow-left,el-icon-d-arrow-right,el-icon-video-pause,el-icon-video-play,el-icon-refresh,el-icon-refresh-right,el-icon-refresh-left,el-icon-finished,el-icon-sort,el-icon-sort-up,el-icon-sort-down,el-icon-rank,el-icon-loading,el-icon-view,el-icon-c-scale-to-original,el-icon-date,el-icon-edit,el-icon-edit-outline,el-icon-folder,el-icon-folder-opened,el-icon-folder-add,el-icon-folder-remove,el-icon-folder-delete,el-icon-folder-checked,el-icon-tickets,el-icon-document-remove,el-icon-document-delete,el-icon-document-copy,el-icon-document-checked,el-icon-document,el-icon-document-add,el-icon-printer,el-icon-paperclip,el-icon-takeaway-box,el-icon-search,el-icon-monitor,el-icon-attract,el-icon-mobile,el-icon-scissors,el-icon-umbrella,el-icon-headset,el-icon-brush,el-icon-mouse,el-icon-coordinate,el-icon-magic-stick,el-icon-reading,el-icon-data-line,el-icon-data-board,el-icon-pie-chart,el-icon-data-analysis,el-icon-collection-tag,el-icon-film,el-icon-suitcase,el-icon-suitcase-1,el-icon-receiving,el-icon-collection,el-icon-files,el-icon-notebook-1,el-icon-notebook-2,el-icon-toilet-paper,el-icon-office-building,el-icon-school,el-icon-table-lamp,el-icon-house,el-icon-no-smoking,el-icon-smoking,el-icon-shopping-cart-full,el-icon-shopping-cart-1,el-icon-shopping-cart-2,el-icon-shopping-bag-1,el-icon-shopping-bag-2,el-icon-sold-out,el-icon-sell,el-icon-present,el-icon-box,el-icon-bank-card,el-icon-money,el-icon-coin,el-icon-wallet,el-icon-discount,el-icon-price-tag,el-icon-news,el-icon-guide,el-icon-male,el-icon-female,el-icon-thumb,el-icon-cpu,el-icon-link,el-icon-connection,el-icon-open,el-icon-turn-off,el-icon-set-up,el-icon-chat-round,el-icon-chat-line-round,el-icon-chat-square,el-icon-chat-dot-round,el-icon-chat-dot-square,el-icon-chat-line-square,el-icon-message,el-icon-postcard,el-icon-position,el-icon-turn-off-microphone,el-icon-microphone,el-icon-close-notification,el-icon-bangzhu,el-icon-time,el-icon-odometer,el-icon-crop,el-icon-aim,el-icon-switch-button,el-icon-full-screen,el-icon-copy-document,el-icon-mic,el-icon-stopwatch,el-icon-medal-1,el-icon-medal,el-icon-trophy,el-icon-trophy-1,el-icon-first-aid-kit,el-icon-discover,el-icon-place,el-icon-location,el-icon-location-outline,el-icon-location-information,el-icon-add-location,el-icon-delete-location,el-icon-map-location,el-icon-alarm-clock,el-icon-timer,el-icon-watch-1,el-icon-watch,el-icon-lock,el-icon-unlock,el-icon-key,el-icon-service,el-icon-mobile-phone,el-icon-bicycle,el-icon-truck,el-icon-ship,el-icon-basketball,el-icon-football,el-icon-soccer,el-icon-baseball,el-icon-wind-power,el-icon-light-rain,el-icon-lightning,el-icon-heavy-rain,el-icon-sunrise,el-icon-sunrise-1,el-icon-sunset,el-icon-sunny,el-icon-cloudy,el-icon-partly-cloudy,el-icon-cloudy-and-sunny,el-icon-moon,el-icon-moon-night,el-icon-dish,el-icon-dish-1,el-icon-food,el-icon-chicken,el-icon-fork-spoon,el-icon-knife-fork,el-icon-burger,el-icon-tableware,el-icon-sugar,el-icon-dessert,el-icon-ice-cream,el-icon-hot-water,el-icon-water-cup,el-icon-coffee-cup,el-icon-cold-drink,el-icon-goblet,el-icon-goblet-full,el-icon-goblet-square,el-icon-goblet-square-full,el-icon-refrigerator,el-icon-grape,el-icon-watermelon,el-icon-cherry,el-icon-apple,el-icon-pear,el-icon-orange,el-icon-coffee,el-icon-ice-tea,el-icon-ice-drink,el-icon-milk-tea,el-icon-potato-strips,el-icon-lollipop,el-icon-ice-cream-square,el-icon-ice-cream-round',
                form: {
                    is_menu: 1,
                    sort: 100,
                }
            },
            computed: {
                parent_access_id() {
                    if (this.cascader_value.length > 0) {
                        return this.cascader_value[this.cascader_value.length - 1]
                    } else {
                        return 0
                    }
                },
                icon_list() {
                    return this.icon_list_string.split(',')
                }
            },
            mounted() {
                this.getInfo()
            },
            methods: {
                /**
                 * 获取联动组件的渲染值
                 * @param parent_access_id
                 */
                getCascaderValue(parent_access_id) {
                    this.access_list.forEach(item => {
                        if (item.children) {
                            item.children.forEach(it => {
                                if (parseInt(it.access_id) === parseInt(parent_access_id)) {
                                    this.cascader_value = [item.access_id, it.access_id]
                                    return
                                }
                            })

                            if (parseInt(item.access_id) === parseInt(parent_access_id)) {
                                this.cascader_value = [item.access_id]
                                return
                            }
                        }
                    })
                },
                /**
                 * 获取编辑所需信息
                 */
                getInfo() {
                    this.httpGet("{:url('admin/access/edit/info')}", {
                        ...this.getUrlQuery()
                    }).then(res => {
                        if (res.status) {
                            let {access_list = [], access = {}} = res.data
                            this.access_list = [
                                {
                                    access_id: 0,
                                    access_name: '一级菜单',
                                    children: []
                                },
                                ...access_list
                            ]
                            if (access.access_id) {
                                this.getCascaderValue(access.parent_access_id)
                                this.form = {
                                    ...access
                                }
                            }
                        } else {
                            this.$message.error(res.msg)
                        }
                    })
                },
                /**
                 * 提交信息
                 */
                submitEvent() {
                    this.httpPost("{:url('admin/access/edit')}", {
                        ...this.form,
                        parent_access_id: this.parent_access_id
                    }).then(res => {
                        if (res.status) {
                            this.$message.success(res.msg)
                            location.href = "{:url('admin/access/index')}"
                        } else {
                            this.$message.error(res.msg)
                        }
                    })
                },
                /**
                 * 选择icon之后的回调
                 * @param icon
                 */
                confirmIconEvent(icon) {
                    this.show_icon = false
                    this.form.menu_icon = icon
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
