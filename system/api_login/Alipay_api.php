<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------

$api_lang = array(
	'name'	=>	'支付宝通用登录',
	'app_key'	=>	'合作者身份ID',
	'app_secret'	=>	'安全检验码',
);

$config = array(
	'app_key'	=>	array(
		'INPUT_TYPE'	=>	'0',
	), //合作者身份ID
	'app_secret'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //安全检验码
);

/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
	if(ACTION_NAME=='install')
	{
		//更新字段
		$GLOBALS['db']->query("ALTER TABLE `".DB_PREFIX."user`  ADD COLUMN `alipay_id`  varchar(255) NOT NULL",'SILENT');
	}
    $module['class_name']    = 'Alipay';

    /* 名称 */
    $module['name']    = $api_lang['name'];

	$module['config'] = $config;
	
	$module['lang'] = $api_lang;
    
    return $module;
}

// 支付宝快捷登录接口
require_once(APP_ROOT_PATH.'system/libs/api_login.php');
class Alipay_api implements api_login {
	
	private $api;
	
	public function __construct($api)
	{
		$api['config'] = unserialize($api['config']);
		$this->api = $api;
	}
	
	public function get_api_url()
	{
		require_once APP_ROOT_PATH.'system/api_login/alipay/alipay_user_service.php';
		$callback = SITE_DOMAIN.APP_ROOT."/api_callback.php?c=Alipay";
		es_session::start();
		//构造要请求的参数数组
		$parameter = array(
		        "service"			=> "user_authentication",	//接口名称，不需要修改
		        "partner"			=> $this->api['config']['app_key'],
		        "return_url"		=> $callback,
		        "_input_charset"	=> 'utf-8',
				
		);
		
		//构造请求函数
		$alipay = new alipay_user_service($parameter,$this->api['config']['app_secret'],"MD5");
		$sHtmlText = $alipay->build_form($this->api['icon']);
		return $sHtmlText;
	}
	
	public function get_big_api_url()
	{
		require_once APP_ROOT_PATH.'system/api_login/alipay/alipay_user_service.php';
		$callback = SITE_DOMAIN.APP_ROOT."/api_callback.php?c=Alipay";
		es_session::start();
		//构造要请求的参数数组
		$parameter = array(
		        "service"			=> "user_authentication",	//接口名称，不需要修改
		        "partner"			=> $this->api['config']['app_key'],
		        "return_url"		=> $callback,
		        "_input_charset"	=> 'utf-8',
				
		);
		
		//构造请求函数
		$alipay = new alipay_user_service($parameter,$this->api['config']['app_secret'],"MD5");
		$sHtmlText = $alipay->build_form($this->api['bicon']);
		return $sHtmlText;
	}
	
	public function callback()
	{
		require_once APP_ROOT_PATH.'system/api_login/alipay/alipay_notify.php';
		es_session::start();
		//构造通知函数信息
		$alipay = new alipay_notify($this->api['config']['app_key'],$this->api['config']['app_secret'],"MD5","utf-8","http");
		//计算得出通知验证结果
		$verify_result = $alipay->return_verify();
		
		if($verify_result) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代码
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
		    //获取支付宝的通知返回参数
		    $user_id           = $_GET['user_id'];		//获取支付宝用户唯一ID号
			
			//判断获取到的user_id的值是否在商户会员数据库中存在（即：是否曾经做过支付宝会员免注册登陆）
			//	若不存在，则程序自动为会员快速注册一个会员，把信息插入商户网站会员数据表中，
			//	且把该会员的在商户网站上的登录状态，更改成“已登录”状态。并记录在商家网站会员数据表中记录登陆信息，如登陆时间、次数、IP等。
			//	若存在，判断该会员在商户网站上的登录状态是否是“已登录”状态
			//		若不是，则把该会员的在商户网站上的登录状态，更改成“已登录”状态。并记录在商家网站会员数据表中记录登陆信息，如登陆时间、次数、IP等。
			//		若是，则不做任何数据库业务逻辑处理。判定该次反馈信息为重复刷新返回链接导致。
			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$msg['id'] = $user_id;
			$msg['name'] = "ali_".$user_id;
		    $msg['field'] = 'alipay_id';
		    es_session::set("api_user_info",$msg);
			$user_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where alipay_id = ".$msg['id']." and alipay_id <> 0");	
			if($user_data)
			{
					$user_current_group = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_group where id = ".intval($user_data['group_id']));
					$user_group = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_group where score <=".intval($user_data['score'])." order by score desc");
					if($user_current_group['score']<$user_group['score'])
					{
						$user_data['group_id'] = intval($user_group['id']);
					}
					es_session::set("user_info",$user_data);
					$GLOBALS['db']->query("update ".DB_PREFIX."user set login_ip = '".CLIENT_IP."',login_time= ".TIME_UTC.",group_id=".intval($user_data['group_id'])." where id =".$user_data['id']);				
					//$GLOBALS['db']->query("update ".DB_PREFIX."deal_cart set user_id = ".intval($_SESSION['user_info']['id'])." where session_id = '".es_session::id()."'");
					es_session::delete("api_user_info");
					app_recirect_preview();
			}
			else
			app_redirect(url("shop","user#api_login"));
		    
		}
		else {
		    //验证失败
		    //如要调试，请看alipay_notify.php页面的return_verify函数，比对sign和mysign的值是否相等，或者检查$veryfy_result有没有返回true
		    echo "fail";
		}		
		exit;		
	}
	
	public function get_title()
	{
		return '支付宝通用登录';
	}
	public function create_user()
	{
		$s_api_user_info = es_session::get("api_user_info");
		$user_data['user_name'] = $s_api_user_info['name'];
		$user_data['user_pwd'] = md5(rand(100000,999999));
		$user_data['create_time'] = TIME_UTC;
		$user_data['update_time'] = TIME_UTC;
		$user_data['login_ip'] = CLIENT_IP;
		$user_data['group_id'] = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."user_group order by score asc limit 1");
		$user_data['is_effect'] = 1;
		$user_data['alipay_id'] = $s_api_user_info['id'];
		
		$count = 0;
		do{
			if($count>0)
			$user_data['user_name'] = $user_data['user_name'].$count;
			if(intval($user_data['alipay_id'])>0)
			$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user_data,"INSERT",'','SILENT');
			$rs = $GLOBALS['db']->insert_id();
			$count++;
		}while(intval($rs)==0&&intval($user_data['alipay_id'])>0);
		
		$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".intval($rs));
		if($rs > 0)
		{
			$user_id = $rs;	
			$register_money = doubleval(app_conf("USER_REGISTER_MONEY"));
			$register_score = intval(app_conf("USER_REGISTER_SCORE"));
			$register_point = intval(app_conf("USER_REGISTER_POINT"));
			$register_lock_money = intval(app_conf("USER_LOCK_MONEY"));
			if($register_money>0||$register_score>0 || $register_point > 0 || $register_lock_money>0)
			{
				$user_get['score'] = $register_score;
				$user_get['money'] = $register_money;
				$user_get['point'] = $register_point;
				$user_get['lock_money'] = $register_lock_money;
				require_once(APP_ROOT_PATH."system/libs/user.php");
				modify_account($user_get,intval($user_id),"在".to_date(TIME_UTC)."注册成功",18);
			}
		}
		es_session::set("user_info",$user_info);
		es_session::delete("api_user_info");
	}	
}
?>