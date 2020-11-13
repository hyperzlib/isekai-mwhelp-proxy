<?php
namespace utils;

class Toc {
    public $tocData;

    private $html;

    public function __construct($tocData = []){
        $this->tocData = $tocData;

        $this->generateHtml();
    }

    private function generateHtml(){
        $this->html = $this->getPartHtml($this->tocData, true);
    }

    private function getPartHtml($tocList, $isRoot = false){
        $html = '';
        if(!$isRoot) $html .= '';
        foreach($tocList as $item){
            if(isset($item['items'])){ //有子项
                $html .= '<div class="mdui-collapse-item mdui-collapse-item-open">' . "\n" .
                         '    <li class="mdui-collapse-item-header mdui-list-item isekai-toc-item" data-target="' . $item['id'] . '">' . "\n" .
                         '        <a href="#' . $item['id'] . '" class="mdui-list-item-content mdui-ripple">' . "\n" .
                         '            ' . $item['name'] . "\n" .
                         '        </a>' . "\n" .
                         '        <button class="mdui-btn mdui-btn-icon mdui-ripple isekai-collapse-btn isekai-btn-rect">' . "\n" .
                         '            <i class="mdui-icon material-icons">keyboard_arrow_down</i>' . "\n" .
                         '        </button>' . "\n" .
                         '    </li>' . "\n" .
                         '    <div class="mdui-collapse-item-body isekai-collapse-item-body mdui-list">' . "\n";
                $html .= $this->getPartHtml($item['items']);
                $html .= '    </div>' . "\n" .
                         '</div>';
            } else {
                $html .= '<a href="#' . $item['id'] . '" class="mdui-list-item mdui-ripple" data-target="' . $item['id'] . '">' . $item['name'] . '</a>';
            }
        }
        return $html;
    }

    public function getHtml(){
        return $this->html;
    }

    public function isEmpty(){
        return empty($this->tocData);
    }
}