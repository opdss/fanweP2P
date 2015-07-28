<?php 
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class SiteBaseModule{
	public function __construct()
	{
		if($GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']=="ES_FILE")
		{
			logger::write($GLOBALS['distribution_cfg']['OSS_DOMAIN']."/es_file.php");
			global $syn_image_ci;
			global $curl_param;
			//global $syn_image_idx;
			$syn_image_idx = 0;
			$syn_image_ci  =  curl_init($GLOBALS['distribution_cfg']['OSS_DOMAIN']."/es_file.php");
			curl_setopt($syn_image_ci, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($syn_image_ci, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($syn_image_ci, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($syn_image_ci, CURLOPT_NOPROGRESS, true);
			curl_setopt($syn_image_ci, CURLOPT_HEADER, false);
			curl_setopt($syn_image_ci, CURLOPT_POST, TRUE);
			curl_setopt($syn_image_ci, CURLOPT_TIMEOUT, 1);
			curl_setopt($syn_image_ci, CURLOPT_TIMECONDITION, 1);
			$curl_param['username'] = $GLOBALS['distribution_cfg']['OSS_ACCESS_ID'];
			$curl_param['password'] = $GLOBALS['distribution_cfg']['OSS_ACCESS_KEY'];
			$curl_param['act'] = 2;
		}
		
		$GLOBALS['tmpl']->assign("MODULE_NAME",MODULE_NAME);
		$GLOBALS['tmpl']->assign("ACTION_NAME",ACTION_NAME);
		
		$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/page_static_cache/");
		$GLOBALS['dynamic_cache'] = $GLOBALS['cache']->get("APP_DYNAMIC_CACHE_".APP_INDEX."_".MODULE_NAME."_".ACTION_NAME);
		$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/avatar_cache/");
		$GLOBALS['dynamic_avatar_cache'] = $GLOBALS['cache']->get("AVATAR_DYNAMIC_CACHE"); //头像的动态缓存
		
		//输出导航菜单
		$nav_list = get_nav_list();
		$nav_list= init_nav_list($nav_list);
		foreach($nav_list as $k=>$v){
			$nav_list[$k]['sub_nav'] = init_nav_list($v['sub_nav']);
		}
		$GLOBALS['tmpl']->assign("nav_list",$nav_list);
		
		
		
		//输出在线客服与时间
		if(app_conf("ONLINE_QQ")!=""){
			$qq = unserialize(app_conf("ONLINE_QQ"));
			$GLOBALS['tmpl']->assign("online_qq",$qq);
		}
		
		//输出页面的标题关键词与描述
		$GLOBALS['tmpl']->assign("site_info",get_site_info());
		
		//输出系统文章
		$system_article = get_article_list(8,0,"ac.type_id = 3","",true);
		$GLOBALS['tmpl']->assign("system_article",$system_article['list']);
		
		//输出帮助
		$deal_help = get_help();
		$GLOBALS['tmpl']->assign("deal_help",$deal_help);
		
		
		if(MODULE_NAME=="acate"&&ACTION_NAME=="index"||
		MODULE_NAME=="article"&&ACTION_NAME=="index"||
		MODULE_NAME=="cate"&&ACTION_NAME=="index"||
		MODULE_NAME=="comment"&&ACTION_NAME=="index"||
		MODULE_NAME=="help"&&ACTION_NAME=="index"||
		MODULE_NAME=="link"&&ACTION_NAME=="index"||
		MODULE_NAME=="mobile"&&ACTION_NAME=="index"||
		MODULE_NAME=="msg"&&ACTION_NAME=="index"||
		MODULE_NAME=="notice"&&ACTION_NAME=="index"||
		MODULE_NAME=="notice"&&ACTION_NAME=="list_notice"||
		MODULE_NAME=="rec"&&ACTION_NAME=="rhot"||
		MODULE_NAME=="rec"&&ACTION_NAME=="rnew"||
		MODULE_NAME=="rec"&&ACTION_NAME=="rbest"||
		MODULE_NAME=="rec"&&ACTION_NAME=="rsale"||
		MODULE_NAME=="score"&&ACTION_NAME=="index"||
		MODULE_NAME=="space"&&ACTION_NAME=="index"||
		MODULE_NAME=="space"&&ACTION_NAME=="fav"||
		MODULE_NAME=="space"&&ACTION_NAME=="fans"||
		MODULE_NAME=="space"&&ACTION_NAME=="focus"||
		MODULE_NAME=="msg"&&ACTION_NAME=="index"||
		MODULE_NAME=="ss"&&ACTION_NAME=="index"||
		MODULE_NAME=="ss"&&ACTION_NAME=="pick"||
		MODULE_NAME=="sys"&&ACTION_NAME=="index"||
		MODULE_NAME=="sys"&&ACTION_NAME=="list_notice"||
		MODULE_NAME=="vote"&&ACTION_NAME=="index")
		{
			set_gopreview();
		}

		
	}

	public function index()
	{
		 app_redirect("404.html");
		 exit();
		//showErr("invalid access");
	}
	public function __destruct()
	{
		if(isset($GLOBALS['cache']))
		{
			$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/page_static_cache/");
			$GLOBALS['cache']->set("APP_DYNAMIC_CACHE_".APP_INDEX."_".MODULE_NAME."_".ACTION_NAME,$GLOBALS['dynamic_cache']);
			if(count($GLOBALS['dynamic_avatar_cache'])<=500)
			{
				$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/avatar_cache/");
				$GLOBALS['cache']->set("AVATAR_DYNAMIC_CACHE",$GLOBALS['dynamic_avatar_cache']); //头像的动态缓存
			}
		}
		
		if($GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']=="ES_FILE")
		{
			if(count($GLOBALS['curl_param']['images'])>0)
			{
				$GLOBALS['curl_param']['images'] =  base64_encode(serialize($GLOBALS['curl_param']['images']));
				curl_setopt($GLOBALS['syn_image_ci'], CURLOPT_POSTFIELDS, $GLOBALS['curl_param']);
				$rss = curl_exec($GLOBALS['syn_image_ci']);
			}
			curl_close($GLOBALS['syn_image_ci']);
		}
		unset($this);
	}
}
?>