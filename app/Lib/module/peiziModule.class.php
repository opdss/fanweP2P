<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/page.php';

class peiziModule extends SiteBaseModule
{
	
	
	public function everwin()
	{
		$GLOBALS['tmpl']->caching = true;
		$cache_id  = md5(MODULE_NAME.ACTION_NAME);
		if (!$GLOBALS['tmpl']->is_cached('peizi/weekwin.html', $cache_id))
		{
			$peizi_conf = load_auto_cache("peizi_conf",array('type'=>0));
				
				
			
			$GLOBALS['tmpl']->assign("peizi_conf_json", json_encode($peizi_conf));
			$GLOBALS['tmpl']->assign("peizi_conf",$peizi_conf);
				
		}
		
		//开始交易时间，是否显示：今天(节假日，周末及下午1:30后，也不显示）
		$is_show_today = 1;		
		$GLOBALS['tmpl']->assign("is_show_today",$is_show_today);
		
		$GLOBALS['tmpl']->display("peizi/everwin.html",$cache_id);
	}
	
	public function weekwin()
	{
	
		$GLOBALS['tmpl']->caching = true;
		$cache_id  = md5(MODULE_NAME.ACTION_NAME);
		if (!$GLOBALS['tmpl']->is_cached('peizi/weekwin.html', $cache_id))
		{
			$peizi_conf = load_auto_cache("peizi_conf",array('type'=>1));
			
			
			$GLOBALS['tmpl']->assign("peizi_conf",$peizi_conf);
			
			$GLOBALS['tmpl']->assign("peizi_conf_json", json_encode($peizi_conf));
			
		}
		
		//开始交易时间，是否显示：今天(节假日，周末及下午1:30后，也不显示）
		$is_show_today = 1;
		$GLOBALS['tmpl']->assign("is_show_today",$is_show_today);
				
		$GLOBALS['tmpl']->display("peizi/weekwin.html",$cache_id);
	}
	
	public function scheme()
	{
		$GLOBALS['tmpl']->caching = true;
		$cache_id  = md5(MODULE_NAME.ACTION_NAME);
		if (!$GLOBALS['tmpl']->is_cached('peizi/weekwin.html', $cache_id))
		{
			$peizi_conf = load_auto_cache("peizi_conf",array('type'=>2));
				
			$GLOBALS['tmpl']->assign("peizi_conf",$peizi_conf);
				
			$GLOBALS['tmpl']->assign("peizi_conf_json", json_encode($peizi_conf));
				
		}
		
		//print_r($peizi_conf);exit;
		//开始交易时间，是否显示：今天(节假日，周末及下午1:30后，也不显示）
		$is_show_today = 1;
		$GLOBALS['tmpl']->assign("is_show_today",$is_show_today);
		
		$GLOBALS['tmpl']->display("peizi/scheme.html",$cache_id);
	}
	
	
	public function futures()
	{
		$GLOBALS['tmpl']->caching = true;
		$cache_id  = md5(MODULE_NAME.ACTION_NAME);
		if (!$GLOBALS['tmpl']->is_cached('peizi/weekwin.html', $cache_id))
		{
			$peizi_conf = load_auto_cache("peizi_conf",array('type'=>3));
				
			$GLOBALS['tmpl']->assign("is_show_today",1);
								
			$GLOBALS['tmpl']->assign("peizi_conf_json", json_encode($peizi_conf));
				
				
			$GLOBALS['tmpl']->assign("peizi_conf",$peizi_conf);
				
		}
		$GLOBALS['tmpl']->display("peizi/futures.html",$cache_id);
	}
	
}
?>