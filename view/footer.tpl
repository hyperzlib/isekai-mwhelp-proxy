<div class="mdui-color-theme">
    <div class="mdui-container-fluid">
        <div class="mdui-row">
            {if condition="$nav->hasPrev()"}
                <a href="{$nav->prev['url']}" class="mdui-ripple mdui-color-theme mdui-col-xs-10 mdui-col-sm-6 doc-footer-nav-left">
                    <div class="doc-footer-nav-text">
                        <i class="mdui-icon material-icons">arrow_backward</i>
                        <span class="doc-footer-nav-direction">上一章</span>
                        <div class="doc-footer-nav-chapter">{$nav->prev['title']}</div>
                    </div>
                </a>
            {else}
                <div class="mdui-col-xs-2 mdui-col-sm-6 doc-footer-nav-left"></div>
            {/if}

            {if condition="$nav->hasNext()"}
                <a href="{$nav->next['url']}" class="mdui-ripple mdui-color-theme mdui-col-xs-10 mdui-col-sm-6 doc-footer-nav-right">
                    <div class="doc-footer-nav-text">
                        <i class="mdui-icon material-icons">arrow_forward</i>
                        <span class="doc-footer-nav-direction">下一章</span>
                        <div class="doc-footer-nav-chapter">{$nav->next['title']}</div>
                    </div>
                </a>
            {else}
                <div class="mdui-col-xs-2 mdui-col-sm-6 doc-footer-nav-right"></div>
            {/if}
        </div>
    </div>
</div>
<script src="/static/js/mdui.min.js"></script>
<script src="/static/js/index.js"></script>
</body>
</html>