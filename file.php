<?php 
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
error_reporting(0);
if((trim($_REQUEST['m'])=='File'&&trim($_REQUEST['a'])=='do_upload_img')||(trim($_REQUEST['m'])=='File'&&trim($_REQUEST['a'])=='do_upload'))
{
	define("ADMIN_ROOT",1);
	require "admin.php";
}
?>
