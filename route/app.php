<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;

Route::rule('help/<page>', 'index/helpPage')->pattern(['page' => '.*']);
Route::rule('api/html/<page>', 'api/html')->pattern(['page' => '.*']);
Route::rule('api/html', 'api/emptyHtml');
Route::rule('images/<group>/<size>px/<path>', 'images/thumb')->pattern([
	'size' => '[0-9]+','path' => '.*'
]);
Route::rule('images/<group>/<path>', 'images/get')->pattern(['path' => '.*']);