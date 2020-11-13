<?php
namespace app\controller;

use app\BaseController;
use utils\MediaWikiImage;

class Images extends BaseController {
	public function get(string $group, string $path){
		$image = new MediaWikiImage($path, $group);
		$image->prepare();
		return $image->outputImage();
	}

	public function thumb(string $group, int $size, string $path){
		$image = new MediaWikiImage($path, $group);
		$image->prepare();
		return $image->outputThumb($size);
	}
}