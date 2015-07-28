<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

$api_lang = array(
	'name'	=>	'腾讯微博登录插件',
	'app_key'	=>	'腾讯API应用APP_KEY',
	'app_secret'	=>	'腾讯API应用APP_SECRET',
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
		$GLOBALS['db']->query("ALTER TABLE `".DB_PREFIX."user`  ADD COLUMN `tencent_id`  varchar(255) NOT NULL",'SILENT');
		$GLOBALS['db']->query("ALTER TABLE `".DB_PREFIX."user`  ADD COLUMN `tencent_app_key`  varchar(255) NOT NULL",'SILENT');
		$GLOBALS['db']->query("ALTER TABLE `".DB_PREFIX."user`  ADD COLUMN `tencent_app_secret`  varchar(255) NOT NULL",'SILENT');
		$GLOBALS['db']->query("ALTER TABLE `".DB_PREFIX."user`  ADD COLUMN `is_syn_tencent`  tinyint(1) NOT NULL",'SILENT');
	}
    $module['class_name']    = 'Tencent';

    /* 名称 */
    $module['name']    = $api_lang['name'];

	$module['config'] = $config;
	$module['is_weibo'] = 1;  //可以同步发送微博
	
	$module['lang'] = $api_lang;
    
    return $module;
}
$debug = false;
es_session::start();
// 腾讯的api登录接口
require_once(APP_ROOT_PATH.'system/libs/api_login.php');
require_once APP_ROOT_PATH.'system/api_login/Tencent/Tencent.php';

class Tencent_api implements api_login {
	
	private $api;
	
	public function __construct($api)
	{
		$api['config'] = unserialize($api['config']);
		$this->api = $api;
		
	}
	
	public function get_api_url()
	{
		if($this->api['config']['app_key'] && $this->api['config']['app_secret']){
			OAuth::init($this->api['config']['app_key'], $this->api['config']['app_secret']);
			Tencent::$debug = $debug;
			
			if (es_session::is_set('t_access_token') || (es_session::is_set('t_openid') && es_session::is_set('t_openkey'))) {//用户已授权
				$aurl = SITE_DOMAIN.APP_ROOT."/api_callback.php?c=Tencent";
			}
			else{
				$aurl = OAuth::getAuthorizeURL(SITE_DOMAIN.APP_ROOT."/api_callback.php?c=Tencent");
			}
		}
		$str = "<a href='".$aurl."' title='".$this->api['name']."'><img src='".$this->api['icon']."' alt='".$this->api['name']."' /></a>&nbsp;";
		
		return $str;
	}
	
	public function get_big_api_url()
	{
		if($this->api['config']['app_key'] && $this->api['config']['app_secret']){
			OAuth::init($this->api['config']['app_key'], $this->api['config']['app_secret']);
			Tencent::$debug = $debug;
			
			if (es_session::is_set('t_access_token') || (es_session::is_set('t_openid') && es_session::is_set('t_openkey'))) {//用户已授权
				$aurl = SITE_DOMAIN.APP_ROOT."/api_callback.php?c=Tencent";
			}
			else{
				$aurl = OAuth::getAuthorizeURL(SITE_DOMAIN.APP_ROOT."/api_callback.php?c=Tencent");
			}
		}
		
		$str = "<a href='".$aurl."' title='".$this->api['name']."'><img src='".$this->api['bicon']."' alt='".$this->api['name']."' /></a>&nbsp;";
		return $str;
	}	
	
