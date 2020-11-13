<?php
// 应用公共文件
function getPagePathClass(\utils\Nav $nav){
    if($nav->hasCurrent()){
        return ' page-' . str_replace('/', '-', $nav->current['path']);
    } else {
        return '';
    }
}