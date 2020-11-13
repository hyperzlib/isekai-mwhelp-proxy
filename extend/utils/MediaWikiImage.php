<?php
namespace utils;

use League\Flysystem\FileNotFoundException;
use think\Exception;
use think\exception\HttpException;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Request;

class MediaWikiImage {
	/** @var string $fileName */
	public $fileName;
	/** @var string $group */
	public $group;
	/** @var string $remoteUrl */
	public $remoteUrl;
	/** @var string $localPath */
	public $localPath;
	/** @var int $lastModified */
	public $lastModified;

	/** @var array[] $config */
	private $config;

	public function __construct($fileName, $group = 'mediawiki') {
		$this->config = Config::get('mediawiki');
		$this->fileName = $fileName;
		$this->group = $group;
		$this->remoteUrl = self::getImageRemoteUrl($fileName, $group);
		$this->localPath = self::getImagePath($fileName, $group);
	}

	public function prepare(){ //准备文件
		if(file_exists($this->localPath)){
			//检查有效期
			$expire = $this->config['expire'];
			$cacheData = Cache::get('image:' . $this->fileName, false);
			if($cacheData) {
				if ( is_int( $cacheData['time'] ) && $cacheData['time'] + $expire > time() ) {
					//不需要执行任何操作
					$this->lastModified = $cacheData['time'];

					return true;
				}
				//检测远端缓存
				$lastModified = $this->getLastModified();
				if ( $cacheData['time'] >= $lastModified ) {
					//文件未更改
					$this->lastModified = $cacheData['time'];
					//更新缓存
					$cacheData['time'] = time();
					Cache::set('image:' . $this->fileName, $cacheData, 0);
					return true;
				}
			}
		}
		//下载或更新文件
		$this->download();
		return true;
	}

	public function buildCurlopt(){
		$opt = [
			CURLOPT_URL => $this->remoteUrl,
			CURLOPT_CAINFO => config_path() . 'cacert.pem',
			CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.111 Safari/537.36 Edg/86.0.622.51',
			CURLOPT_ACCEPT_ENCODING => 'gzip, deflate, br',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 60,
		];
		if($this->config['proxy']){
			$opt += self::getProxyConfig($this->config['proxy']);
		}
		return $opt;
	}

