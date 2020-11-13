/**
 * 异世界百科 MW帮助 左侧TOC组件
 */
import $ from "jquery";

/** @typedef {Object} TocData
 *  @type {string} TocData.id
 *  @property {string} name
 *  @property {TocData[]|undefined} items
 */
export class IsekaiToc {
    /**
     * 构造函数
     * @param el
     */
    constructor(el){
        this.el = $(el);
        this.scrollOffset = 80;
        this.scrollSpyOffset = 100;
        this.sectionIdList = [];
        this.sections = [];
        this.pauseScrollSpy = false;

        this.bindEvents();
    }

    /**
     * 增加子目录项目
     * @param container
     * @param {TocData[]} items
     * @private
     */
    _addSubItems(container, items){
        items.forEach(item => {
            if(item.items) { //有子项
                var el = container.append(
                    '<div class="mdui-collapse-item mdui-collapse-item-open">\n' +
                    '    <li class="mdui-collapse-item-header mdui-list-item isekai-toc-item" data-target="' + item.id + '">\n' +
                    '        <a href="#' + item.id + '" class="mdui-list-item-content mdui-ripple">\n' +
                    '            ' + item.name + '\n' +
                    '        </a>\n' +
                    '        <button class="mdui-btn mdui-btn-icon mdui-ripple isekai-collapse-btn isekai-btn-rect">\n' +
                    '            <i class="mdui-icon material-icons">keyboard_arrow_down</i>\n' +
                    '        </button>\n' +
                    '    </li>' +
                    '    <div class="mdui-collapse-item-body isekai-collapse-item-body mdui-list"></div>\n' +
                    '</div>'
                ).find('.isekai-collapse-item-body:last');
                this._addSubItems(el, item.items);
            } else {
                container.append('<a href="#' + item.id + '" class="mdui-list-item mdui-ripple" data-target="' + item.id + '">' + item.name + '</a>');
            }
        });
    }

    /**
     * 设置项目列表
     * @param {TocData[]} items TOC信息列表
     * @returns {boolean}
     */
    setItems(items){
        this._addSubItems(this.el, items);
        this.updateScrollSpy();
        return true;
    }

    /**
     * 设置激活的项目
     * @param {string|boolean} name 目录的id，false则取消所有激活
     * @returns {boolean}
     */
    setActive(name){
        this.el.find('.mdui-list-item').removeClass('mdui-list-item-active')
        if(name) {
            this.el.find('.mdui-list-item[data-target=' + name + ']').addClass('mdui-list-item-active');
        }
        return true;
    }

    /**
     * 绑定事件
     */
    bindEvents(){
        var that = this;
        this.el.on('click', '.isekai-collapse-btn', function(){
            that.toggleCollapse($(this));
        });

        $(window).on('scroll', (event) => {
            this._updateActiveLink(window.scrollY);
        });

        $(window).on('resize', (event) => {
            this.updateScrollSpy();
        });

        //锚链接偏移
        this.el.on('click', 'a', function(){
            var target = $(this).attr('data-target') || $(this).parent('.mdui-list-item').attr('data-target');
            if(target) {
                that.setActive(target);
                return that._scrollToAnchor("#" + target);
            }
        });

        this.initScrollSpy();
    }

    /**
     * 加载滚动条监听
     */
    initScrollSpy(){
        var sectionIdList = [];
        this.el.find('.mdui-list-item[data-target]').each(function(){
            sectionIdList.push($(this).attr('data-target'));
        });
        this.sectionIdList = sectionIdList;
        this.updateScrollSpy();
    }

    /**
     * 更新滚动条监听的section位置
     */
    updateScrollSpy(){
        var maxOffset = $('html').height() - $(window).height() - 1;
        var dom;
        this.sections.splice(0, this.sections.length);
        this.sectionIdList.forEach((id) => {
            if((dom = $('#' + id)).length > 0){
                this.sections.push({
                    id,
                    offset: Math.min(dom.offset().top, maxOffset),
                });
            }
        });
    }

    /**
     * 根据滚动条位置更新激活项目
     * @param {number} scrollTop
     * @private
     */
    _updateActiveLink(scrollTop){
        if(this.pauseScrollSpy) return;
        var currentSection = null;
        this.sections.forEach((section) => {
            if(window.scrollY + this.scrollSpyOffset > section.offset){
                if(!currentSection || currentSection.offset < section.offset) {
                    currentSection = section;
                }
            }
        });
        if(currentSection){
            this.setActive(currentSection.id);
        } else {
            this.setActive(false);
        }
    }

    /**
     * 切换collapse的展开状态
     * @param el
     */
    toggleCollapse(el){
        var container = el.parent('li').parent('.mdui-collapse-item');
        if(container.hasClass('mdui-collapse-item-open')){
            this.closeCollapse(container);
        } else {
            this.openCollapse(container);
        }
    }

    /**
     * 展开collapse
     * @param el
     */
    openCollapse(el){
        el = mdui.$(el[0]);
        var body = el.children('.mdui-collapse-item-body');
        body.height(body[0].scrollHeight).transitionEnd(() => this.transitionEnd(body, true));
        el.addClass('mdui-collapse-item-open');
    }

    /**
     * 关闭collapse
     * @param el
     */
    closeCollapse(el){
        el = mdui.$(el[0]);
        var body = el.children('.mdui-collapse-item-body');
        body.transition(0)
            .height(body[0].scrollHeight)
            .reflow()
            .transition('')
            .height('')
            .transitionEnd(() => this.transitionEnd(body, false));
        el.removeClass('mdui-collapse-item-open');
    }

    /**
     * 结束展开、关闭动画
     * @param $content
     * @param {boolean} isOpen
     */
    transitionEnd($content, isOpen) {
        if (isOpen) {
            $content.transition(0).height('auto').reflow().transition('');
        } else {
            $content.height('');
        }
    }

    /**
     * 平滑移动到锚点
     * @param link
     * @returns {boolean}
     * @private
     */
    _scrollToAnchor(link){
        var target = $(link);
        this.pauseScrollSpy = true;
        if(target.length > 0){
            target.click(function(){ return false; });
            var position = target.offset().top - this.scrollOffset;
            $('html, body').animate({
                scrollTop: position,
            }, 500, 'swing', () => {
                this.pauseScrollSpy = false;
            });
            return false;
        } else {
            return true;
        }
    }
}