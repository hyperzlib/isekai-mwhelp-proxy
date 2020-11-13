{include file="../view/header"}
<div class="mdui-container main-container mw-parser-output page-index">
    <link rel="stylesheet" href="/static/css/pageIndex.css">
    <h1 class="mdui-hidden">首页</h1>
    <img class="mediawiki-logo mdui-float-right" src="/static/images/mediawiki-logo.svg">
    <h1 class="page-title mdui-text-color-theme">
        MediaWiki帮助
    </h1>
    <div class="mdui-typo">
        <p>此帮助文档镜像由<a href="https://www.isekai.cn" target="_blank">异世界百科</a>提供</p>
        <p>页面内容均来自<a href="https://www.mediawiki.org/wiki/Help:Contents/zh" target="_blank">MediaWiki帮助</a>，如果您想要更改页面的翻译，请前往MediaWiki帮助页面。</p>
    </div>
    <!--开始卡片-->
    <div class="mdui-row help-cards">
        <!--阅读帮助-->
        <div class="help-card mdui-col-xs-12 mdui-col-md-6 mdui-col-lg-4">
            <div class="mdui-card">
                <div class="mdui-card-media">
                    <img src="/static/images/card-reading.svg">
                    <div class="mdui-card-media-covered">
                        <div class="mdui-card-primary">
                            <div class="mdui-card-primary-title">阅读</div>
                            <div class="mdui-card-primary-subtitle">
                                <a href="https://www.vecteezy.com/members/nightwolfdezines" target="_blank">
                                    图片由 nightwolfdezines 制作
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mdui-card-actions mdui-card-actions-stacked">
                    {foreach $nav->getItemsByParent('reading') as $item}
                    <a class="mdui-btn mdui-ripple" href="{$item.url}">{$item.title}</a>
                    {/foreach}
                </div>
            </div>
        </div>
        <!--编辑帮助-->
        <div class="help-card mdui-col-xs-12 mdui-col-md-6 mdui-col-lg-4">
            <div class="mdui-card">
                <div class="mdui-card-media">
                    <img src="/static/images/card-editing.svg">
                    <div class="mdui-card-media-covered">
                        <div class="mdui-card-primary">
                            <div class="mdui-card-primary-title">编辑</div>
                            <div class="mdui-card-primary-subtitle">
                                <a href="https://www.vecteezy.com/members/vectorbox_studio" target="_blank">
                                    图片由 Vectorbox Studio 制作
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mdui-card-actions mdui-card-actions-stacked">
                    {foreach $nav->getItemsByParent('editing') as $item}
                    <a class="mdui-btn mdui-ripple" href="{$item.url}">{$item.title}</a>
                    {/foreach}
                </div>
            </div>
        </div>
        <!--进阶编辑帮助-->
        <div class="help-card mdui-col-xs-12 mdui-col-md-6 mdui-col-lg-4">
            <div class="mdui-card">
                <div class="mdui-card-media">
                    <img src="/static/images/card-advanced-editing.svg">
                    <div class="mdui-card-media-covered">
                        <div class="mdui-card-primary">
                            <div class="mdui-card-primary-title">进阶编辑</div>
                            <div class="mdui-card-primary-subtitle">
                                <a href="https://www.vecteezy.com/members/vectorbox_studio" target="_blank">
                                    图片由 Vectorbox Studio 制作
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mdui-card-actions mdui-card-actions-stacked">
                    {foreach $nav->getItemsByParent('advanced-editing') as $item}
                    <a class="mdui-btn mdui-ripple" href="{$item.url}">{$item.title}</a>
                    {/foreach}
                </div>
            </div>
        </div>
        <!--合作帮助-->
        <div class="help-card mdui-col-xs-12 mdui-col-md-6 mdui-col-lg-4">
            <div class="mdui-card">
                <div class="mdui-card-media">
                    <img src="/static/images/card-collaboration.svg">
                    <div class="mdui-card-media-covered">
                        <div class="mdui-card-primary">
                            <div class="mdui-card-primary-title">合作</div>
                            <div class="mdui-card-primary-subtitle">
                                <a href="https://www.vecteezy.com/members/vectorbox_studio" target="_blank">
                                    图片由 Vectorbox Studio 制作
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mdui-card-actions mdui-card-actions-stacked">
                    {foreach $nav->getItemsByParent('collaboration') as $item}
                    <a class="mdui-btn mdui-ripple" href="{$item.url}">{$item.title}</a>
                    {/foreach}
                </div>
            </div>
        </div>
        <!--管理帮助-->
        <div class="help-card mdui-col-xs-12 mdui-col-md-6 mdui-col-lg-4">
            <div class="mdui-card">
                <div class="mdui-card-media">
                    <img src="/static/images/card-wiki-admin.svg">
                    <div class="mdui-card-media-covered">
                        <div class="mdui-card-primary">
                            <div class="mdui-card-primary-title">管理</div>
                            <div class="mdui-card-primary-subtitle">
                                <a href="https://www.vecteezy.com/members/vectorbox_studio" target="_blank">
                                    图片由 Vectorbox Studio 制作
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mdui-card-actions mdui-card-actions-stacked">
                    {foreach $nav->getItemsByParent('wiki-admin') as $item}
                    <a class="mdui-btn mdui-ripple" href="{$item.url}">{$item.title}</a>
                    {/foreach}
                </div>
            </div>
        </div>
        <!--个性化帮助-->
        <div class="help-card mdui-col-xs-12 mdui-col-md-6 mdui-col-lg-4">
            <div class="mdui-card">
                <div class="mdui-card-media">
                    <img src="/static/images/card-custom.svg">
                    <div class="mdui-card-media-covered">
                        <div class="mdui-card-primary">
                            <div class="mdui-card-primary-title">个性化</div>
                            <div class="mdui-card-primary-subtitle">
                                <a href="https://www.vecteezy.com/members/vectorbox_studio" target="_blank">
                                    图片由 Vectorbox Studio 制作
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mdui-card-actions mdui-card-actions-stacked">
                    {foreach $nav->getItemsByParent('personal-custom') as $item}
                    <a class="mdui-btn mdui-ripple" href="{$item.url}">{$item.title}</a>
                    {/foreach}
                </div>
            </div>
        </div>
    </div>
    <!--结束卡片-->
    <script type="text/javascript" src="/static/js/pageIndex.js"></script>
</div>
{include file="../view/footer"}