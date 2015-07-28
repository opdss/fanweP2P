<?php 
// +----------------------------------------------------------------------
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------


/**
 非明确的操作时,禁止提交如下几个参数名(因为这个参数名，会被覆盖)
 $request_param['city_id']=$city_id;
 $request_param['city_name']=$city_name;

 $request_param['uid']=es_session::get("uid");
 $request_param['pwd']=es_session::get("user_pwd");
 $request_param['email']=es_session::get("user_name");

 $request_param['supplier_id']=es_session::get("supplier_id");
 $request_param['biz_email']=es_session::get("biz_email");
 $request_param['biz_pwd']=es_session::get("biz_pwd");

 $request_param['m_latitude']= es_session::get("m_latitude");
 $request_param['m_longitude']= es_session::get("m_longitude");
 */

define('TMPL_NAME','fanwe');

require '../system/common.php';
require './lib/page.php';
require './lib/functions.php';
require './lib/transport.php';
require './lib/template.php';


define('AS_LOG_DIR', APP_ROOT_PATH.'wap/log/');
define('AS_DEBUG', true);
require './lib/logUtils.php';

if (es_cookie::is_set("is_pc")){
	es_cookie::delete("is_pc");
}

$transport = new transport;
$transport->use_curl = true;
//调用模板引擎
//require_once  APP_ROOT_PATH.'system/template/template.php';
if(!file_exists(APP_ROOT_PATH.'public/runtime/wap/'))
	mkdir(APP_ROOT_PATH.'public/runtime/wap/',0777);

if(!file_exists(APP_ROOT_PATH.'public/runtime/wap/tpl_caches/'))
	mkdir(APP_ROOT_PATH.'public/runtime/wap/tpl_caches/',0777);

if(!file_exists(APP_ROOT_PATH.'public/runtime/wap/tpl_compiled/'))
	mkdir(APP_ROOT_PATH.'public/runtime/wap/tpl_compiled/',0777);

if(!file_exists(APP_ROOT_PATH.'public/runtime/wap/statics/'))
	mkdir(APP_ROOT_PATH.'public/runtime/wap/statics/',0777);

$tmpl = new WapTemplate;
$tmpl->template_dir   = APP_ROOT_PATH . 'wap/tpl/'.TMPL_NAME;
$tmpl->cache_dir      = APP_ROOT_PATH . 'public/runtime/wap/tpl_caches';
$tmpl->compile_dir    = APP_ROOT_PATH . 'public/runtime/wap/tpl_compiled';
$tmpl->assign("TMPL_REAL", APP_ROOT_PATH . 'wap/tpl/'.TMPL_NAME);
//定义模板路径
$tmpl_path = SITE_DOMAIN.APP_ROOT.'/tpl/'.TMPL_NAME;
$tmpl->assign("TMPL",$tmpl_path);

//访问wap时，去除使用pc端访问，标志
if (isset($_COOKIE["is_pc"])){
	setcookie ("is_pc", null);
	setcookie ("is_pc", time()-3600);
	unset($_COOKIE["is_pc"]);
}

if (isset($_REQUEST['i_type']))
{
	$i_type = intval($_REQUEST['i_type']);
}

//$_REQUEST = array_merge($_GET,$_POST);
$request_param = $_REQUEST;

//将客户ip,传到mapi接口
$request_param['client_ip']= get_client_ip();

if(isset($request_param['ctl'])){
	$class = strtolower(strim($request_param['ctl']));
		
}else{
	$class='init';
}

if(isset($request_param['act'])){
$act2 = strtolower(strim($request_param['act']))?strtolower(strim($request_param['act'])):"";
}else{
	$act2='index';
}

if (empty($act2)) $act2='index';




//获取模板文件的名称
$tmpl_dir=$class.'.html';
//=========================

//$request_url = 'http://127.0.0.1/'.str_replace('/wap', '', APP_ROOT).'/sjmapi/index.php';
$request_url = SITE_DOMAIN.str_replace('/wap', '', APP_ROOT).'/mapi/index.php';

//echo get_domain()."<br>;".APP_ROOT; exit;

