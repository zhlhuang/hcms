<div class="page-container" v-cloak>
    <el-card>
        <div>
            hello main {$msg}
        </div>
    </el-card>
</div>

<script>
    $(function () {
        new Vue({
            el: ".page-container"
        })
    })
</script>