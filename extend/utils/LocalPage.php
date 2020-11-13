<?php
namespace utils;

use League\Flysystem\FileNotFoundException;

class LocalPage {
    private $pageName;
    private $filePath;
    private $fileType;
    public $title;
    public $toc = [];
    public $content;

    public $mtime;

    static function exists($pageName){
        $path = self::getPath($pageName);
        return file_exists($path . '.html') || file_exists($path . '.wikitext');
    }

    static function getPath(string $pageName){
        $pageName = basename($pageName);
        return root_path() . 'local' . DIRECTORY_SEPARATOR . $pageName;
    }

    public function __construct(string $pageName){
        $this->pageName = $pageName;
        $this->filePath = self::getPath($this->pageName);
        if(file_exists($this->filePath . '.html')){
            $this->fileType = 'html';
            $this->filePath .= '.html';
        } elseif(file_exists($this->filePath . '.wikitext')){
            $this->fileType = 'wikitext';
            $this->filePath .= '.wikitext';
        } else {
            throw new FileNotFoundException($this->filePath);
        }
        $this->mtime = filemtime($this->filePath);
    }

    public function initFile(){
        $this->content = file_get_contents($this->filePath);
        if($this->fileType === 'wikitext'){
            $this->parseWikiText();
        }
        $this->title = $this->findTitle();
    }

    public function getHtml(){
        return $this->content;
    }

    public function getTitle(){
    	return $this->title;
	}

	public function getTOC(){
        return $this->toc;
    }

    public function parseWikiText(){

    }

    public function findTitle(){
        preg_match('#<h1(.*?[^>])?>(?<title>.*?)</h1>#', $this->content, $matches);
    	if(isset($matches['title'])){
    	    return $matches['title'];
        } else {
    	    return '无标题';
        }
	}
}