	public function get_bind_api_url()
	{
		if($this->api['config']['app_key'] && $this->api['config']['app_secret']){
			OAuth::init($this->api['config']['app_key'], $this->api['config']['app_secret']);
			Tencent::$debug = $debug;
			
			if (es_session::is_set('t_access_token') || (es_session::is_set('t_openid') && es_session::is_set('t_openkey'))) {//用户已授权
				$aurl = SITE_DOMAIN.APP_ROOT."/api_callback.php?c=Tencent";
			}
			else{
				$aurl = OAuth::getAuthorizeURL(SITE_DOMAIN.APP_ROOT."/api_callback.php?c=Tencent");
			}
		}
		
		$str = "<a href='".$aurl."' title='".$this->api['name']."'><img src='".$this->api['bicon']."' alt='".$this->api['name']."' /></a>&nbsp;";
		return $str;
	}		
	public function callback()
	{
		OAuth::init($this->api['config']['app_key'], $this->api['config']['app_secret']);
		Tencent::$debug = $debug;
		
		$callback = SITE_DOMAIN.APP_ROOT."/api_callback.php?c=Tencent";
		if (es_session::is_set('t_access_token') || (es_session::is_set('t_openid') && es_session::is_set('t_openkey'))) {//用户已授权
		    //echo '<pre><h3>已授权</h3>用户信息：<br>';
		    //获取用户信息
		    $r = Tencent::api('user/info');
		    $json_data = json_decode($r, true);
		    //print_r($json_data);
		   // echo '</pre>';
		    
		}
		else{
			if ($_GET['code']) {//已获得code
		        $code = $_GET['code'];
		        $openid = $_GET['openid'];
		        $openkey = $_GET['openkey'];
		        //获取授权token
		        $url = OAuth::getAccessToken($code, $callback);
		        $r = Http::request($url);
		        parse_str($r, $out);
		        //存储授权数据
		        if ($out['access_token']) {
		            es_session::set('t_access_token',$out['access_token']);
		          	es_session::set('refresh_token',$out['refresh_token']);
		            es_session::set('expires_in',$out['expires_in']);
		            es_session::set('t_code',$code);
		            es_session::set('t_openid',$openid);
		            es_session::set('t_openkey',$openkey);
		            
		            //验证授权
		            $r = OAuth::checkOAuthValid();
		            if ($r) {
		                app_redirect($callback);//刷新页面
		            } else {
		                exit('<h3>授权失败,请重试</h3>');
		            }
		        } else {
		            exit($r);
		        }
		    } else {//获取授权code
		        if ($_GET['openid'] && $_GET['openkey']){//应用频道
		            s_session::set('t_openid',$_GET['openid']);
		            es_session::set('t_openkey',$_GET['openkey']);
		            //验证授权
		            $r = OAuth::checkOAuthValid();
		            if ($r) {
		                app_redirect($callback);//刷新页面
		            } else {
		                exit('<h3>授权失败,请重试</h3>');
		            }
		        } else{
		            $url = OAuth::getAuthorizeURL($callback);
		            app_redirect($url);
		        }
		    }
		}
		
		if($json_data['msg'] != "ok"){
		    echo '<pre><h3>出错了</h3><pre>';
		    die();
		}
		
		$is_bind = intval($_REQUEST['is_bind']);
		
		$tencent_id = $json_data['data']['openid'];		

		$msg['field'] = 'tencent_id';
		$msg['id'] = $tencent_id;
		$msg['name'] = $json_data['data']['name'];
		es_session::set("api_user_info",$msg);
		$user_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where tencent_id = '".$tencent_id."' and tencent_id <> ''");	
		if($user_data)
		{
				$user_current_group = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_group where id = ".intval($user_data['group_id']));
				$user_group = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_group where score <=".intval($user_data['score'])." order by score desc");
				if($user_current_group['score']<$user_group['score'])
				{
					$user_data['group_id'] = intval($user_group['id']);
				}
				//$GLOBALS['db']->query("update ".DB_PREFIX."user set tencent_app_key ='".$last_key['oauth_token']."',tencent_app_secret = '".$last_key['oauth_token_secret']."', login_ip = '".CLIENT_IP."',login_time= ".TIME_UTC.",group_id=".intval($user_data['group_id'])." where id =".$user_data['id']);				
				//$GLOBALS['db']->query("update ".DB_PREFIX."deal_cart set user_id = ".intval($user_data['id'])." where session_id = '".es_session::id()."'");
				es_session::delete("api_user_info");
				if($is_bind)
				{
					if(intval($user_data['id'])!=intval($GLOBALS['user_info']['id']))
					{
						showErr("该帐号已经被别的会员绑定过，请直接用帐号登录",0,url("shop","uc_center#setweibo"));
					}
					else
					{
						es_session::set("user_info",$user_data);
						app_redirect(url("shop","uc_center#setweibo"));
					}
				}
				else
				{
					es_session::set("user_info",$user_data);
					app_recirect_preview();
				}
		}
		elseif($is_bind==1&&$GLOBALS['user_info'])
		{
			//当有用户身份且要求绑定时
			$GLOBALS['db']->query("update ".DB_PREFIX."user set tencent_id= '".$tencent_id."' where id =".$GLOBALS['user_info']['id']);						
			app_redirect(url("index","uc_center#setweibo"));
		}
		else{
			$this->create_user();
			//app_redirect(url("index","user#api_login"));
			app_recirect_preview();
		}
		
	}
	
	public function get_title()
	{
		return '腾讯api登录接口，需要php_curl扩展的支持';
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
		$user_data['tencent_id'] = $s_api_user_info['id'];
		
		$count = 0;
		do{
			if($count>0)
			$user_data['user_name'] = $user_data['user_name'].$count;
			if($user_data['tencent_id'])
			$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user_data,"INSERT",'','SILENT');
			$rs = $GLOBALS['db']->insert_id();
			$count++;
		}while(intval($rs)==0&&$user_data['tencent_id']);
		
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
	
	public function send_message($data)
	{
		static $client = NULL;
		if($client === NULL)
		{
			define( "MB_RETURN_FORMAT" , 'json' );
			define( "MB_API_HOST" , 'open.t.qq.com' );
			require_once APP_ROOT_PATH.'system/api_login/Tencent/api_client.php';
			$uid = intval($GLOBALS['user_info']['id']);
			$udata = $GLOBALS['db']->getRow("select tencent_app_key,tencent_app_secret from ".DB_PREFIX."user where id = ".$uid);
	
			$client = new MBApiClient( $this->api['config']['app_key'],$this->api['config']['app_secret'],$udata['tencent_app_key'],$udata['tencent_app_secret']);
		}
		
		$p['c'] = $data['content'];
		
		//组装autho类所需的图片参数内容
		if(!empty($data['img']))
		{
			$filename = $data['img'];
			$pic[0] = $this->get_image_mime($filename);
			$pic[1] = reset( explode( '?' , basename( $filename ) ));
			$pic[2] = file_get_contents($filename);
			$p['p'] = $pic;
		}
		
		$p['ip'] = CLIENT_IP;
		$p['type']	=1;
		
		try
		{
			$msg = $client->postOne($p);
//			echo "success";
//			print_r($msg);
		
		}
		catch(Exception $e)
		{
//			echo "error";
//			print_r($e);
		}
	}
	
    private function get_image_mime( $file )
    {
    	$ext = strtolower(pathinfo( $file , PATHINFO_EXTENSION ));
    	switch( $ext )
    	{
    		case 'jpg':
    		case 'jpeg':
    			$mime = 'image/jpg';
    			break;
    		 	
    		case 'png';
    			$mime = 'image/png';
    			break;
    			
    		case 'gif';
    		default:
    			$mime = 'image/gif';
    			break;    		
    	}
    	return $mime;
    }

}
?>