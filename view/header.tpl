<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no"/>
    <meta name="renderer" content="webkit"/>
    <meta name="force-rendering" content="webkit"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <title>{$title} - MediaWiki帮助 | 异世界百科</title>
    <link rel="stylesheet" href="/static/css/mdui.min.css">
    <link rel="stylesheet" href="/static/css/index.css">
    <script>
    function $load(callback){
        window.addEventListener('load', callback);
    }
    </script>
</head>
<body class="mdui-drawer-body-left mdui-appbar-with-toolbar mdui-theme-primary-indigo mdui-theme-accent-pink mdui-theme-layout-auto">
<!--导航栏-->
<header class="mdui-appbar mdui-appbar-fixed">
    <div class="mdui-toolbar mdui-color-theme">
        <span class="mdui-btn mdui-btn-icon mdui-ripple mdui-ripple-white" mdui-drawer="{target: '#main-drawer', swipe: true}">
            <i class="mdui-icon material-icons">menu</i>
        </span>
        <a href="/" class="mdui-typo-headline mdui-hidden-sm-down mdui-ripple mdui-ripple-white">MediaWiki帮助</a>
        <span class="mdui-typo-title">{$title}</span>
        <div class="mdui-toolbar-spacer"></div>
        <a href="https://www.isekai.cn" target="_blank" class="mdui-hidden-sm-down mdui-btn mdui-ripple mdui-ripple-white">异世界百科</a>
    </div>
</header>
<!--抽屉-->
<div class="mdui-drawer mdui-shadow-4" id="main-drawer">
    <div class="mdui-tab mdui-tab-full-width mdui-shadow-2 isekai-nav-tab" mdui-tab>
        <a href="#navigator" class="mdui-ripple{if condition="$toc->isEmpty()"} mdui-tab-active{/if}">
            导航
        </a>
        <a href="#nav-toc" class="mdui-ripple mdui-tab-active{if condition="!$toc->isEmpty()"} mdui-tab-active{/if}">
            目录
        </a>
    </div>
    <!--导航-->
    <div id="navigator">
        <div class="mdui-list" mdui-collapse="{accordion: true}">
            {$navHtml|raw}
        </div>
    </div>
    <div id="nav-toc">
        <div class="mdui-list isekai-toc">
            {$tocHtml|raw}
        </div>
    </div>
</div>