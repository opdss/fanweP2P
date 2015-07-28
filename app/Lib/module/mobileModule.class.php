<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class mobileModule extends SiteBaseModule
{
	public function index()
	{
		if($this->isios()){
			app_redirect(app_conf("APPLE_DOWLOAD_URL"));
		}else{
		  app_redirect(app_conf("ANDROID_DOWLOAD_URL"));
		}
	}
	
	public function isios() {
		//判断手机发送的客户端标志,兼容性有待提高
		if (isset ($_SERVER['HTTP_USER_AGENT'])) {
			$clientkeywords = array (
					'iphone',
					'ipod',
					'mac',
			);
			// 从HTTP_USER_AGENT中查找手机浏览器的关键字
			if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
				return true;
			}
		}
	}
	
	
}
?>