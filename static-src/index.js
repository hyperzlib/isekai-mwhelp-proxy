import "jquery";
import "./index.less";
import { IsekaiToc } from "./IsekaiToc";
import { IsekaiCollapse } from "./IsekaiCollapse";
import * as Masonry from 'masonry-layout';

global.isekai = {};

isekai.toc = new IsekaiToc('#nav-toc');
isekai.collapse = new IsekaiCollapse();

//为了pjax(还未完成)正常运行，所有外部库必须在这里加载
global.Masonry = Masonry;