<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <title>Hcms</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="renderer" content="webkit"/>
    <meta http-equiv="Cache-Control" content="no-siteapp"/>

    <link rel="stylesheet" href="/assets/element-ui@2.15.6/index.css?v={$version}">
    <link rel="stylesheet" href="/assets/css/index.css?v={$version}">

    <script src="/assets/js/jquery.min.js?v={$version}"></script>
    {if $env == 'dev'}
    <script src="/assets/js/vue.js?v={$version}"></script>
    {else /}
    <!-- 正式版本使 min -->
    <script src="/assets/js/vue-min.js?v={$version}"></script>
    {/if}
    <script src="/assets/js/vue-common.js?v={$version}"></script>
    <script src="/assets/js/vue-list.js?v={$version}"></script>
    <script src="/assets/element-ui@2.15.6/index.js?v={$version}"></script>
</head>
<body style="background-color: #f8f8f8;height: 100%">
<div>
    {__CONTENT__}
</div>
</body>
</html>
