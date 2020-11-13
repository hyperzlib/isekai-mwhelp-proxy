import $ from 'jquery';

export class IsekaiCollapse {
    constructor(){
        this.update();
    }

    update(){
        this.collapses = [];
        var that = this;
        var i = 1;
        $('.mw-collapsible').each(function(){
            var dom = $(this);
            var title = dom.find('.mw-collapsible-toggle').text();
            var content = dom.find('.mw-collapsible-content').html();
            var dstDomId = "isekai-collapse-" + i.toString();

            var dstHtml =
                '<div class="mdui-panel" id="' + dstDomId + '" mdui-panel>' +
                '<div class="mdui-panel-item">' +
                '<div class="mdui-panel-item-header">' +
                '<div class="mdui-panel-item-title"></div>' +
                '<i class="mdui-panel-item-arrow mdui-icon material-icons">keyboard_arrow_down</i>' +
                '</div>' +
                '<div class="mdui-panel-item-body"></div>' +
                '</div>';
            var dstDom = $(dstHtml);
            dstDom.find('.mdui-panel-item-title').text(title);
            dstDom.find('.mdui-panel-item-body').html(content);
            //生成新的dom
            dom.replaceWith(dstDom);
            var inst = new mdui.Panel(dstDomId);
            that.collapses.push(inst);
            i ++;
        })
    }
}