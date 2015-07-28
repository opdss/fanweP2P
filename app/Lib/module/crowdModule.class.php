<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/crowd_func.php';
class crowdModule extends SiteBaseModule
{
	
	public function show(){
		
		 
		$GLOBALS['tmpl']->display("page/crowd.html");
	}
 	
}
?>
