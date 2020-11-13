<?php
namespace app\controller;

use app\BaseController;
use app\model\HelpPage;
use think\facade\View;
use utils\Nav;
use utils\Toc;

class Index extends BaseController
{
    public function index()
    {
		$toc = new Toc([]);
		$nav = new Nav();
		$nav->setCurrent('Index');
		return view('index', [
			'title' => '首页',
			'toc' => $toc,
			'tocHtml' => $toc->getHtml(),
			'nav' => $nav,
			'navHtml' => $nav->getHtml(),
		]);
    }

    public function helpPage(string $page){
		switch(strtolower($page)){
			case 'index': //特殊：首页
				return $this->index();
				break;
			default:
				$pageData = HelpPage::getPage($page);
				$toc = new Toc($pageData['toc']);
				$nav = new Nav();
				$nav->setCurrent($page);
				return view('helpPage', [
					'title' => $pageData['title'],
					'content' => $pageData['content'],
					'toc' => $toc,
					'tocHtml' => $toc->getHtml(),
					'nav' => $nav,
					'navHtml' => $nav->getHtml(),
					'time' => $pageData['time'],
				]);
				break;
		}
	}
}
