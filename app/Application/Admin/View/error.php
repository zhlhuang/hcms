{__NOLAYOUT__}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <title>页面错误提示</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="renderer" content="webkit"/>
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    <link rel="stylesheet" href="/assets/element-ui@2.15.6/index.css">
    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/vue.js"></script>
    <script src="/assets/js/vue-common.js"></script>
    <script src="/assets/js/vue-list.js"></script>
    <script src="/assets/element-ui@2.15.6/index.js"></script>
</head>

<body>
<div id="app" v-cloak>
    <div class="loading">
        <el-empty description="{$description}"></el-empty>
        <div>
            {$location}
        </div>
        <div>
            {$content}
        </div>
    </div>
</div>
</body>
<script>
    $(function () {
        new Vue({
            el: "#app",
            data: {},
            mounted() {
            },
            methods: {}
        })
    })
</script>
<style>
</style>
</html>
