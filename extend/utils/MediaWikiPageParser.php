<?php
namespace utils;

use DOMDocument;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Options;
use think\facade\Config;
use think\facade\Route;

class MediaWikiPageParser {
    private $originHtml;
	/** @var Dom $originDom */
	private $originDom;
	/** @var string|Dom $mainSection */
	private $mainSection;
	private $mainSectionHtml;
	private $mwBaseUrl;
	/** @var string $dom */
	public $html = '';
    public $title;
    public $toc = [];

	public function __construct($html, $title = false) {
		$this->originHtml = $html;
		$this->title = $title;

		$this->mwBaseUrl = Config::get('mediawiki.baseUrl');

		$this->initDom();

		$this->toc = $this->generateTOC();
	}

	private function initDom(){
		$this->originDom = new Dom();
		$this->originDom->setOptions(
            (new Options())
                ->setPreserveLineBreaks(true)
                ->setRemoveStyles(false)
        );
		libxml_use_internal_errors(true);
		$this->originDom->loadStr($this->originHtml);
		libxml_clear_errors();
	}

	public function findTitle(){
		$elements = $this->originDom->find('#firstHeading');
		if($elements->count() > 0){
			/** @var Dom\Node\HtmlNode $titleElement */
			$titleElement = $elements[0];
			$title = $titleElement->innerText();
			$title = basename(str_replace(['Help:', '帮助:', '幫助:'], '', $title));
			return $title;
		} else {
			return 'Untitled';
		}
	}

    /**
     * @param Dom\Node\HtmlNode $dom
     */
	private function getChunkTOC($dom){
        $ret = [];
        $elements = $dom->getChildren();
        /** @var Dom\Node\HtmlNode $element */
        foreach($elements as $element){
            if($element->tag->name() !== 'li') continue;
            //获取链接
            $a = $element->find('a');
            if($a->count() == 0) continue;
            /** @var Dom\Node\HtmlNode $a */
            $a = $a[0];
            $targetId = ltrim($a->getAttribute('href'), '#');
            //获取文本
            $tocNumber = $a->find('.tocnumber');
            $tocText = $a->find('.toctext');
            if($tocNumber->count() == 0 || $tocText->count() == 0) continue;
            /** @var Dom\Node\HtmlNode $tocNumber */
            $tocNumber = $tocNumber[0];
            /** @var Dom\Node\HtmlNode $tocText */
            $tocText = $tocText[0];
            $tocName = $tocNumber->innerText . ' ' . $tocText->innerText;

            $subList = $element->find('ul');

            //准备数据
            $tocData = [
                'id' => $targetId,
                'name' => $tocName,
            ];
            if($subList->count() > 0){
                $tocData['items'] = $this->getChunkTOC($subList[0]);
            }
            $ret[] = $tocData;
        }
        return $ret;
    }

	public function generateTOC(){
	    $elements = $this->originDom->find('#toc ul');
	    if($elements->count() > 0){
	        return $this->getChunkTOC($elements[0]);
        } else {
	        return [];
        }
    }

	public function getMainSection(){
		$elements = $this->originDom->find(".mw-parser-output");
		if($elements->count() > 0){
			return $elements[0];
		} else {
			return '<div>Cannot find mw-parser-output</div>';
		}
	}

	public function makeTitleElement(){
		if(!$this->title){
			$this->title = $this->findTitle();
		}
		return '<h1 class="page-title mdui-text-color-theme">' . htmlspecialchars($this->title) . '</h1>';
	}

	public function removeUnnecessaryParts(){
		$elements = $this->originDom->find('#toc, .mw-pt-languages, .printfooter, .nmbox.noprint, noscript');
		/** @var Dom\Node\HtmlNode $element */
		foreach ($elements as $element){
			$element->parent->removeChild($element->id());
		}
		$elements = $this->originDom->find('table');
		if($elements->count() > 0){
			$element = $elements[0];
			$element->parent->removeChild($element->id());
		}
	}

	public function replaceLinks(){
		$elements = $this->originDom->find('a[href]');
		/** @var Dom\Node\HtmlNode $element */
		foreach ($elements as $element){
			$href = $element->getAttribute('href');
			if(preg_match('/^\/wiki\/Special:MyLanguage\/Help:(?<page>.*?)$/',
				$href,
				$matches)){
				$page = $matches['page'];
				$element->setAttribute('href', Route::buildUrl('/help/') . $page);
				$element->setAttribute('data-ajax', 'true');
			} elseif($href[0] === '/'){
				$element->setAttribute('href', $this->mwBaseUrl . $href);
				$element->setAttribute('target', '_blank');
				self::addClass($element, 'external');
			} elseif($href[0] !== '#') {
				$element->setAttribute('target', '_blank');
			}
		}
	}

	public static function getClasses($element){
        $classes = $element->getAttribute('class');
        if($classes) {
            $classes = explode(' ', $classes);
        } else {
            $classes = [];
        }
        return $classes;
    }