//存储邀请人的id
if($_REQUEST['r'])
{
	$rid = intval(base64_decode($_REQUEST['r']));
	$ref_uid = intval($GLOBALS['db']->getOne("select id from ".DB_PREFIX."user where id = ".intval($rid)));
	es_cookie::set("REFERRAL_USER",intval($ref_uid));
}
else
{
	//获取存在的推荐人ID
	if(intval(es_cookie::get("REFERRAL_USER"))>0)
	$ref_uid = intval($GLOBALS['db']->getOne("select id from ".DB_PREFIX."user where id = ".intval(es_cookie::get("REFERRAL_USER"))));
	$GLOBALS['tmpl']->assign('ref_uid',$ref_uid);//邀请人的id
}


if($class =='login_out'){
	/*
	es_session::delete("uid");
	es_session::delete("user_name");
	es_session::delete("user_pwd");
	
	
	
	//cookie
	es_cookie::delete("uid");
	*/
	
	es_cookie::delete("user_name");
	es_cookie::delete("user_pwd");
	es_session::delete("user_info");
	
	showSuccess('退出成功!',0,url('index','login#index'));
}

//会员自动登录及输出
$cookie_uname = es_cookie::get("user_name")?es_cookie::get("user_name"):'';
$cookie_upwd = es_cookie::get("user_pwd")?es_cookie::get("user_pwd"):'';


logUtils::log_str($cookie_uname);
logUtils::log_str($cookie_upwd);

if($cookie_uname!=''&&$cookie_upwd!=''&&!es_session::get("user_info"))
{
	logUtils::log_str("=======1=======");
	$cookie_uname = addslashes(trim(htmlspecialchars($cookie_uname)));
	$cookie_upwd = addslashes(trim(htmlspecialchars($cookie_upwd)));
	require_once APP_ROOT_PATH."system/libs/user.php";
	//require_once APP_ROOT_PATH."app/Lib/common.php";
	auto_do_login_user($cookie_uname,$cookie_upwd);
	
	logUtils::log_str("========2=========");
}



$user_info = es_session::get('user_info');
logUtils::log_obj($user_info);

if($user_info)
{
	$request_param['uid']= intval($user_info["id"]);
	$request_param['pwd']=$user_info["user_pwd"];
	$request_param['email']=$user_info["user_name"];
}

//如果用户已经登陆,再点：登陆按钮时,则直接转到会员中心界面
if($class =='login' && $request_param['uid'] > 0){
	//logUtils::log_obj($request_param);
	app_redirect(url('index','user_center'));
}


//logUtils::log_obj($request_param);

