<?php
// +----------------------------------------------------------------------
// | Fanwe 方维订餐小秘书商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------

$api_lang = array(
	'name'	=>	'QQv2登录插件',
	'app_key'	=>	'QQAPI应用appid',
	'app_secret'	=>	'QQAPI应用appkey',
);

$config = array(
	'app_key'	=>	array(
		'INPUT_TYPE'	=>	'0',
	), //腾讯API应用的KEY值
	'app_secret'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //腾讯API应用的密码值
);

/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
	if(ACTION_NAME=='install')
	{
		//更新字段
		$GLOBALS['db']->query("ALTER TABLE `".DB_PREFIX."user`  ADD COLUMN `qq_id`  varchar(255) NOT NULL",'SILENT');
	}
    $module['class_name']    = 'Qqv2';

    /* 名称 */
    $module['name']    = $api_lang['name'];

	$module['config'] = $config;
	
	$module['lang'] = $api_lang;
    
    return $module;
}

// QQ的api登录接口
require_once(APP_ROOT_PATH.'system/libs/api_login.php');
class Qqv2_api implements api_login {
	
	private $api;
	
	public function __construct($api)
	{
		$api['config'] = unserialize($api['config']);
		$this->api = $api;
	}
	
	public function get_api_url()
	{
		es_session::start();
		$inc=array();
		$callback = SITE_DOMAIN.APP_ROOT."/qqv2_callback.php";
		$scope="get_user_info,add_share,list_album,add_album,upload_pic,add_topic,add_one_blog,add_weibo,check_page_fans,add_t,add_pic_t,del_t,get_repost_list,get_info,get_other_info,get_fanslist,get_idolist,add_idol,del_idol,get_tenpay_addr";
		$inc['appid']=$this->api['config']['app_key'];
		$inc['appkey']=$this->api['config']['app_secret'];
		$inc['callback']=$callback;
		$inc['scope']=$scope;
		$inc['errorReport']=1;
		$inc['storageType']="file";
		$inc['host']=SITE_DOMAIN;
		$setting = json_encode($inc);
		@file_put_contents(APP_ROOT_PATH."/public/qqv2_inc.php",$setting);
		@chmod(APP_ROOT_PATH."/public/qqv2_inc.php",0777);
		$url = SITE_DOMAIN.APP_ROOT."/system/api_login/qqv2/qq_login.php";	
		$str = "<a href='".$url."' title='".$this->api['name']."'><img src='".$this->api['icon']."' alt='".$this->api['name']."' /></a>&nbsp;";
		return $str;
	}
	
	public function get_big_api_url()
	{
		es_session::start();
		$inc=array();
		$callback = SITE_DOMAIN.APP_ROOT."/qqv2_callback.php";
		$scope="get_user_info,add_share,list_album,add_album,upload_pic,add_topic,add_one_blog,add_weibo,check_page_fans,add_t,add_pic_t,del_t,get_repost_list,get_info,get_other_info,get_fanslist,get_idolist,add_idol,del_idol,get_tenpay_addr";
		$inc['appid']=$this->api['config']['app_key'];
		$inc['appkey']=$this->api['config']['app_secret'];
		$inc['callback']=$callback;
		$inc['scope']=$scope;
		$inc['errorReport']=1;
		$inc['storageType']="file";
		$inc['host']=SITE_DOMAIN;
		$setting = json_encode($inc);
		@file_put_contents(APP_ROOT_PATH."/public/qqv2_inc.php",$setting);
		@chmod(APP_ROOT_PATH."/public/qqv2_inc.php",0777);
		$url = SITE_DOMAIN.APP_ROOT."/system/api_login/qqv2/qq_login.php";
		$str = "<a href='".$url."' title='".$this->api['name']."'><img src='".$this->api['bicon']."' alt='".$this->api['name']."' /></a>&nbsp;";
		return $str;
	}
		
	public function callback()
	{
		es_session::start();	
		require_once(APP_ROOT_PATH."system/api_login/qqv2/qqConnectAPI.php");
		$qc = new QC();
		$access_token =$qc->qq_callback();
		$openid = $qc->get_openid();
		$use_info_keysArr = array(
            "access_token" => $access_token,
			"openid" => $openid,
		 	"oauth_consumer_key" => $this->api['config']['app_key']
        );
		$use_info_url="https://graph.qq.com/user/get_user_info";
        $graph_use_info_url = $qc->urlUtils->combineURL($use_info_url, $use_info_keysArr);
        $response = $qc->urlUtils->get_contents($graph_use_info_url);
        $arr = array();
    	$arr = json_decode( $response, true);
			
		$msg['field'] = 'qq_id';
		$msg['id'] = $openid;
		$msg['name'] = $arr["nickname"];
		es_session::set("api_user_info",$msg);

		$user_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where qq_id = '".$openid."' and qq_id <> '' and is_effect=1 and is_delete=0");	
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
				es_session::delete("api_user_info");
				app_recirect_preview();
		}
		else
		{
			$this->create_user();
			app_redirect(APP_ROOT."/");
		}
		
	}
	
	public function get_title()
	{
		return 'QQv2登录接口，需要php_curl扩展的支持';
	}
	public function create_user()
	{
		$s_api_user_info = es_session::get("api_user_info");
		$user_data['user_name'] = $s_api_user_info['name'];
		$user_data['user_pwd'] = md5(rand(100000,999999));
		$user_data['email'] = "";
		$user_data['create_time'] = TIME_UTC;
		$user_data['update_time'] = TIME_UTC;
		$user_data['login_ip'] = CLIENT_IP;
		$user_data['group_id'] = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."user_group order by score asc limit 1");
		$user_data['is_effect'] = 1;
		$user_data['step'] = 1;
		$user_data['qq_id'] = $s_api_user_info['id'];
		$origin_username = $user_data['user_name'];
		$count = 0;
		do{
			if($count>0)
			$user_data['user_name'] = $origin_username.TIME_UTC;
			if($user_data['qq_id'])
			$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user_data,"INSERT",'','SILENT');
			$rs = $GLOBALS['db']->insert_id();
			$count++;
		}while(intval($rs)==0&&$user_data['qq_id']);
		
		
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