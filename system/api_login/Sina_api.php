<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

$api_lang = array(
	'name'	=>	'新浪微博api登录接口',
	'app_key'	=>	'新浪API应用APP_KEY',
	'app_secret'	=>	'新浪API应用APP_SECRET',
	'app_url'	=>	'回调地址',
);

$config = array(
	'app_key'	=>	array(
		'INPUT_TYPE'	=>	'0',
	), //新浪API应用的KEY值
	'app_secret'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //新浪API应用的密码值
	'app_url'	=>	array(
		'INPUT_TYPE'	=>	'0'
	),
);

/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
	if(ACTION_NAME=='install' || ACTION_NAME=='update')
	{
		//更新字段
		$GLOBALS['db']->query("ALTER TABLE `".DB_PREFIX."user`  ADD COLUMN `sina_id`  varchar(255) NOT NULL",'SILENT');
		$GLOBALS['db']->query("ALTER TABLE `".DB_PREFIX."user`  MODIFY COLUMN `sina_id`  varchar(255) NOT NULL",'SILENT');
		$GLOBALS['db']->query("ALTER TABLE `".DB_PREFIX."user`  ADD COLUMN `sina_token`  varchar(255) NOT NULL",'SILENT');
		$GLOBALS['db']->query("ALTER TABLE `".DB_PREFIX."user`  MODIFY COLUMN `sina_token`  varchar(255) NOT NULL",'SILENT');
		$GLOBALS['db']->query("ALTER TABLE `".DB_PREFIX."user`  ADD COLUMN `is_syn_sina`  tinyint(1) NOT NULL",'SILENT');
		$GLOBALS['db']->query("ALTER TABLE `".DB_PREFIX."user`  MODIFY COLUMN `is_syn_sina`  varchar(255) NOT NULL",'SILENT');
	}
    $module['class_name']    = 'Sina';

    /* 名称 */
    $module['name']    = $api_lang['name'];

	$module['config'] = $config;
	$module['is_weibo'] = 1;  //可以同步发送微博
	
	$module['lang'] = $api_lang;
    
    return $module;
}

// 新浪的api登录接口
require_once(APP_ROOT_PATH.'system/libs/api_login.php');
class Sina_api implements api_login {
	
	private $api;
	
	public function __construct($api)
	{
		$api['config'] = unserialize($api['config']);
		$this->api = $api;
	}
	
	public function get_api_url()
	{
		require_once APP_ROOT_PATH.'system/api_login/sina/saetv2.ex.class.php';
		$o = new SaeTOAuthV2($this->api['config']['app_key'],$this->api['config']['app_secret']);
		es_session::start();
		//$keys = $o->getRequestToken();
		if($this->api['config']['app_url']=="")
		{
			$app_url = SITE_DOMAIN.APP_ROOT."/api_callback.php?c=Sina";
		}
		else
		{
			$app_url = $this->api['config']['app_url'];
		}
		$aurl = $o->getAuthorizeURL($app_url);

		es_session::set("is_bind",0);
		
		
		$str = "<a href='".$aurl."' title='".$this->api['name']."'><img src='".$this->api['icon']."' alt='".$this->api['name']."' /></a>&nbsp;";
		return $str;
	}
	
	public function get_big_api_url()
	{
		require_once APP_ROOT_PATH.'system/api_login/sina/saetv2.ex.class.php';
		$o = new SaeTOAuthV2($this->api['config']['app_key'],$this->api['config']['app_secret']);
		es_session::start();
		//$keys = $o->getRequestToken();
		//$aurl = $o->getAuthorizeURL($keys['oauth_token'] ,false , SITE_DOMAIN.APP_ROOT."/api_callback.php?c=Sina");

		if($this->api['config']['app_url']=="")
		{
			$app_url = SITE_DOMAIN.APP_ROOT."/api_callback.php?c=Sina";
		}
		else
		{
			$app_url = $this->api['config']['app_url'];
		}
		$aurl = $o->getAuthorizeURL($app_url);
		es_session::set("is_bind",0);		
		
		$str = "<a href='".$aurl."' title='".$this->api['name']."'><img src='".$this->api['bicon']."' alt='".$this->api['name']."' /></a>&nbsp;";
		return $str;
	}	
	
	public function get_bind_api_url()
	{
		require_once APP_ROOT_PATH.'system/api_login/sina/saetv2.ex.class.php';
		$o = new SaeTOAuthV2($this->api['config']['app_key'],$this->api['config']['app_secret']);
		es_session::start();
		//$keys = $o->getRequestToken();
		if($this->api['config']['app_url']=="")
		{
			$app_url = SITE_DOMAIN.APP_ROOT."/api_callback.php?c=Sina";
		}
		else
		{
			$app_url = $this->api['config']['app_url'];
		}
		$aurl = $o->getAuthorizeURL($app_url);	
		es_session::set("is_bind",1);
		
		$str = "<a href='".$aurl."' title='".$this->api['name']."'><img src='".$this->api['bicon']."' alt='".$this->api['name']."' /></a>&nbsp;";
		return $str;
	}	
	