if($request_param['post_type']!='json'){
	
	$request_param['act']=$class;
	$request_param['r_type']=0;
	$request_param['i_type']=1;
	$request_param['from']='wap';
	
	
	//echo $request_url."<br>";
	//print_r($request_param);
	$request_data=$GLOBALS['transport']->request($request_url,$request_param);
	

	$data=$request_data['body'];
	//print_r($data);exit;
	$data=json_decode(base64_decode($data),1);
	
	if ($request_param['is_debug'] == 1){
		print_r($data);exit;
	}
	//echo "<br>=========================<br>";
	//print_r($data);exit;
	 //判断是否需要登陆
	if(isset($data['user_login_status']) && $data['user_login_status'] == 0 && $class != "pwd" && $class != "login" && $class !='register' && $class !='register_verify_code') {
	
		//接口需要求登陆,并且未登陆时,提示用户登陆;
		//es_session::delete("uid");
		//es_session::delete("user_email");
		//es_session::delete("user_pwd");

		/*
			es_cookie::delete("user_name");
			es_cookie::delete("user_pwd");
			es_session::delete("user_info");
			
			showSuccess('请先登陆2!',0,url('index','login#index'));
		*/
	
	}
	
	
	
	//$domain = app_conf("PUBLIC_DOMAIN_ROOT")==''?get_domain().APP_ROOT:app_conf("PUBLIC_DOMAIN_ROOT");
	//echo $domain;exit;
	
	if(isset($data['page']) && is_array($data['page']) && $data['page']['page_total'] > 1){
		//感觉这个分页有问题,查询条件处理;分页数10,需要与sjmpai同步,是否要将分页处理移到sjmapi中?或换成下拉加载的方式,这样就不要用到分页了		
		$page = new Page($data['page']['page_total'],$data['page']['page_size']);   //初始化分页对象 	
		//$page->parameter
		$p  =  $page->show();
		//print_r($p);exit;
		$GLOBALS['tmpl']->assign('pages',$p);
	}
	
	if($class=='pay_order'){
		//在支付界面时,清空购买车,但如果清空了,用户点：返回 后，再去购买时,会购买空商品，这个需要注意处理一下
		$session_cart_data=es_session::get("cart_data");
		unset($session_cart_data);
		es_session::set("cart_data",$session_cart_data);
		es_session::set("cart_data",array());
		es_session::delete("cart_data");
	
	}
		
	//echo $tmpl_dir; exit;
	//print_r($request_param);exit;
	$GLOBALS['tmpl']->assign('request',$request_param);
	$GLOBALS['tmpl']->assign('is_ajax',intval($request_param['is_ajax']));
	$GLOBALS['tmpl']->assign('data',$data);
	$GLOBALS['tmpl']->assign('WAP_ROOT',APP_ROOT);
	$WAP_ROOT = str_replace('/wap', '', APP_ROOT);
	$GLOBALS['tmpl']->assign('APP_ROOT', $WAP_ROOT);
	
	if (es_session::get('user_info')){
		$GLOBALS['tmpl']->assign('is_login',1);//用户已登陆
	}else{
		$GLOBALS['tmpl']->assign('is_login',0);//用户未登陆
	}
	

	//==============================
	//判断是否有缓存
	//echo $tmpl_dir; exit;
	//生成缓存的ID
	
	//$cache_id  = md5($class.$act2.trim($request_param['id']).$city_id);	
	//if (!$GLOBALS['tmpl']->is_cached($tmpl_dir, $cache_id)){}
	//echo $tmpl_dir; exit;
	$GLOBALS['tmpl']->display($tmpl_dir);
}else{
	$request_param['from']='wap';
	$request_param['act']=$class;
	//$request_param['i_type']=2;
	//$request_param['r_type']=0;
	
	$postData = array();
	$postData['i_type']=0;
	$postData['r_type']=0;	 
	$postData['requestData'] = base64_encode(json_encode($request_param));
	
	$request_data=$GLOBALS['transport']->request($request_url,$postData);
	$data=$request_data['body'];
	
	//@eval("\$data = ".$data.';');
	$data=base64_decode($data);

	
	if($class=='register' || $class=='register_verify_code'){
		$i=json_decode($data);
 		if($i->response_code==1){
 			/*
			//将会员信息存在session中
			es_session::set('uid',$i->uid);			
			es_session::set('user_name',$i->user_name);			
			es_session::set('user_pwd',$i->user_pwd);
			*/
 			
 			//logUtils::log_obj($i);
 			
 			es_session::delete("user_info");
			es_cookie::set("user_name",$i->user_name,3600*24*30);
			es_cookie::set("user_pwd",md5($i->user_pwd."_EASE_COOKIE"),3600*24*30);
		}
	}
	if($class=='pwd'){
		$i=json_decode($data);
		if($i->response_code==1){
			//es_session::set('user_pwd',$request_param['newpassword']);
			es_session::delete("user_info");
			es_cookie::set("user_pwd",md5($i->user_pwd."_EASE_COOKIE"),3600*24*30);
		}
	}
	
	if($class=='login'){
		$i=json_decode($data);
 		if($i->response_code==1){
 			/*
			//将会员信息存在session中			
 			es_session::set('uid',$i->uid);
			es_session::set('user_name',$i->user_name);
			es_session::set('user_pwd',$request_param['pwd']);
			//cookie
			es_cookie::set('uid',$i->uid,3600*24*365);
			es_cookie::set('user_name',$i->user_name,3600*24*365);
			es_cookie::set('user_pwd',$request_param['pwd'],3600*24*365);
			*/
 			
 			logUtils::log_obj($i);
 			es_session::delete("user_info");
			es_cookie::set("user_name",$i->user_name,3600*24*30);
			es_cookie::set("user_pwd",md5($i->user_pwd."_EASE_COOKIE"),3600*24*30);
		}
	}
	
	echo $data;
}

?>