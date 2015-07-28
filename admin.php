<?php 
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

if(!defined("ADMIN_ROOT"))
{
	die("Invalid access");
}

define("FILE_PATH",""); //文件目录，空为根目录
require './system/common.php';
define('BASE_PATH','./');
define('THINK_PATH', './admin/ThinkPHP');
//定义项目名称和路径
define('APP_NAME', 'admin');
define('APP_PATH', './admin');

// 加载框架入口文件 
require(THINK_PATH."/ThinkPHP.php");

//实例化一个网站应用实例
$AppWeb = new App(); 
//应用程序初始化
$AppWeb->run();

?>