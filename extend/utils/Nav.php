<?php
namespace utils;

use think\facade\Config;
use think\facade\Route;

class Nav {
    public $navConf = [];
    public $navTree = [];
    public $current = false;
    public $currentId = false;
    public $prev = false;
    public $next = false;

    public function __construct() {
        $this->navConf = Config::get('nav');
        $this->makeNavTree();
    }

    /**
     * 生成导航信息树
     */
    private function makeNavTree(){
        $this->navTree = [];
        $this->makePartNavTree($this->navConf);
    }

    /**
     * 递归用，生成单层导航信息树
     * @param array $items 导航信息
     * @param string $prefix 前缀
     */
    private function makePartNavTree(&$items, $prefix = ''){
        foreach ($items as $key => &$item){
            if(isset($item['page']) && $item['page']){ //是页面项目
                $this->navTree[] = [
                    'page' => $item['page'],
                    'title' => $item['title'],
                    'path' => $prefix . $key,
                    'url' => self::makePageLink($item['page']),
                ];
            } elseif(isset($item['items'])){ //是collapse container
                $this->makePartNavTree($item['items'], $prefix . $key . '/');
            }
        }
    }

    /**
     * 设置当前页面
     * @param string $pageName 页面名
     * @return bool 页面是否存在
     */
    public function setCurrent($pageName){
        $currentPageId = $this->getPageId($pageName);
        if($currentPageId === false){ //页面不存在（一定要用===）
            return false;
        }

        $this->currentId = $currentPageId;
        $this->current = $this->navTree[$currentPageId];
        //前后页面
        if(isset($this->navTree[$currentPageId - 1])){
            $this->prev = $this->navTree[$currentPageId - 1];
        }
        if(isset($this->navTree[$currentPageId + 1])){
            $this->next = $this->navTree[$currentPageId + 1];
        }
        return true;
    }

    public function hasPrev(){
        return $this->prev !== false;
    }

    public function hasCurrent(){
        return $this->current !== false;
    }

    public function hasNext(){
        return $this->next !== false;
    }

    /**
     * 根据页面名查找页面
     * @param string $pageName 页面名
     * @return bool|int 页面id
     */
    public function getPageId($pageName){
        foreach($this->navTree as $key => $item){
            if(strtolower($item['page']) == strtolower($pageName)){
                return $key;
            }
        }
        return false;
    }

    /**
     * 根据页面名获取页面信息
     * @param string $pageName 页面名
     * @return array|bool 页面，未找到返回false
     */
    public function getPageData($pageName){
        $pageId = $this->getPageId($pageName);
        if($pageId){
            return $this->navTree[$pageId];
        } else {
            return false;
        }
    }

    /**
     * 获取导航的html
     * @return string 生成的html
     */
    public function getHtml(){
        return $this->makePartHtml($this->navConf);
    }

    /**
     * 创建一个项目的html
     * @return string 生成的html
     */
    private function makePartHtml($items, $prefix = ''){
        $html = [];
        foreach($items as $key => $item) {
            if (isset($item['page'])) { //页面项目
                $isCurrent = ($this->current && $item['page'] == $this->current['page']);
                $html[] = '<a href="' . self::makePageLink($item['page'])
                    . '" class="mdui-list-item mdui-ripple'
                    . ($isCurrent ? ' mdui-list-item-active' : '') . '" id="nav-item-'
                    . str_replace('/', '-', $prefix . $key) . '">';
                if (isset($item['icon'])){ //有图标
                    $html[] = '<i class="mdui-list-item-icon mdui-icon material-icons'
                        . (isset($item['iconColor']) ? ' mdui-text-color-' . $item['iconColor'] : '')
                        . '">' . $item['icon'] . '</i>';
                    $html[] = '<div class="mdui-list-item-content">' . htmlspecialchars($item['title']) . '</div>';
                } else { //纯文本
                    $html[] = htmlspecialchars($item['title']);
                }
                $html[] = '</a>';
            } elseif (isset($item['items'])) { //collapse项目
                $isOpened = ($this->current && strpos($this->current['path'], $prefix . $key . '/') === 0); //判断当前项目是否是子项目
                //header
                $html[] = '<div class="mdui-collapse-item' . ($isOpened ? ' mdui-collapse-item-open' : '') . '" id="nav-item-'
                    . str_replace('/', '-', $prefix . $key) . '"><div class="mdui-collapse-item-header mdui-list-item mdui-ripple">';
                if (isset($item['icon'])) { //有图标
                    $html[] = '<i class="mdui-list-item-icon mdui-icon material-icons'
                    . (isset($item['iconColor']) ? ' mdui-text-color-' . $item['iconColor'] : '')
                    . '">' . $item['icon'] . '</i>';
                }
                $html[] = '<div class="mdui-list-item-content">' . htmlspecialchars($item['title']) . '</div>';
                $html[] = '<i class="mdui-collapse-item-arrow mdui-icon material-icons">keyboard_arrow_down</i></div>';
                //body
                $html[] = '<div class="mdui-collapse-item-body mdui-list">';
                $html[] = $this->makePartHtml($item['items'], $prefix . $key . '/'); //生成子项目
                $html[] = '</div>';
                $html[] = '</div>';
            }
        }
        return implode('', $html);
    }

    public function getItemsByParent($parent){
        $basePath = trim($parent, '/') . '/';
        $results = [];
        foreach($this->navTree as $item){
            if(strpos($item['path'], $basePath) === 0){
                $results[] = $item;
            }
        }
        return $results;
    }

    public static function makePageLink($pageName){
        return Route::buildUrl('help/') . $pageName;
    }
}