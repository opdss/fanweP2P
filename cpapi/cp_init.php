<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

@session_start();
@set_magic_quotes_runtime (0);
define('MAGIC_QUOTES_GPC',get_magic_quotes_gpc()?True:False);
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
if(!defined('APP_ROOT')) {
        // 网站URL根目录
        $_root = dirname(_PHP_FILE_);
        $_root = (($_root=='/' || $_root=='\\')?'':$_root);
        $_root = str_replace("/system","",$_root);
        define('APP_ROOT', $_root  );
}
if(!defined('APP_ROOT_PATH')) 
define('APP_ROOT_PATH', str_replace('cpapi/cp_init.php', '', str_replace('\\', '/', __FILE__)));




//引入数据库的系统配置及定义配置函数
$sys_config = require APP_ROOT_PATH.'system/config.php';
function app_conf($name)
{
	return stripslashes($GLOBALS['sys_config'][$name]);
}
//end 引入数据库的系统配置及定义配置函数

//引入时区配置及定义时间函数
if(function_exists('date_default_timezone_set'))
	date_default_timezone_set(app_conf('DEFAULT_TIMEZONE'));
//end 引入时区配置及定义时间函数

//定义缓存
require APP_ROOT_PATH.'system/cache/Cache.php';
$cache = CacheService::getInstance();
//end 定义缓存

//定义DB
require APP_ROOT_PATH.'system/db/db.php';
define('DB_PREFIX', app_conf('DB_PREFIX')); 
if(!file_exists(APP_ROOT_PATH.'public/runtime/app/db_caches/'))
	mkdir(APP_ROOT_PATH.'public/runtime/app/db_caches/',0777);
$pconnect = false;
$db = new mysql_db(app_conf('DB_HOST').":".app_conf('DB_PORT'), app_conf('DB_USER'),app_conf('DB_PWD'),app_conf('DB_NAME'),'utf8',$pconnect);
//end 定义DB

//定义模板引擎
require  APP_ROOT_PATH.'system/template/template.php';
if(!file_exists(APP_ROOT_PATH.'public/runtime/app/tpl_caches/'))
	mkdir(APP_ROOT_PATH.'public/runtime/app/tpl_caches/',0777);
	
if(!file_exists(APP_ROOT_PATH.'public/runtime/app/tpl_compiled/'))
	mkdir(APP_ROOT_PATH.'public/runtime/app/tpl_compiled/',0777);
$tmpl = new AppTemplate;
$tmpl->template_dir   = APP_ROOT_PATH . 'app/Tpl/' . app_conf("TEMPLATE");
$tmpl->cache_dir      = APP_ROOT_PATH . 'public/runtime/app/tpl_caches';
$tmpl->compile_dir    = APP_ROOT_PATH . 'public/runtime/app/tpl_compiled';
//end 定义模板引擎

require APP_ROOT_PATH.'system/utils/es_cookie.php';

$lang = require APP_ROOT_PATH.'/app/Lang/'.app_conf("SHOP_LANG").'/lang.php';



/**
 * 
 * @param $coupon_sn    团购券序列号
 * @param $msg			日志内容
 * @param $query_id		第三方验证时提供的对帐ID
 */
function log_coupon($coupon_sn,$msg,$query_id = '')
{
	$data = array();
	$data['coupon_sn'] = $coupon_sn;
	$data['msg'] = $msg;
	$data['query_id'] = $query_id;
	$data['create_time'] = get_gmtime();
	$GLOBALS['db']->autoExecute(DB_PREFIX."coupon_log",$data); //插入			
}
//获取GMTime
function get_gmtime()
{
	return (time() - date('Z'));
}
function to_date($utc_time, $format = 'Y-m-d H:i:s') {
	if (empty ( $utc_time )) {
		return '';
	}
	$timezone = intval(app_conf('TIME_ZONE'));
	$time = $utc_time + $timezone * 3600; 
	return date ($format, $time );
}
//获取客户端IP
function get_client_ip() {
	if (getenv ( "HTTP_CLIENT_IP" ) && strcasecmp ( getenv ( "HTTP_CLIENT_IP" ), "unknown" ))
		$ip = getenv ( "HTTP_CLIENT_IP" );
	else if (getenv ( "HTTP_X_FORWARDED_FOR" ) && strcasecmp ( getenv ( "HTTP_X_FORWARDED_FOR" ), "unknown" ))
		$ip = getenv ( "HTTP_X_FORWARDED_FOR" );
	else if (getenv ( "REMOTE_ADDR" ) && strcasecmp ( getenv ( "REMOTE_ADDR" ), "unknown" ))
		$ip = getenv ( "REMOTE_ADDR" );
	else if (isset ( $_SERVER ['REMOTE_ADDR'] ) && $_SERVER ['REMOTE_ADDR'] && strcasecmp ( $_SERVER ['REMOTE_ADDR'], "unknown" ))
		$ip = $_SERVER ['REMOTE_ADDR'];
	else
		$ip = "unknown";
	return ($ip);
}