	public function callback()
	{
		require_once APP_ROOT_PATH.'system/api_login/sina/saetv2.ex.class.php';
		es_session::start();
		//$sina_keys = es_session::get("sina_keys");
		$o = new SaeTOAuthV2($this->api['config']['app_key'],$this->api['config']['app_secret']);
		if (isset($_REQUEST['code'])) {
			$keys = array();
			$keys['code'] = $_REQUEST['code'];
			if($this->api['config']['app_url']=="")
			{
				$app_url = SITE_DOMAIN.APP_ROOT."/api_callback.php?c=Sina";
			}
			else
			{
				$app_url = $this->api['config']['app_url'];
			}
			$keys['redirect_uri'] = $app_url;
			try {
				$token = $o->getAccessToken( 'code', $keys ) ;
			} catch (OAuthException $e) {
				//print_r($e);exit;
				showErr("授权失败,错误信息：".$e->getMessage());
				die();
			}
		}
		
		
		$c = new SaeTClientV2($this->api['config']['app_key'],$this->api['config']['app_secret'] ,$token['access_token'] );
		$ms  = $c->home_timeline(); // done
		$uid_get = $c->get_uid();
		$uid = $uid_get['uid'];
		$msg = $c->show_user_by_id( $uid);//根据ID获取用户等基本信息
		
		if(intval($msg['error_code'])!=0){
			showErr("授权失败,错误代码:".$msg['error_code']);
			die();
		}
		
		$msg['field'] = 'sina_id';
		$msg['sina_token'] = $token['access_token'];
		es_session::set("api_user_info",$msg);
		
		if(!$msg['name'])
		{
		   app_redirect(url("index"));
		   exit();
		}
		
		$user_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where sina_id = '".$msg['id']."' and sina_id <> 0");	
		//print_r($user_data);die();
		if($user_data)
		{
				$user_current_group = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_group where id = ".intval($user_data['group_id']));
				$user_group = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_group where score <=".intval($user_data['score'])." order by score desc");
				if($user_current_group['score']<$user_group['score'])
				{
					$user_data['group_id'] = intval($user_group['id']);
				}				
				$GLOBALS['db']->query("update ".DB_PREFIX."user set sina_token = '".$token['access_token']."',login_ip = '".CLIENT_IP."',login_time= ".get_gmtime().",group_id=".intval($user_data['group_id'])." where id =".$user_data['id']);				
				es_session::delete("api_user_info");
				$is_bind = intval(es_session::get("is_bind"));				
				if($is_bind)
				{
					if(intval($user_data['id'])!=intval($GLOBALS['user_info']['id']))
					{
						showErr("该帐号已经被别的会员绑定过，请直接用帐号登录",0,url("shop","uc_center#setweibo"));
					}
					else
					{
						es_session::set("user_info",$user_data);
						app_redirect(url("index","uc_center#setweibo"));
					}
				}
				else
				{
					require_once APP_ROOT_PATH."system/libs/user.php";
					auto_do_login_user($user_data['user_name'],md5($user_data['user_pwd']."_EASE_COOKIE"),$from_cookie = false);
					app_recirect_preview();
				}
		}
		elseif($is_bind==1&&$GLOBALS['user_info'])
		{
			
			//当有用户身份且要求绑定时
			$GLOBALS['db']->query("update ".DB_PREFIX."user set sina_id= '".intval($msg['id'])."', sina_token ='".$token['access_token']."' where id =".$GLOBALS['user_info']['id']);						
			app_redirect(url("index","uc_center#setweibo"));
		}
		else{
			 $this->create_user();
			app_redirect(get_gopreview());
		}
		
		
	}
	
	public function get_title()
	{
		return '新浪api登录接口，需要php_curl扩展的支持(V2)';
	}
	
	public function create_user()
	{
		$s_api_user_info = es_session::get("api_user_info");
		$user_data['user_name'] = $s_api_user_info['name'];
		$user_data['user_pwd'] = md5(rand(100000,999999));
		$user_data['create_time'] = get_gmtime();
		$user_data['update_time'] = get_gmtime();
		$user_data['login_ip'] = CLIENT_IP;
		$user_data['group_id'] = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."user_group order by score asc limit 1");
		$user_data['is_effect'] = 1;
		$user_data['sina_id'] = $s_api_user_info['id'];
		$user_data['sina_token'] = $s_api_user_info['sina_token'];
		//$user_data['sina_app_secret'] = $s_api_user_info['app_secret'];
		$count = 0;
		
		do{
			if($count>0)
			{
			   $user_data['user_name'] = $user_data['user_name'].$count;
			}
			
			if($user_data['sina_id']>0)
			{
			  $GLOBALS['db']->autoExecute(DB_PREFIX."user",$user_data,"INSERT",'','SILENT');
			  $rs = $GLOBALS['db']->insert_id();
			  $count++;
			}
        
		}while($rs==0&&$user_data['sina_id']);
		
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
	
	
	//同步发表到新浪微博
	public function send_message($data)
	{
		static $client = NULL;
		if($client === NULL)
		{
			require_once APP_ROOT_PATH.'system/api_login/sina/saetv2.ex.class.php';
			$uid = intval($GLOBALS['user_info']['id']);
			$udata = $GLOBALS['db']->getRow("select sina_token from ".DB_PREFIX."user where id = ".$uid);
			$client = new SaeTClientV2($this->api['config']['app_key'],$this->api['config']['app_secret'],$udata['sina_token']);
		}
		try
		{
			if(empty($data['img']))
				$msg = $client->update($data['content']);
			else
				$msg = $client->upload($data['content'],$data['img']);

			if($msg['error'])
			{
				$result['status'] = false;
				$result['msg'] = "新浪微博同步失败，请偿试重新通过腾讯微博登录或得新授权。";
				ajax_return($result);
			}
			else
			{
				$result['status'] = true;
				$result['msg'] = "success";
				ajax_return($result);
			}

		}
		catch(Exception $e)
		{

		}
	}
	
}
?>