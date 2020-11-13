<?php
namespace app\controller;

use app\BaseController;
use app\model\HelpPage;
use think\facade\Response;
use utils\MediaWikiPageParser;
use utils\Nav;

class Api extends BaseController {
    public function emptyHtml(){
        return \utils\LocalPage::getPath('test/var');
        return 'isekai help api backend';
	}
	
	public function nav(){
		$nav = new Nav();
		return json([
			'status' => 1,
			'navData' => $nav->navData,
			'navTree' => $nav->navTree,
		]);
	}

    public function html(string $page){
    	try {
			$pageData = HelpPage::getPage( $page );

			return json([
				'status' => 1,
				'title' => $pageData['title'],
				'content' => $pageData['content'],
                'toc' => $pageData['toc'],
			]);
		} catch(\Exception $e){
			return json([
				'status' => 0,
				'error' => $e->getCode(),
				'message' => $e->getMessage(),
				'stack' => $e->getTraceAsString(),
			]);
		}
    }
}