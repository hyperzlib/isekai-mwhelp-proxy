<?php
namespace app\model;

use think\facade\Config;
use think\facade\Cache;
use utils\LocalPage;
use utils\MediaWikiPage;

class HelpPage {
    public static function getPage(string $pageName){
        if(LocalPage::exists($pageName)){ //使用本地的帮助文档
            return self::getLocalPage($pageName);
        } else { //使用mw官网的帮助文档
            return self::getRemotePage($pageName);
        }
    }

    public static function getLocalPage(string $pageName){
        //先查询缓存
        $localPage = new LocalPage($pageName);
        $cache = Cache::get('page:' . $pageName, false);
        if($cache && $cache['time'] >= $localPage->mtime){ //命中缓存，未修改
            return $cache;
        }
        //开始解析html
        $localPage->initFile();
        $html = $localPage->getHtml();
        $title = $localPage->getTitle();
        $toc = $localPage->getTOC();
        $data = [
			'time' => time(),
			'title' => $title,
			'content' => $html,
            'toc' => $toc,
		];
        //写入缓存
        Cache::set('page:' . $pageName, $data, 0);

        //返回页面信息
        return $data;
    }

    public static function getRemotePage(string $pageName){
        $cache = Cache::get('page:' . $pageName, false);
        if($cache){
            if($cache['time'] + Config::get('mediawiki.expire', 0) > time()){ //缓存有效
                return $cache;
            }
        }
		$cache = null;
        $mwPage = new MediaWikiPage($pageName);
        if($cache && $cache['time'] >= $mwPage->mtime){
            //服务器上的页面没有更改
            //刷新缓存时间
            $cache['time'] = time();
            Cache::set($pageName, $cache, 'page');
            return $cache['content'];
        }
        $mwPage->loadPage();
        $html = $mwPage->getHtml();
        $title = $mwPage->getTitle();
        $toc = $mwPage->getTOC();
        $data = [
        	'time' => time(),
			'title' => $title,
			'content' => $html,
            'toc' => $toc,
		];
        //写入缓存
        Cache::set('page:' . $pageName, $data, 0);

        //返回html
        return $data;
    }
}