//发团购券确认使用的短信
function send_use_coupon_sms($deal_coupon_id)
{	
	if(app_conf("SMS_ON")==1&&app_conf("SMS_USE_COUPON")==1)
	{
		$coupon_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_coupon where id = ".$deal_coupon_id);						
		if($coupon_data)
		{
			$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$coupon_data['user_id']);
			if($user_info['mobile']!='')
			{
				$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_SMS_USE_COUPON'");				
				$tmpl_content = $tmpl['content'];
				$coupon_data['confirm_time_format'] = to_date($coupon_data['confirm_time'],'Y-m-d H:i:s');
				$coupon_data['user_name'] = $user_info['user_name'];
				$coupon_data['deal_name'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal_order_item where id = ".$coupon_data['order_deal_id']);
				$coupon_data['deal_sub_name'] = $GLOBALS['db']->getOne("select sub_name from ".DB_PREFIX."deal_order_item where id = ".$coupon_data['order_deal_id']);
				
				$order_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order_item where id = ".$coupon_data['order_deal_id']);
				$deal_type = intval($GLOBALS['db']->getOne("select deal_type from ".DB_PREFIX."deal where id = ".intval($order_item['deal_id'])));
				if($deal_type == 1)
				{
					$coupon_data['deal_name'].= " ".$GLOBALS['lang']['BUY_NUMBER']."(".$order_item['number'].")";
					$coupon_data['deal_sub_name'].= " ".$GLOBALS['lang']['BUY_NUMBER']."(".$order_item['number'].")";
				}
				$GLOBALS['tmpl']->assign("coupon",$coupon_data);
				$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
				$msg_data['dest'] = $user_info['mobile'];
				$msg_data['send_type'] = 0;
				$msg_data['content'] = addslashes($msg);;
				$msg_data['send_time'] = 0;
				$msg_data['is_send'] = 0;
				$msg_data['create_time'] = get_gmtime();
				$msg_data['user_id'] = $user_info['id'];
				$msg_data['is_html'] = $tmpl['is_html'];
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入				
			}
		}		
	}
}

//发团购券确认使用的邮件
function send_use_coupon_mail($deal_coupon_id)
{
	if(app_conf("MAIL_ON")==1&&app_conf("MAIL_USE_COUPON")==1)
	{
		$coupon_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_coupon where id = ".$deal_coupon_id);				
		if($coupon_data)
		{
			$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$coupon_data['user_id']);
			
				$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_MAIL_USE_COUPON'");				
				$tmpl_content = $tmpl['content'];
				$coupon_data['confirm_time_format'] = to_date($coupon_data['confirm_time'],'Y-m-d H:i:s');
				$coupon_data['user_name'] = $user_info['user_name'];
				$coupon_data['deal_name'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal_order_item where id = ".$coupon_data['order_deal_id']);
				$coupon_data['deal_sub_name'] = $GLOBALS['db']->getOne("select sub_name from ".DB_PREFIX."deal_order_item where id = ".$coupon_data['order_deal_id']);
				
				$order_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order_item where id = ".$coupon_data['order_deal_id']);
				$deal_type = intval($GLOBALS['db']->getOne("select deal_type from ".DB_PREFIX."deal where id = ".intval($order_item['deal_id'])));
				if($deal_type == 1)
				{
					$coupon_data['deal_name'].= " ".$GLOBALS['lang']['BUY_NUMBER']."(".$order_item['number'].")";
					$coupon_data['deal_sub_name'].= " ".$GLOBALS['lang']['BUY_NUMBER']."(".$order_item['number'].")";
				}
				$GLOBALS['tmpl']->assign("coupon",$coupon_data);
				$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
				$msg_data['dest'] = $user_info['email'];
				$msg_data['send_type'] = 1;
				$msg_data['content'] = addslashes($msg);;
				$msg_data['send_time'] = 0;
				$msg_data['is_send'] = 0;
				$msg_data['create_time'] = get_gmtime();
				$msg_data['user_id'] = $user_info['id'];
				$msg_data['is_html'] = $tmpl['is_html'];
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入				
			
		}		
	}
}
?>