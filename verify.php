<?php 
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------\
define("FILE_PATH",""); //文件目录，空为根目录
require_once './system/common.php';
es_session::start();
require APP_ROOT_PATH."system/utils/es_image.php";
$verify = isset($_REQUEST['vname']) ? !empty($_REQUEST['vname']) ? strim($_REQUEST['vname']) : 'verify' : 'verify';
$w = isset($_REQUEST['w']) ? intval($_REQUEST['w']) : 50;
$h = isset($_REQUEST['h']) ? intval($_REQUEST['h']) : 22;
es_image::buildImageVerify(4,1,'gif',$w,$h,$verify);
?>