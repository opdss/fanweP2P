<?php
require_once 'common.php';
filter_injection($_REQUEST);

if(!file_exists(APP_ROOT_PATH.'public/runtime/app/'))
{
	mkdir(APP_ROOT_PATH.'public/runtime/app/',0777);
}

$GLOBALS['tmpl']->assign("site_info",get_site_info());
//开始输出友情链接

$GLOBALS['tmpl']->assign("f_link_data",load_auto_cache("links"));

//输出根路径
$GLOBALS['tmpl']->assign("APP_ROOT",APP_ROOT);

//输出语言包的js
if(!file_exists(get_real_path()."public/runtime/app/lang.js"))
{			
		$str = "var LANG = {";
		foreach($lang as $k=>$lang_row)
		{
			$str .= "\"".$k."\":\"".str_replace("nbr","\\n",addslashes($lang_row))."\",";
		}
		$str = substr($str,0,-1);
		$str .="};";
		@file_put_contents(get_real_path()."public/runtime/app/lang.js",$str);
}

//会员自动登录及输出
$cookie_uname = es_cookie::get("user_name")?es_cookie::get("user_name"):'';
$cookie_upwd = es_cookie::get("user_name")?es_cookie::get("user_pwd"):'';
if($cookie_uname!=''&&$cookie_upwd!=''&&!es_session::get("user_info"))
{
	require_once APP_ROOT_PATH."system/libs/user.php";
	auto_do_login_user($cookie_uname,$cookie_upwd);
}

if(strim($_REQUEST['ctl']) == "uc_invest" ||  strim($_REQUEST['ctl']) == "uc_deal"){
	$r_user_name=strim($_REQUEST['user_name']);
	$r_user_pwd=strim($_REQUEST['user_pwd']);
	
	if($r_user_name!=''&&$r_user_pwd!='')
	{
		require_once APP_ROOT_PATH."system/libs/user.php";
		auto_do_login_user($r_user_name,$r_user_pwd);
	}
}



$user_info = es_session::get('user_info');
if(intval($user_info['id']) > 0){
	$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where is_delete = 0 and is_effect = 1 and id = ".intval($user_info['id']));	
	if($user_info)
	{	
		es_session::set('user_info',$user_info);
		$GLOBALS['tmpl']->assign("user_info",$user_info);
		if(check_ipop_limit(CLIENT_IP,"auto_send_msg",30,$user_info['id']))  //自动检测收发件
		{
			//有会员登录状态时，自动创建消息
			$msg_systems = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."msg_system where (end_time = 0 or end_time > ".TIME_UTC.") and (user_ids = '' or user_ids like '%"."|".$user_info['id']."-".$user_info['user_name']."|"."%')");
			foreach($msg_systems as $msg)
			{
				if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."msg_box where to_user_id = ".$user_info['id']." and system_msg_id = ".$msg['id'])==0)
				{
					send_user_msg($msg['title'],$msg['content'],0,$user_info['id'],$msg['create_time'],$msg['id'],true);
				}		
			}
		}
	}
}
else{
	es_session::set('user_info',array());
}




//保存来路
if(!es_cookie::get("referer_url"))
{	
	if(!preg_match("/".urlencode(SITE_DOMAIN.APP_ROOT)."/",urlencode($_SERVER["HTTP_REFERER"])))
	es_cookie::set("referer_url",$_SERVER["HTTP_REFERER"]);
}
$referer = es_cookie::get("referer_url");

$IMG_APP_ROOT = APP_ROOT;
if(!file_exists(APP_ROOT_PATH.'public/runtime/app/tpl_caches/'))
	mkdir(APP_ROOT_PATH.'public/runtime/app/tpl_caches/',0777);
if(!file_exists(APP_ROOT_PATH.'public/runtime/app/tpl_compiled/'))
	mkdir(APP_ROOT_PATH.'public/runtime/app/tpl_compiled/',0777);
$GLOBALS['tmpl']->cache_dir      = APP_ROOT_PATH . 'public/runtime/app/tpl_caches';
$GLOBALS['tmpl']->compile_dir    = APP_ROOT_PATH . 'public/runtime/app/tpl_compiled';
$GLOBALS['tmpl']->template_dir   = APP_ROOT_PATH . 'app/Tpl/' . app_conf("TEMPLATE");
//定义当前语言包
$GLOBALS['tmpl']->assign("LANG",$lang);
//定义模板路径
$tmpl_path = SITE_DOMAIN.APP_ROOT."/app/Tpl/";
$GLOBALS['tmpl']->assign("TMPL",$tmpl_path.app_conf("TEMPLATE"));
$GLOBALS['tmpl']->assign("TMPL_REAL",APP_ROOT_PATH."app/Tpl/".app_conf("TEMPLATE")); 

$GLOBALS['tmpl']->assign("MOBILE_DOWN_PATH",SITE_DOMAIN.url("index","mobile")); 


if(app_conf("SHOP_OPEN")==0)
{
	$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['SHOP_CLOSE']);
	$GLOBALS['tmpl']->assign("html",app_conf("SHOP_CLOSE_HTML"));
	$GLOBALS['tmpl']->display("shop_close.html");
	exit;
}

$DEAL_MSG_COUNT = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_msg_list where is_send = 0 and (send_type = 0 or send_type = 1) ");
$GLOBALS['tmpl']->assign("DEAL_MSG_COUNT",$DEAL_MSG_COUNT); 
?>