	public function getLastModified(){
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

	public function download(){
		$this->prepareDir($this->localPath);
		$ch = curl_init();
		$fp = fopen($this->localPath, 'wb');
		$opt = $this->buildCurlopt();
		$opt[CURLOPT_RETURNTRANSFER] = false;
		$opt[CURLOPT_FILE] = $fp;
		curl_setopt_array($ch, $opt);
		curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$curlError = curl_error($ch);
		if(!empty($curlError) || $httpCode !== 200){
			if($curlError) {
				$curlErrno = curl_errno($ch);
				curl_close($ch);
				fclose($fp);
				unlink($this->localPath);
				throw new \Exception($curlError, $curlErrno);
			} else {
				curl_close($ch);
				fclose($fp);
				unlink($this->localPath);
				throw new HttpException($httpCode, 'Request: "' . $this->remoteUrl . '" http error');
			}
		}
		fclose($fp);
		$this->lastModified = time();
		Cache::set('image:' . $this->fileName, [
			'time' => $this->lastModified,
		], 0);
		return true;
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

	public function outputImage(){
		if(!file_exists($this->localPath)){
			throw new FileNotFoundException($this->localPath);
		}
		//生成header
		if(extension_loaded('fileinfo')){
			$fi = new \finfo(FILEINFO_MIME_TYPE);
			$mime = $fi->file($this->localPath);
		} else {
			$fileType = self::getFileType($this->fileName);
			if($fileType){
				$mime = 'image/' . $fileType;
			} else {
				$mime = 'application/octet-stream';
			}
		}
		$gmtLastModified = gmdate('D, d M Y H:i:s T', $this->lastModified);
		$eTag = md5($this->fileName . $this->lastModified);
		//处理缓存，降低服务器压力
		$response304 = self::processHttp304($eTag, $this->lastModified, $mime);
		if($response304) return $response304;

		$image = file_get_contents($this->localPath);

		return response($image)
			->lastModified($gmtLastModified)
			->eTag($eTag)
			->cacheControl('max-age=' . $this->config['expire'])
			->contentType($mime);
	}

	public function outputThumb($size = 200){
		if(!file_exists($this->localPath)){
			throw new FileNotFoundException($this->localPath);
		}
		//生成header
		if(extension_loaded('fileinfo')){
			$fi = new \finfo(FILEINFO_MIME_TYPE);
			$mime = $fi->file($this->localPath);
		} else {
			$fileType = self::getFileType($this->fileName);
			if($fileType){
				$mime = 'image/' . $fileType;
			} else {
				$mime = 'application/octet-stream';
			}
		}
		$gmtLastModified = gmdate('D, d M Y H:i:s T', $this->lastModified);
		$eTag = md5($this->fileName . $size . $this->lastModified);
		//处理缓存，降低服务器压力
		$response304 = self::processHttp304($eTag, $this->lastModified, $mime);
		if($response304) return $response304;

		$cacheKey = 'thumb:cache_hit:' . $size . 'px@' . $this->fileName;
		$cacheStatus = Cache::get($cacheKey, [
			'hit' => 0,
			'cached' => false,
		]);
		$cachePath = self::getImagePath($this->fileName, $this->group, $size);

		if($cacheStatus['cached'] && file_exists($cachePath)){ //存在缓存图片
			$image = file_get_contents($cachePath);
		} elseif($cacheStatus['hit'] >= 10){ //生成缓存图片
			$image = self::generateThumb($this->localPath, $size, $cachePath);
			$cacheStatus['cached'] = true;
		} else { //直接输出
			$image = self::generateThumb($this->localPath, $size);
		}
		$cacheStatus['hit'] ++;
		Cache::set($cacheKey, $cacheStatus, 0);

		if(is_string($image)){
			return response($image)
				->lastModified($gmtLastModified)
				->eTag($eTag)
				->header([ 'X-In-Cache' => $cacheStatus['cached'] ? 'yes' : 'no' ])
				->cacheControl('max-age=' . $this->config['expire'])
				->contentType($mime);
		} else {
			return response('Cannot generate thumb: file type not allowed.');
		}
	}

	/**
	 * 生成缩略图
	 * @param string $file 源文件
	 * @param int $size 大小
	 * @param bool|string $cache 缓存文件
	 * @return bool
	 */
	public static function generateThumb(string $file, int $size, $cache = false){
		$fileType = self::getFileType($file);
		if(!$fileType){
			return false;
		}
		$imgData = file_get_contents($file);
		list($srcWidth, $srcHeight, $srcType) = getimagesizefromstring($imgData);
		/*if($srcWidth > $srcHeight){
			$dstWidth = min($size, $srcWidth);
			$dstHeight = $srcHeight / $srcWidth * $dstWidth;
		} else {
			$dstHeight = min($size, $srcHeight);
			$dstWidth = $srcWidth / $srcHeight * $dstHeight;
		}*/
		$dstWidth = min($size, $srcWidth);
		$dstHeight = $srcHeight / $srcWidth * $dstWidth;

		$canvas = imagecreatetruecolor($dstWidth, $dstHeight);
		imagealphablending($canvas, false);
		imagesavealpha($canvas, true);
		$srcCanvas = imagecreatefromstring($imgData);
		imagecopyresampled($canvas, $srcCanvas, 0, 0, 0, 0,
			$dstWidth, $dstHeight, $srcWidth, $srcHeight);

		$renderFunction = 'image' . $fileType;

		ob_start();
		$renderFunction($canvas);
		$image = ob_get_clean();

		if($cache){
			self::prepareDir($cache);
			$renderFunction($canvas, $cache);
		}

		imagedestroy($srcCanvas);
		imagedestroy($canvas);
		return $image;
	}

	public static function processHttp304($eTag, $lastModified, $mime){
		$localETag = Request::header('if-none-match');
		$localLastModified = strtotime(Request::header('if-modified-since',
			'Thu, 1 Jan 1970 00:00:00 GMT'));
		if($localETag == $eTag && $lastModified <= $localLastModified){
			return response('')
				->code(304)
				->lastModified(gmdate('D, d M Y H:i:s T', $lastModified))
				->eTag($eTag)
				->contentType($mime);
		} else {
			return false;
		}
	}

	public static function prepareDir($path){
		$dir = dirname($path);
		if(!file_exists($dir) && !is_dir($dir)) {
			mkdir( $dir, 0755, true );
		}
	}

	public static function getImageRemoteUrl($fileName, $groupName = 'mediawiki'){
		$baseUrl = Config::get('mediawiki.imageUrl');
		$hash = md5($fileName);
		$path = $hash[0] . '/' . substr($hash, 0, 2) . '/' . $fileName;
		return rtrim($baseUrl, '/') . '/' . $groupName . '/' . $path;
	}

	public static function getImagePath($fileName, $groupName = 'mediawiki', $thumbSize = false){
		$path = root_path() . 'images/';
		if($thumbSize) $path .= 'thumb/';
		$path .= $groupName . '/';
		if($thumbSize){
			$path .= strval($thumbSize) .'px-' . $fileName;
		} else {
			$path .= $fileName;
		}
		return $path;
	}

	public static function getFileType($fileName){
	    $ext = substr(strrchr($fileName,'.'), 1);
        $fileType = [
            'png' => 'png',
            'jpg' => 'jpeg',
            'jpeg' => 'jpeg',
            'gif' => 'gif',
            'webp' => 'webp',
            'svg' => 'svg',
        ];
        if(isset($fileType[$ext])){
            return $fileType[$ext];
        } else {
            return false;
        }
    }
}