    public static function addClass($elemenet, $addClass = []){
	    if(is_string($addClass)) $addClass = [$addClass];
	    $classes = self::getClasses($elemenet);
	    $classes = array_merge($classes, $addClass);
	    $elemenet->setAttribute('class', implode(' ', $classes));
    }

	public static function getImageFromUrl($url){
		if(preg_match('#upload\.wikimedia\.org/wikipedia/(?<group>[\w\-_.]+)(/thumb)?/\w/\w{2}/(?<filename>[\w\-_.%]+)(/(?<size>[0-9]+)px)?#', $url, $matches)) {
			$ret = [];
			$ret['file'] = $matches['filename'];
			$ret['type'] = MediaWikiImage::getFileType($matches['filename']);
			$ret['group'] = $matches['group'];
			$ret['thumb'] = false;
			if ( isset( $matches['size'] ) && !empty( $matches['size'] ) ) {
				$ret['size'] = intval( $matches['size'] );
				$ret['thumb'] = true;
			}
			return $ret;
		} else {
			return false;
		}
	}

	public static function transformImageUrl($url){
		$imgData = self::getImageFromUrl($url);
		if($imgData && $imgData['type']/* 判断是否是支持代理的图片 */){
			if($imgData['thumb'] && !in_array($imgData['type'], ['svg'])){
				return Route::buildUrl('images/') . $imgData['group'] . '/' .
					strval($imgData['size']) . 'px/' . $imgData['file'];
			} else {
				return Route::buildUrl('images/') . $imgData['group'] . '/' . $imgData['file'];
			}
		} else {
			return $url;
		}
	}

	public function improveImage(){
		$elements = $this->originDom->find('img[src]');
		/** @var Dom\Node\HtmlNode $element */
		foreach ($elements as $element){
			$src = $element->getAttribute('src');
			$url = self::transformImageUrl($src);
			$element->setAttribute('src', $url);
			//处理srcset
			$srcset = $element->getAttribute('srcset');
			if($srcset){
				$srcset = explode(',', $srcset);
				$resultSet = [];
				foreach($srcset as $one){
					$t = explode(' ', trim($one));
					if(count($t) !== 2) continue;
					list($src, $size) = $t;
					$url = self::transformImageUrl($src);
					if($url){
						$resultSet[] = $url . ' ' . $size;
					} else {
						$resultSet[] = $src . ' ' . $size;
					}
				}
				$srcset = implode(', ', $resultSet);
				$element->setAttribute('srcset', $srcset);
			}
		}
	}

	public function improveTable(){
        $elements = $this->originDom->find('table');
        /** @var Dom\Node\HtmlNode $element */
        foreach ($elements as $element) {
            $classes = self::getClasses($element);
            if(!in_array('mw-version', $classes)) {
                self::addClass($element, 'mdui-table');
            }
        }
	}
	
	public function fixKbdStyle(){
		$elements = $this->originDom->find('kbd');
        /** @var Dom\Node\HtmlNode $element */
        foreach ($elements as $element) {
            $element->setAttribute('style', '');
        }
	}

    public function fixPre(){
	    preg_match_all('#<pre>(?<preContent>.*?)</pre>#is', $this->originHtml, $matches);
	    $preContents = $matches['preContent'];
	    $i = 0;
        $this->mainSectionHtml = preg_replace_callback('#<pre>(.*?)</pre>#is', function($matches)use(&$i, $preContents){
            return '<pre>' . $preContents[$i ++] . '</pre>';
        }, $this->mainSectionHtml);
        return true;
    }

    public static function replaceCssImageLinks($css){
        return preg_replace_callback('/url\((\'|")?(?<imgUrl>.*?)(\'|")?\)/', function($matches){
            return "url('" . self::transformImageUrl($matches["imgUrl"]) . "')";
        }, $css);
    }

    public function fixStyleBlock(){
        preg_match_all('#<style(.*?[^>])>(?<css>.*?)</style>#is', $this->originHtml, $matches);
        $styles = $matches['css'];
        $i = 0;
        $this->mainSectionHtml = preg_replace_callback('#<style(.*?)</style>#is', function($matches)use(&$i, $styles){
            if(isset($styles[$i])){
                $result = '<style>' . self::replaceCssImageLinks($styles[$i]) . '</style>';
            } else {
                $result = '';
            }
            $i ++;
            return $result;
        }, $this->mainSectionHtml);
        return true;
    }

	public function replaceStyles(){

	}

	public function transform(){
		$this->html .= $this->makeTitleElement();
		$this->mainSection = $this->getMainSection();
		if(is_string($this->mainSection)){
			$this->html .= $this->mainSection;
		} else {
			$this->removeUnnecessaryParts();
			$this->replaceLinks();
			$this->improveImage();
			$this->improveTable();
			$this->fixKbdStyle();

			$this->mainSectionHtml = $this->mainSection->innerhtml;
            //特殊修正
            //$this->fixPre();
            $this->fixStyleBlock();

            $this->html .= $this->mainSectionHtml;
		}
	}

	public function getHtml(){
		return $this->html;
	}

	public function getTitle(){
		return $this->title;
	}

	public function getTOC(){
	    return $this->toc;
    }
}