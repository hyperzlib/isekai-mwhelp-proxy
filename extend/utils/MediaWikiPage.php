<?php
namespace utils;

use think\exception\HttpException;
use think\facade\Config;

class MediaWikiPage {
    private $config;
    private $pageName;
    private $ch;

    public $mtime;
    public $title;
    public $originHtml;
    public $html;
    public $dom;
    public $toc;

    public function __construct($pageName){
        $this->config = Config::get('mediawiki');
        $this->pageName = $pageName;
        $this->mtime = $this->getMTime();
    }

    public function loadPage(){
        $ch = curl_init();
        $opt = $this->buildCurlopt();
        curl_setopt_array($ch, $opt);
        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        if($httpCode == 404){ //特别：不存在的页面处理
        	throw new HttpException(404, '页面不存在');
		}
        if(empty($data)){
        	if($curlError) {
				$this->html = 'Error: ' . $curlError;
				return false;
			} else {
        		$this->html = 'Http Error: ' . $httpCode;
        		return false;
			}
		}
        $this->originHtml = $data;
        $parser = new MediaWikiPageParser($this->originHtml);
        $parser->transform();
        $this->html = $parser->getHtml();
        $this->title = $parser->getTitle();
        $this->toc = $parser->getTOC();
    }

    public function getUrl(){
        return str_replace('{pagename}', $this->pageName, $this->config['url']);
    }

    public function buildCurlopt(){
        $opt = [
            CURLOPT_URL => $this->getUrl(),
            CURLOPT_CAINFO => config_path() . 'cacert.pem',
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.111 Safari/537.36 Edg/86.0.622.51',
			CURLOPT_ACCEPT_ENCODING => 'gzip, deflate, br',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 30,
        ];
        if($this->config['proxy']){
            $opt += self::getProxyConfig($this->config['proxy']);
        }
        return $opt;
    }

    public function getMtime(){
    	$ch = curl_init();
    	$opt = $this->buildCurlopt();
    	$opt[CURLOPT_CUSTOMREQUEST] = 'HEAD';
    	$opt[CURLOPT_HEADER] = true;
    	$opt[CURLOPT_NOBODY] = true;
    	curl_setopt_array($ch, $opt);
    	$headers = curl_exec($ch);
    	if($headers){
    		$headers = explode("\r\n", $headers);
    		foreach($headers as $header){
    			$lowerHeader = strtolower($header);
    			if(strpos($lowerHeader, 'last-modified:') === 0){
					$timeStr = trim(substr($header, 14));
					return strtotime($timeStr);
				}
			}
    		return false;
		} else {
    		$message = curl_error($ch);
    		$code = curl_errno($ch);
    		curl_close($ch);
    		throw new \Exception($message, $code);
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

    public static function getProxyConfig($proxyUrl){
        $ret = [];
        $proxyData = parse_url($proxyUrl);
        // 协议
        if(isset($proxyData['scheme'])){
            switch($proxyData['scheme']){
                case 'http':
                    $ret[CURLOPT_PROXYTYPE] = CURLPROXY_HTTP;
                break;
                case 'https':
                    $ret[CURLOPT_PROXYTYPE] = CURLPROXY_HTTPS;
                break;
                case 'socks': case 'socks5':
                    $ret[CURLOPT_PROXYTYPE] = CURLPROXY_SOCKS5;
                break;
                case 'socks4':
                    $ret[CURLOPT_PROXYTYPE] = CURLPROXY_SOCKS4;
                break;
            }
        } else {
            $ret[CURLOPT_PROXYTYPE] = CURLPROXY_HTTP;
        }
        // 地址
        if(isset($proxyData['host'])){
            $ret[CURLOPT_PROXY] = $proxyData['host'];
        }
        if(isset($proxyData['port'])){
            $ret[CURLOPT_PROXYPORT] = $proxyData['port'];
        }
        if(isset($proxyData['user']) && isset($proxyData['pass'])){
            $ret[CURLOPT_PROXYAUTH] = CURLAUTH_BASIC;
            $ret[CURLOPT_PROXYUSERNAME] = $proxyData['user'];
            $ret[CURLOPT_PROXYPASSWORD] = $proxyData['pass'];
        }
        return $ret;
    }
}