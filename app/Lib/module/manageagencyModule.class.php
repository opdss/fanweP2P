<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class manageagencyModule extends SiteBaseModule
{
	static private function checkLogin(){
		$manageagency_info  = es_session::get("manageagency_info");
		if(!$manageagency_info){
			app_redirect(url("index","manageagency#login"));
			die();
		}
		$manageagency_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user WHERE id=".$manageagency_info['id']);
		return $manageagency_info;
	}
	public function register()
	{		
		$GLOBALS['tmpl']->caching = true;
		$cache_id  = md5(MODULE_NAME.ACTION_NAME);		
		if (!$GLOBALS['tmpl']->is_cached('manageagency/manageagency_register.html', $cache_id))	
		{
			$GLOBALS['tmpl']->assign("page_title","担保机构注册");
			
			$GLOBALS['tmpl']->assign("agreement",app_conf("AGREEMENT"));
			$GLOBALS['tmpl']->assign("privacy",app_conf("PRIVACY"));
			
		}
		$GLOBALS['tmpl']->display("manageagency/manageagency_register.html",$cache_id);
	}
	
	public function doregister()
	{
		$verify = md5(trim($_REQUEST['verify']));
		$session_verify = es_session::get('verify');
		if($verify!=$session_verify)
		{				
			showErr($GLOBALS['lang']['VERIFY_CODE_ERROR'],0,url("shop","manageagency#register"));
		}
		
		$user_data = $_POST;
		require_once APP_ROOT_PATH."system/libs/user.php";
		if(!$user_data){
			 app_redirect("404.html");
			 exit();
		}
		
		foreach($user_data as $k=>$v)
		{
			$user_data[$k] = htmlspecialchars(addslashes($v));
		}
		
		if(trim($user_data['user_pwd'])!=trim($user_data['user_pwd_confirm']))
		{
			showErr($GLOBALS['lang']['USER_PWD_CONFIRM_ERROR']);
		}
		if(trim($user_data['user_pwd'])=='')
		{
			showErr($GLOBALS['lang']['USER_PWD_ERROR']);
		}
		
		$pid = $GLOBALS['db']->getOne("SELECT id FROM ".DB_PREFIX."user WHERE mobile ='".$user_data['user_name']."' OR `name`='".$user_data['user_name']."'");
		
		if($pid['id'] > 0){
			showErr(sprintf($GLOBALS['lang']['EXIST_ERROR_TIP'],$GLOBALS['lang']['USER_TITLE_UNIT_NAME']));
			//会员已经存在
		}
		
		$user_data["user_type"] = 2;
		
		$res = save_user($user_data);
		if($res['status'] == 1)
		{
			$user_id = intval($res['data']);
			//更新来路
			$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$user_id);
			if($user_info['is_effect']==0)
			{
				//在此自动登录
				$result = do_login_user($user_data['user_name'],$user_data['user_pwd']);
				$GLOBALS['tmpl']->assign('integrate_result',$result['msg']);
				app_redirect(url("index","manageagency#steptwo"));
			}
			else{
				showSuccess($GLOBALS['lang']['WAIT_VERIFY_USER'],0,APP_ROOT."/");
			}
		}
		else
		{
			$error = $res['data'];		
			if(!$error['field_show_name'])
			{
					$error['field_show_name'] = $GLOBALS['lang']['USER_TITLE_'.strtoupper($error['field_name'])];
			}
			if($error['error']==EMPTY_ERROR)
			{
				$error_msg = sprintf($GLOBALS['lang']['EMPTY_ERROR_TIP'],$error['field_show_name']);
			}
			if($error['error']==FORMAT_ERROR)
			{
				$error_msg = sprintf($GLOBALS['lang']['FORMAT_ERROR_TIP'],$error['field_show_name']);
			}
			if($error['error']==EXIST_ERROR)
			{
				$error_msg = sprintf($GLOBALS['lang']['EXIST_ERROR_TIP'],$error['field_show_name']);
			}
			showErr($error_msg);
		}
	}
	
	public function login()
	{
		$login_info = es_session::get("manageagency_info");
		if($login_info)
		{
			app_redirect(url("index","manageagency#account"));
		}
				
		$GLOBALS['tmpl']->caching = true;
		$cache_id  = md5(MODULE_NAME.ACTION_NAME.$GLOBALS['deal_city']['id']);		
		if (!$GLOBALS['tmpl']->is_cached('manageagency/manageagency_login.html', $cache_id))	
		{
			$GLOBALS['tmpl']->assign("page_title","担保机构登陆");
			$GLOBALS['tmpl']->assign("CREATE_TIP",$GLOBALS['lang']['REGISTER']);
			 
		}
		$GLOBALS['tmpl']->display("manageagency/manageagency_login.html",$cache_id);
	}
	
	public function steptwo(){
		$GLOBALS['manageagency_info'] = es_session::get("manageagency_info");
		//print_r($GLOBALS['manageagency_info']);die;
		if(intval($GLOBALS['manageagency_info']['id'])==0)
		{
			es_session::set('before_login',$_SERVER['REQUEST_URI']);
			app_redirect(url("shop","manageagency#login"));
		}
		$GLOBALS['tmpl']->display("manageagency/manageagency_step_two.html");
		exit;
	}
	
	public function dologin()
	{
		if(!$_POST)
		{
			 app_redirect("404.html");
			 exit();
		}
		foreach($_POST as $k=>$v)
		{
			$_POST[$k] = htmlspecialchars(addslashes($v));
		}
		$ajax = intval($_REQUEST['ajax']);
		if(!check_hash_key()){
			showErr("非法请求!",$ajax);
		}
		$_POST['user_pwd'] = trim(FW_DESPWD($_POST['user_pwd']));
		
		require_once APP_ROOT_PATH."system/libs/user.php";
		if(check_ipop_limit(CLIENT_IP,"user_dologin",intval(app_conf("SUBMIT_DELAY")))){
			$result = do_login_user($_POST['email'],$_POST['user_pwd']);
		}
		else
			showErr($GLOBALS['lang']['SUBMIT_TOO_FAST'],$ajax,url("shop","manageagency#login"));
		if($result['status'])
		{	
			$s_user_info = es_session::get("user_info");

			{	
				$jump_url = url("index","manageagency#account");
				$s_user_info = es_session::get("manageagency_info");

					if($ajax==1)
					{
						$return['status'] = 1;
						$return['info'] = $GLOBALS['lang']['LOGIN_SUCCESS'];
						$return['data'] = $result['msg'];
						$return['jump'] = $jump_url;
						ajax_return($return);
					}
					else
					{
						$GLOBALS['tmpl']->assign('integrate_result',$result['msg']);					
						showSuccess($GLOBALS['lang']['LOGIN_SUCCESS'],$ajax,$jump_url);
					}

			}
			
		}
		else
		{
			if($result['data'] == ACCOUNT_NO_EXIST_ERROR)
			{
				$err = $GLOBALS['lang']['USER_NOT_EXIST'];
			}
			if($result['data'] == ACCOUNT_PASSWORD_ERROR)
			{
				$err = $GLOBALS['lang']['PASSWORD_ERROR'];
			}
			if($result['data'] == ACCOUNT_NO_VERIFY_ERROR)
			{
				$err = $GLOBALS['lang']['USER_NOT_VERIFY'];
				if(app_conf("MAIL_ON")==1&&$ajax==0)
				{				
					$GLOBALS['tmpl']->assign("page_title",$err);
					$GLOBALS['tmpl']->assign("user_info",$result['user']);
					$GLOBALS['tmpl']->display("verify_user.html");
					exit;
				}
				
			}
			showErr($err,$ajax);
		}
	}
	
	
	public function loginout()
	{
		require_once APP_ROOT_PATH."system/libs/user.php";
		$result = loginout_manageagency();
		if($result['status'])
		{
			$s_user_info = es_session::get("user_info");
			es_cookie::delete("user_name");
			es_cookie::delete("user_pwd");
			$GLOBALS['tmpl']->assign('integrate_result',$result['msg']);
			$before_loginout = $_SERVER['HTTP_REFERER']?$_SERVER['HTTP_REFERER']:url("index");
			if(trim(app_conf("INTEGRATE_CODE"))=='')
			{
				app_redirect($before_loginout);
			}
			else
			showSuccess($GLOBALS['lang']['LOGINOUT_SUCCESS'],0,$before_loginout);
		}
		else
		{
			app_redirect(url("index"));		
		}
	}
	
	public function getpassword()
	{
		$GLOBALS['tmpl']->caching = true;
		$cache_id  = md5(MODULE_NAME.ACTION_NAME.$GLOBALS['deal_city']['id']);		
		if (!$GLOBALS['tmpl']->is_cached('manageagency/manageagency_get_password.html', $cache_id))	
		{
			 
			$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['GET_PASSWORD_BACK']);
		}
		$GLOBALS['tmpl']->display("manageagency/manageagency_get_password.html",$cache_id);
	}
	
	public function send_password()
	{
		$email = addslashes(trim($_REQUEST['email']));
		$user_pwd = strim($_REQUEST['pwd_m']);
		$sms_code =strim($_REQUEST['sms_codes']);
		
		if(!check_email($email))
		{
			showErr($GLOBALS['lang']['MAIL_FORMAT_ERROR']); //没输入邮件
		}

		if($GLOBALS['db']->getAll("select count(*) from ".DB_PREFIX."user where email ='".$email."'") == 0)
		{
			showErr($GLOBALS['lang']['NO_THIS_MAIL']);  //无此邮箱用户
		}
		if($sms_code==""){
			showErr("请输入邮箱验证码",1);
		}
		
		if($user_pwd==""){
			showErr("请输入密码",1);
		}
		
		$yanzheng = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."email_verify_code WHERE email='".$email."' AND verify_code='".$sms_code."'");
		if(!$yanzheng){
			showErr("邮箱验证码出错",1);
		}
		
		if($user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where email='".$email."'"))
		{
			$result = 1;  //初始为1
			
			if($result>0)
			{
				$user_info_m['user_pwd'] = md5($user_pwd);
				$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user_info_m,"UPDATE","id=".$user_info['id']);
			}
				
			showSuccess($GLOBALS['lang']['MOBILE_SUCCESS'],1);//密码修改成功
				
		}
		else{
			showErr("邮箱账户不存在",1);  //没有该手机账户
		}
		
	}
	
	public function phone_send_password()
	{	
		$mobile = strim($_REQUEST['mobile']);
		$user_pwd = strim($_REQUEST['pwd_m']);
		$sms_code=strim($_POST['sms_code']);
		
		if(!$mobile)
		{
			showErr($GLOBALS['lang']['MOBILE_FORMAT_ERROR']); //手机格式错误
		}
		
		if($sms_code==""){
			showErr("请输入手机验证码",1);
		}
		
		if($user_pwd==""){
			showErr("请输入密码",1);
		}
		
		
		//判断验证码是否正确
		if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."mobile_verify_code WHERE mobile='".$mobile."' AND verify_code='".$sms_code."'")==0){
			showErr("手机验证码出错",1);
		}
			
	
		if($user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where mobile ='".$mobile."'"))
		{
			$result = 1;  //初始为1

			if($result>0)
			{
				$user_info_m['user_pwd'] = md5($user_pwd);
				$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user_info_m,"UPDATE","id=".$user_info['id']);
			}
			
			showSuccess($GLOBALS['lang']['MOBILE_SUCCESS'],1);//密码修改成功
			
		}
		else{
			showErr($GLOBALS['lang']['NO_THIS_MOBILE'],1);  //没有该手机账户
		}
		
		
		
	}
	
	public function modify_password()
	{
		 
		$id = intval($_REQUEST['id']);
		$user_info  = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$id);
		if(!$user_info)
		{
			showErr($GLOBALS['lang']['NO_THIS_USER']);
		}
		$verify = $_REQUEST['code'];
		if($user_info['code'] == $verify&&$user_info['code']!='')
		{
			//成功	
			$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['SET_NEW_PASSWORD']);				
			$GLOBALS['tmpl']->assign("user_info",$user_info);
			$GLOBALS['tmpl']->display("manageagency/manageagency_modify_password.html");
		}
		else
		{
			showErr($GLOBALS['lang']['VERIFY_FAILED'],0,APP_ROOT."/");
		}	
	}
	
	public function do_modify_password()
	{
		$id = intval($_REQUEST['id']);
		$user_info  = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$id);
		if(!$user_info)
		{
			showErr($GLOBALS['lang']['NO_THIS_USER']);
		}
		$verify = $_REQUEST['code'];
		if($user_info['code'] == $verify&&$user_info['code']!='')
		{
			if(trim($_REQUEST['user_pwd'])!=trim($_REQUEST['user_pwd_confirm']) || trim($_REQUEST['user_pwd'])=="")
			{
				showErr($GLOBALS['lang']['PASSWORD_VERIFY_FAILED']);
			}
			else
			{			
				$password = addslashes(trim($_REQUEST['user_pwd']));
				$user_info['user_pwd'] = $password;
				$password = md5($password.$user_info['code']);
				$result = 1;  //初始为1
				//载入会员整合
				$integrate_code = trim(app_conf("INTEGRATE_CODE"));
				if($integrate_code!='')
				{
					$integrate_file = APP_ROOT_PATH."system/integrate/".$integrate_code."_integrate.php";
					if(file_exists($integrate_file))
					{
						require_once $integrate_file;
						$integrate_class = $integrate_code."_integrate";
						$integrate_obj = new $integrate_class;
					}	
				}
				
				if($integrate_obj)
				{
					$result = $integrate_obj->edit_user($user_info,$user_info['user_pwd']);				
				}
				if($result>0)
				{
					$GLOBALS['db']->query("update ".DB_PREFIX."user set user_pwd = '".$password."',password_verify='' where id = ".$user_info['id'] );
					showSuccess($GLOBALS['lang']['NEW_PWD_SET_SUCCESS'],0,APP_ROOT."/");
				}
				else
				{
					showErr($GLOBALS['lang']['NEW_PWD_SET_FAILED']);
				}
			}
			//成功	
			$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['SET_NEW_PASSWORD']);				
			$GLOBALS['tmpl']->assign("user_info",$user_info);
			$GLOBALS['tmpl']->display("user_modify_password.html");
		}
		else
		{
			showErr($GLOBALS['lang']['VERIFY_FAILED'],0,APP_ROOT."/");
		}	
	}
	
	public function bidverify()
	{
		$user_info= $GLOBALS['user_info'];
		if(!$user_info)
		{
			showErr($GLOBALS['lang']['NO_THIS_USER']);
		}
		$verify = addslashes(trim($_REQUEST['code']));
		if($user_info['verify']!=''&&$user_info['verify'] == $verify)
		{
			//成功
			es_session::set("user_info",$user_info);
			$GLOBALS['db']->query("update ".DB_PREFIX."user set login_ip = '".CLIENT_IP."',login_time= ".TIME_UTC.",verify = '',emailpassed = 1,email=tmp_email,is_effect = 1 where id =".$user_info['id']);
			$GLOBALS['db']->query("update ".DB_PREFIX."mail_list set is_effect = 1,mail_address='".$user_info['tmp_email']."' where mail_address ='".$user_info['email']."'");	
			$GLOBALS['db']->query("update ".DB_PREFIX."user set tmp_email = '' where id =".$user_info['id']);								
			showSuccess($GLOBALS['lang']['VERIFY_SUCCESS'],0,get_gopreview());
		}
		elseif($user_info['verify']=='')
		{
			showErr($GLOBALS['lang']['HAS_VERIFIED'],1);
		}
		else
		{
			showErr($GLOBALS['lang']['VERIFY_FAILED'],1);
		}
	}
	
	public function api_create()
	{
		$s_api_user_info = es_session::get("api_user_info");
		if($s_api_user_info)
		{
			if($s_api_user_info['field'])
			{
				$module = str_replace("_id","",$s_api_user_info['field']);
				$module = strtoupper(substr($module,0,1)).substr($module,1);
				require_once APP_ROOT_PATH."system/api_login/".$module."_api.php";
				$class = $module."_api";
				$obj = new $class();
				$obj->create_user();
				app_redirect(APP_ROOT."/");
				exit;
			}			
			showErr($GLOBALS['lang']['INVALID_VISIT']);
		}
		else
		{
			showErr($GLOBALS['lang']['INVALID_VISIT']);
		}
	}
	
	public function do_re_name_id()
	{
		$GLOBALS['manageagency_info'] = es_session::get("manageagency_info");
		$id= $GLOBALS['manageagency_info']['id'];
		$real_name = strim($_REQUEST['real_name']);
		$idno = strim($_REQUEST['idno']);

		
		if(!$id)
		{
			showErr("该用户尚未登陆"); 
		}
		
		if(!$real_name)
		{
			showErr("请输入真实姓名"); //姓名格式错误
		}
		
		if(strlen($idno) == 15 || strlen($idno) ==18){}
			else {
			showErr("身份证格式错误");
			}
		
		if($idno==""){
			showErr("请输入身份证号",1);
		}
	
	
		//判断该实名是否存在
		if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user where idno = '.$idno.' and id<> $id ") > 0 ){
			showErr("该实名已被其他用户认证，非本人请联系客服");
		}
		
		if($GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id =".$id))
		{	
			$user_info_re = array();
			$user_info_re['id'] = $id;
			$user_info_re['real_name'] = $real_name;
			$user_info_re['idno'] = $idno;
			//$user_info_re['sex'] = $sex;
			//$user_info_re['byear'] = $byear;
			//$user_info_re['bmonth'] = $bmonth;
			//$user_info_re['bday'] = $bday;
			$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user_info_re,"UPDATE","id=".$id);
			
			showSuccess("注册成功",0,url("index","manageagency#account"));
		}
		else{
			showErr("该用户尚未注册");  //尚未注册
		}
		
	}
	
	/**
	 * 账户资料
	 */
	function account(){
		$manageagency_info = $this->checkLogin();
		$GLOBALS['tmpl']->assign('manageagency_info',$manageagency_info);
		$GLOBALS['tmpl']->assign("inc_file","manageagency/account.html");
		$GLOBALS['tmpl']->display("manageagency/m.html");
	}
	
	/**
	 * 保存账户资料
	 */
	function accountsave(){
		$manageagency_info = $this->checkLogin();
		
		$data['brief'] = strim($_REQUEST["brief"]);
		$data['address'] = strim($_REQUEST["address"]);
		$data['header'] = replace_public(btrim($_REQUEST["header"]));
		$data['company_brief'] = strim($_REQUEST["company_brief"]);
		$data['history'] = replace_public(btrim($_REQUEST["history"]));
		$data['content'] = replace_public(btrim($_REQUEST["content"]));
		
		$GLOBALS['db']->autoExecute(DB_PREFIX."user",$data,"UPDATE","id=".$manageagency_info['id']);
		
		$agency_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user  where id=".$manageagency_info['id']);
		
		es_session::set("manageagency_info",$agency_info);
		
	 	showSuccess("操作成功",url("index","manageagency#account"));
	}
	
	/**
	 * 担保的标
	 */
	 function agencydeal(){
	 	//输出投标列表
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("DEAL_PAGE_SIZE")).",".app_conf("DEAL_PAGE_SIZE");
		
		require APP_ROOT_PATH."app/Lib/deal_func.php";
		require APP_ROOT_PATH."app/Lib/page.php";
		
		$manageagency_info  = $this->checkLogin();
		
		$condition = " agency_id = ".$manageagency_info['id']." and agency_status = 0 ";
		
		$result = get_deal_list($limit,0, $condition,'','','',true);
		
		$GLOBALS['tmpl']->assign("list",$result['list']);
		
		$page = new Page($result['count'],app_conf("DEAL_PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		$GLOBALS['tmpl']->assign("page_title","担保中的借款");
		
		$GLOBALS['tmpl']->assign("inc_file","manageagency/agencydeal.html");
		$GLOBALS['tmpl']->display("manageagency/m.html");
	 }
	 
	 /**
	  * 邀约中的担保的标
	  */
	 function agencydealing(){
	 	//输出投标列表
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("DEAL_PAGE_SIZE")).",".app_conf("DEAL_PAGE_SIZE");
		
		require APP_ROOT_PATH."app/Lib/deal_func.php";
		require APP_ROOT_PATH."app/Lib/page.php";
		
		$manageagency_info  = $this->checkLogin();
		
		$condition = " agency_id = ".$manageagency_info['id']." and agency_status = 1 ";
		
		$result = get_deal_list($limit,0, $condition,'','','',true);
		
		$GLOBALS['tmpl']->assign("list",$result['list']);
		
		$page = new Page($result['count'],app_conf("DEAL_PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		$GLOBALS['tmpl']->assign("page_title","邀约中的担保");
		
		$GLOBALS['tmpl']->assign("inc_file","manageagency/agencydealing.html");
		$GLOBALS['tmpl']->display("manageagency/m.html");
	 }
	 /**
	  * 凭证管理开始
	  */
	 function pingzheng(){
	 	$manageagency_info  = $this->checkLogin();
	 	
	 	//输出投标列表
	 	$page = intval($_REQUEST['p']);
	 	if($page==0)
	 		$page = 1;
	 	$limit = (($page-1)*app_conf("DEAL_PAGE_SIZE")).",".app_conf("DEAL_PAGE_SIZE");
	 
		require APP_ROOT_PATH."app/Lib/page.php";
	 
	 
	 	$agency_id = intval($manageagency_info['id']); // $agency_id;
	 	$view_info = $GLOBALS['db']->getOne("SELECT view_info FROM ".DB_PREFIX."user  where id=".$agency_id);
	 	$result = unserialize( $view_info);
	 	$GLOBALS['tmpl']->assign("list",$result);
	 
	 	
	 	$page = new Page($result['count'],app_conf("DEAL_PAGE_SIZE"));   //初始化分页对象
	 	$p  =  $page->show();
	 	$GLOBALS['tmpl']->assign('pages',$p);
	 
	 	$GLOBALS['tmpl']->assign("page_title","凭证管理");
	 
	 	$GLOBALS['tmpl']->assign("inc_file","manageagency/agency_pingzheng.html");
	 	$GLOBALS['tmpl']->display("manageagency/m.html");
	 }
	 function adds_pingzheng()
	 {
	 	$manageagency_info  = $this->checkLogin();
	 	$GLOBALS['tmpl']->display("manageagency/add_agency_pingzheng.html");
	 }
	 
	 function add_pingzheng()
	 {
	 	$manageagency_info  = $this->checkLogin();
	 	$view_down_data = array();
	 	foreach($_FILES['img_data']['name'] as $k=>$v){
	 		$file = pathinfo($v);
	 			
	 		if($file['error'] == 0){
	 			if(!file_exists(APP_ROOT_PATH."/public/view_info"))
	 				@mkdir(APP_ROOT_PATH."/public/view_info",0777);
	 				
	 			$time = to_date(TIME_UTC,"Ym");
	 			if(!file_exists(APP_ROOT_PATH."/public/view_info/".$time))
	 				@mkdir(APP_ROOT_PATH."/public/view_info/".$time,0777);
	 				
	 			$file_name = md5(TIME_UTC.$_REQUEST['id'].$v.$k).".".$file['extension'];
	 	
	 			move_uploaded_file($_FILES['img_data']['tmp_name'][$k],APP_ROOT_PATH."/public/view_info/".$time."/".$file_name);
	 	
	 			if(file_exists(APP_ROOT_PATH."/public/view_info/".$time."/".$file_name)){
	 				$view_down_data[$k]['img'] = "./public/view_info/".$time."/".$file_name;
	 				$view_down_data[$k]['name'] = strim($_REQUEST['file_name'][$k]);
	 			}
	 		}
	 	}
	 	$agency_id = intval($manageagency_info['id']); // $agency_id;
	 	$new_view_info_arr= array();
	 	
	 	$old_view_infos =  $GLOBALS['db']->getOne("SELECT view_info FROM ".DB_PREFIX."user  where id=".$agency_id);
	 	
	 	if($old_view_infos !=""){
	 		$old_view_info_arr = unserialize($old_view_infos);
	 			
	 		foreach($old_view_info_arr as $k=>$v){
	 			$new_view_info_arr[$k] = $v;
	 		}
	 	}
	 	
	 	foreach($view_down_data as $k=>$v){
	 		$new_view_info_arr[] = $v;
	 	}
	 	
	 	$data['view_info'] = serialize($new_view_info_arr);

	 	if($GLOBALS['db']->autoExecute(DB_PREFIX."user",$data,"UPDATE","id=".$agency_id)){
	 		showSuccess('上传资料成功',0,url("index","manageagency#pingzheng"));
	 	}
	 	else{
	 		showError('上传资料失败',0,url("index","manageagency#pingzheng"));
	 	}
	 }
	 
	 function delete_pingzheng()
	 {
	 	$manageagency_info  = $this->checkLogin();
	 	$result['status'] = 0;
	 	$view_info_id =trim($_REQUEST['view_info_id']);
	 	$agency_id = intval($manageagency_info['id']); // $agency_id;
	 	$view_info = $GLOBALS['db']->getOne("SELECT view_info FROM ".DB_PREFIX."user  where id=".$agency_id);
	 	$view_infos = unserialize( $view_info);
	 	
	 	foreach($view_infos as $k=>$v){
	 		if($k == $view_info_id){
	 			@unlink(APP_ROOT_PATH.$v['img']);
	 			unset($view_infos[$k]);
	 		}
	 	}
	 	$resulttt['view_info'] = serialize($view_infos);
	 	
	 	if($GLOBALS['db']->autoExecute(DB_PREFIX."user",$resulttt,"UPDATE","id=".$agency_id)){
	 		$result['info']="删除成功！";
	 		$result['status'] = 1;
	 	}
	 	else{
	 		$result['info']="删除失败！";
	 	}

	 	ajax_return($result);
	 }

	 function manage_agency_status(){
	 	$manageagency_info  = $this->checkLogin();
	 	
	 	$id = intval($_REQUEST['id']);
	 	require APP_ROOT_PATH."app/Lib/deal_func.php";
	 	
	 	$deal = get_deal($id);
	 	
	 	if(!$deal){
	 		showErr("标不存在");
	 	}
	 	
	 	if($deal['agency_id'] !=  $manageagency_info['id']){
	 		showErr("与此标无关联");
	 	}
	 	
	 	
	 	$GLOBALS['tmpl']->assign("deal",$deal);
	 	$GLOBALS['tmpl']->display("manageagency/agency_status.html");
	 }
	 
	 function agency_status(){
	 	$manageagency_info  = $this->checkLogin();
	 	$id = intval($_REQUEST['id']);
	 	require APP_ROOT_PATH."app/Lib/deal_func.php";
	 	
	 	$deal = get_deal($id);
	 	
	 	if(!$deal){
	 		showErr("标不存在",1);
	 	}
	 	
	 	if($deal['agency_id'] !=  $manageagency_info['id']){
	 		showErr("与此标无关联",1);
	 	}
	 	
	 	$data['agency_status'] = intval($_REQUEST['status']);
	 	if($data['agency_status']==2){
	 		$data['guarantor_margin_amt'] = 0;
	 		$data['guarantor_amt'] = 0;
	 		$data['guarantor_pro_fit_amt'] = 0;
	 	}
	 	
	 	$GLOBALS['db']->autoExecute(DB_PREFIX."deal",$data,"UPDATE","id=".$id);
	 	if($GLOBALS['db']->affected_rows())
	 		showSuccess("操作成功",1);
	 	else
	 		showErr("操作失败",1);
	 }
	 
	 public function security(){
		$manageagency_info = $this->checkLogin();
		//if($manageagency_info['id'] == 0)
		//{
		//	$manageagency_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user WHERE id=".$manageagency_info['id']." ");
		//}
		$GLOBALS['tmpl']->assign('manageagency_info',$manageagency_info);

		$GLOBALS['tmpl']->assign("inc_file","manageagency/agency_security.html");
		$GLOBALS['tmpl']->display("manageagency/m.html");
		
	}
	public function save_ips()
	{
		$GLOBALS['manageagency_info'] = es_session::get("manageagency_info");
		$id= $GLOBALS['manageagency_info']['id'];
		
		$ips_acct_no = intval($_REQUEST['ips_acct_no']);

		if(!$id)
		{
			$data['status'] = 0;
			$data['info'] = "该用户尚未登陆";
			ajax_return($data);
		}

		if($ips_acct_no=="" ){
			$data['status'] = 0;
			$data['info'] = "请输入IPS个人账户";
			ajax_return($data);
		}
		
	
		//判断该实名是否存在
		if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user where ips_acct_no = '.$ips_acct_no.' and id<> $id ") > 0 ){
			$data['status'] = 0;
			$data['info'] = "该IPS账户已被其他用户绑定，非本人请联系客服";
			ajax_return($data);
		}
				
		if($GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id =".$id))
		{	
			$user_info_re = array();
			$user_info_re['id'] = $id;
			$user_info_re["ips_acct_no"] = $ips_acct_no;
			
			$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user_info_re,"UPDATE","id=".$id);
			
			$return['status'] = 1;

			$return['info'] = "提交成功";

			ajax_return($return);
		}
		else{
			$data['status'] = 0;
			$data['info'] = "该用户尚未注册";
			ajax_return($data);
		}
		
	}
	public function save_idsno()
	{
		$GLOBALS['manageagency_info'] = es_session::get("manageagency_info");
		$id= $GLOBALS['manageagency_info']['id'];
		
		$acct_type = intval($_REQUEST['acct_type']);
		
		//if($acct_type==1)
		//{
			$real_name = strim($_REQUEST['real_name']);
			$idno = strim($_REQUEST['idno']);
		//}
		/*else
		{
			$ips_mer_code = strim($_REQUEST['ips_mer_code']);
			$ips_acct_no = strim($_REQUEST['ips_acct_no']);
		}*/
		
		$ajax = intval($_REQUEST['ajax']);
		//$sex = strim($_REQUEST['sex']);
		//$byear = strim($_REQUEST['byear']);
		//$bmonth = strim($_REQUEST['bmonth']);
		//$bday = strim($_REQUEST['bday']);
		
		if(!$id)
		{
			$data['status'] = 0;
			$data['info'] = "该用户尚未登陆";
			ajax_return($data);
		}
		
		if(!$real_name&&$acct_type==1)
		{
			$data['status'] = 0;
			$data['info'] = "请输入真实姓名";
			ajax_return($data);
		}
		
		if(strlen($idno) != 15 && strlen($idno) !=18 ){
			$data['status'] = 0;
			$data['info'] = "身份证格式错误";
			ajax_return($data);
		}
		
		if($idno=="" ){
			$data['status'] = 0;
			$data['info'] = "请输入身份证号";
			ajax_return($data);
		}
		/*
		if($ips_mer_code=="" &&$acct_type==0)
		{
			$data['status'] = 0;
			$data['info'] = "由IPS颁发的商户号不能为空";
			ajax_return($data);
		}
		
		if($ips_acct_no=="" &&$acct_type==0)
		{
			$data['status'] = 0;
			$data['info'] = "IPS个人帐户不能为空";
			ajax_return($data);
		}*/
		
	
	
		//判断该实名是否存在
		if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user where idno = '.$idno.' and id<> $id ") > 0 ){
			$data['status'] = 0;
			$data['info'] = "该实名已被其他用户认证，非本人请联系客服";
			ajax_return($data);
		}
		/*//判断该实名是否存在
		if(acct_type==0 && $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user where ips_mer_code = '.$ips_mer_code.' and id<> $id ") > 0 ){
			$data['status'] = 0;
			$data['info'] = "该实名已被其他用户认证，非本人请联系客服";
			ajax_return($data);
		}*/
		
		
		
		if($GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id =".$id))
		{	
			$user_info_re = array();
			$user_info_re['id'] = $id;
			$user_info_re["acct_type"] = $acct_type;
			//if($acct_type==1)
			//{
				$user_info_re['real_name'] = $real_name;
				$user_info_re['idno'] = $idno;
			//}
			//else
			//{
			//	$user_info_re['ips_acct_no'] = $ips_acct_no;
			//	$user_info_re['ips_mer_code'] = $ips_mer_code;
			//}
			//$user_info_re['sex'] = $sex;
			//$user_info_re['byear'] = $byear;
			//$user_info_re['bmonth'] = $bmonth;
			//$user_info_re['bday'] = $bday;
			$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user_info_re,"UPDATE","id=".$id);
			
			$return['status'] = 1;
			//$return['info'] = $GLOBALS['lang']['LOGIN_SUCCESS'];
			$return['info'] = "提交成功";
			//$return['jump'] = url("index","manageagency#account");
			ajax_return($return);
		}
		else{
			$data['status'] = 0;
			$data['info'] = "该用户尚未注册";
			ajax_return($data);
		}
		
	}
	public function save_pwd()
	{
		$GLOBALS['manageagency_info']  = $this->checkLogin();
		require_once APP_ROOT_PATH.'system/libs/user.php';
		foreach($_REQUEST as $k=>$v)
		{
			$_REQUEST[$k] = htmlspecialchars(addslashes(trim($v)));
		}
		
		if ($_REQUEST['sta'] == 1){
			$sms_code =trim($_REQUEST['sms_code']);
			$phone = $GLOBALS['manageagency_info']['mobile'];
			$code = $GLOBALS['db']->getOne("SELECT verify_code FROM ".DB_PREFIX."mobile_verify_code where mobile='".$phone."'");
			if($sms_code != $code)
			{
				showErr("验证码输出错误！",intval($_REQUEST['is_ajax']));
			}
		}
		
		$_REQUEST['id'] = intval($GLOBALS['manageagency_info']['id']);
		$res = save_user($_REQUEST,'UPDATE');
		if($res['status'] == 1)
		{
			$s_user_info = es_session::get("manageagency_info");
			$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = '".intval($s_user_info['id'])."'");
			es_session::set("user_info",$user_info);
			if(intval($_REQUEST['is_ajax'])==1)
				showSuccess($GLOBALS['lang']['SUCCESS_TITLE'],1);
			else{
				app_redirect(url("index","manageagency#index"));
			}		
		}
		else
		{
			$error = $res['data'];		
			if(!$error['field_show_name'])
			{
					$error['field_show_name'] = $GLOBALS['lang']['USER_TITLE_'.strtoupper($error['field_name'])];
			}
			if($error['error']==EMPTY_ERROR)
			{
				$error_msg = sprintf($GLOBALS['lang']['EMPTY_ERROR_TIP'],$error['field_show_name']);
			}
			if($error['error']==FORMAT_ERROR)
			{
				$error_msg = sprintf($GLOBALS['lang']['FORMAT_ERROR_TIP'],$error['field_show_name']);
			}
			if($error['error']==EXIST_ERROR)
			{
				$error_msg = sprintf($GLOBALS['lang']['EXIST_ERROR_TIP'],$error['field_show_name']);
			}
			showErr($error_msg,intval($_REQUEST['is_ajax']));
		}
	}
	public function email(){
		$GLOBALS['manageagency_info']  = $this->checkLogin();
		
		if($GLOBALS['manageagency_info']['emailpassed']==1){
			exit();
		}
		
		$remail = get_site_email($GLOBALS['manageagency_info']['id']);
		
		if($remail ==$GLOBALS['manageagency_info']['email']){
			$email="";
		}
		else
			$email=$GLOBALS['manageagency_info']['email'];
		
		$GLOBALS['tmpl']->assign("email",$email);
		$step = intval($_REQUEST['step']);
		$GLOBALS['tmpl']->assign("step",$step);
		$GLOBALS['tmpl']->assign("manageagency_info",$GLOBALS['manageagency_info']);
		$GLOBALS['tmpl']->display("manageagency/email_step.html");
	}
	
	public function mobile(){
		$is_ajax = intval($_REQUEST['is_ajax']);
		$GLOBALS['tmpl']->assign("is_ajax",$is_ajax);
		$GLOBALS['manageagency_info']  = $this->checkLogin();
		$GLOBALS['tmpl']->assign("manageagency_info",$GLOBALS['manageagency_info']);
		if($is_ajax==0){
			$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_MOBILE']);
			$GLOBALS['tmpl']->assign("inc_file","manageagency/mobile_index.html");
			$GLOBALS['tmpl']->display("manageagency/m.html");
		}
		else{
			$GLOBALS['tmpl']->display("manageagency/mobile_index.html");
		}
	}
	public function saveemail(){
		$GLOBALS['manageagency_info']  = $this->checkLogin();
		
		$oemail =  strim($_REQUEST['oemail']);
		$email =  strim($_REQUEST['email']);
		$code = $_REQUEST['code'];
		
		$GLOBALS['manageagency_info']  = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = '".intval($GLOBALS['manageagency_info']['id'])."'");
		
		$remail = get_site_email($GLOBALS['manageagency_info']['id']);
		
		if($GLOBALS['manageagency_info']['email']!="" && $remail !=$GLOBALS['manageagency_info']['email']){
			if($oemail!=$GLOBALS['manageagency_info']['email']){
				$data['status'] = 0;
				$data['info'] = "原邮箱不匹配";
				ajax_return($data);
			}
		}
		if($email!="" && !check_email($email)){
				$data['status'] = 0;
				$data['info'] = "新邮箱格式错误";
				ajax_return($data);
		}
		if($GLOBALS['manageagency_info']['emailpassed']==1){
				$data['status'] = 0;
				$data['info'] = "该账户已绑定认证过邮箱，无法进行此操作";
				ajax_return($data);
		}
		if($code != $GLOBALS['manageagency_info']['verify']){
			$data['status'] = 0;
				$data['info'] = "验证码错误";
				ajax_return($data);
		}
		
		if($email==""){
			$email = $oemail;
		}
		
		$GLOBALS['db']->query("update ".DB_PREFIX."user set email = '".$email."',emailpassed = 1 where id = ".$GLOBALS['manageagency_info']['id']);
		
		$result['status'] = 1;
		$result['info'] = "邮箱绑定成功";
		ajax_return($result);
	}
}
?>