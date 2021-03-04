<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
// [ test ]
// 指定允许其他域名访问
// header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT,DELETE');
// header('Access-Control-Allow-Origin: * ');
// header("Access-Control-Allow-Headers:token,Content-type");
// header('content-type:application/json;charset=utf8');
//header('Access-Control-Max-Age: 86400');

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');

// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';

