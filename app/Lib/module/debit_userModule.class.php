<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class debit_userModule extends SiteBaseModule
{
	public function register()
	{			
		
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['USER_REGISTER']);
		
		$field_list =load_auto_cache("user_field_list");
		
		$api_uinfo = es_session::get("api_user_info");
		$GLOBALS['tmpl']->assign("reg_name",$api_uinfo['name']);
		
		$GLOBALS['tmpl']->assign("field_list",$field_list);
		
		$GLOBALS['tmpl']->assign("agreement",app_conf("AGREEMENT"));
		$GLOBALS['tmpl']->assign("privacy",app_conf("PRIVACY"));
		$referer = "";
		if(isset($_REQUEST['r'])){
			$referer = strim(base64_decode($_REQUEST['r']));
		}
		$GLOBALS['tmpl']->assign("referer",$referer);
		
		$GLOBALS['tmpl']->display("debit/debit_user_register.html");
	}
	
	public function doregister()
	{
		//注册验证码
		if(intval(app_conf("VERIFY_IMAGE")) == 1 && intval(app_conf("USER_VERIFY")) >= 3){
			$verify = md5(trim($_REQUEST['verify']));
			$session_verify = es_session::get('verify');
			if($verify!=$session_verify)
			{				
				showErr($GLOBALS['lang']['VERIFY_CODE_ERROR'],0,url("shop","user#register"));
			}
		}
		
		require_once APP_ROOT_PATH."system/libs/user.php";
		$user_data = $_POST;
		
		if(!$user_data){
			 app_redirect("404.html");
			 exit();
		}
		foreach($user_data as $k=>$v)
		{
			$user_data[$k] = htmlspecialchars(addslashes($v));
		}
		$user_data["user_type"] = 0;
		
		if(trim($user_data['user_pwd'])!=trim($user_data['user_pwd_confirm']))
		{
			showErr($GLOBALS['lang']['USER_PWD_CONFIRM_ERROR']);
		}
		if(trim($user_data['user_pwd'])=='')
		{
			showErr($GLOBALS['lang']['USER_PWD_ERROR']);
		}
		
		
		/*if(isset($user_data['referer']) && $user_data['referer']!=""){
			$p_user_data = $GLOBALS['db']->getRow("SELECT id,user_type FROM ".DB_PREFIX."user WHERE mobile ='".$user_data['referer']."' OR user_name='".$user_data['referer']."'");

			if($p_user_data["user_type"] == 3)
			{
				$user_data['referer_memo'] = $p_user_data['id'];
				//$user_data['pid'] = $p_user_data['id'];
				$user_data['pid'] = 0;
			}
			elseif($p_user_data["user_type"] < 2)
			{
				$user_data['pid'] = $p_user_data["id"];
				if($user_data['pid'] > 0){
					$refer_count = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user WHERE pid='".$user_data['pid']."' ");
					if($refer_count == 0){
						$user_data['referral_rate'] = (float)trim(app_conf("INVITE_REFERRALS_MIN"));
					}
					elseif((float)trim(app_conf("INVITE_REFERRALS_MIN")) + $refer_count*(float)trim(app_conf("INVITE_REFERRALS_RATE")) > (float)trim(app_conf("INVITE_REFERRALS_MAX"))){
						$user_data['referral_rate'] =(float)trim(app_conf("INVITE_REFERRALS_MAX"));
					}
					else{
						$user_data['referral_rate'] =(float)trim(app_conf("INVITE_REFERRALS_MIN")) + $refer_count*(float)trim(app_conf("INVITE_REFERRALS_RATE"));
					}
						
					
					if(intval(app_conf("REFERRAL_IP_LIMIT")) > 0 && $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user WHERE register_ip ='".CLIENT_IP."' AND pid='".$user_data['pid']."'") > 0){
						$user_data['referral_rate'] = 0;
					}
				}
				else{
					$user_data['pid'] = 0;
				}
			}
		}
		*/
		if(intval($_REQUEST["REGISTER_TYPE"]) != 1 and intval($_REQUEST["REGISTER_TYPE"]) !=2)
		{
			showErr("非法注册");
		}
		//判断是否为手机注册
		if(intval($_REQUEST["REGISTER_TYPE"]) == 1){
			if(strim($user_data['sms_code'])==""){
				showErr("请输入手机验证码");
			}
			//判断验证码是否正确
			if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."mobile_verify_code WHERE mobile='".strim($user_data['mobile'])."' AND verify_code='".strim($user_data['sms_code'])."' AND create_time + ".SMS_EXPIRESPAN." > ".TIME_UTC." ")==0){
				showErr("手机验证码出错,或已过期");
			}
			$user_data['is_effect'] = 1;
			$user_data['mobilepassed'] = 1;
		}
		
		//判断是否为邮箱注册
		if(intval($_REQUEST["REGISTER_TYPE"]) == 2){
			
			if(strim($user_data['emsms_code'])==""){
				showErr("请输入邮箱验证码");
			}
			//判断验证码是否正确
			if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."email_verify_code WHERE email='".strim($user_data['email'])."' AND verify_code='".strim($user_data['emsms_code'])."' AND create_time + ".SMS_EXPIRESPAN." > ".TIME_UTC." ")==0){
				showErr("邮箱验证码出错,或已过期");
			}
			$user_data['is_effect'] = 1;
			$user_data['emailpassed'] = 1;
				
		}
		
		
		$res = save_user($user_data);
		/*if($_REQUEST['subscribe']==1)
		{
			//订阅
			if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."mail_list where mail_address = '".$user_data['email']."'")==0)
			{
				$mail_item['city_id'] = intval($_REQUEST['city_id']);
				$mail_item['mail_address'] = $user_data['email'];
				$mail_item['is_effect'] = app_conf("USER_VERIFY");
				$GLOBALS['db']->autoExecute(DB_PREFIX."mail_list",$mail_item,'INSERT','','SILENT');
			}
			if($user_data['mobile']!=''&&$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."mobile_list where mobile = '".$user_data['mobile']."'")==0)
			{
				$mobile['city_id'] = intval($_REQUEST['city_id']);
				$mobile['mobile'] = $user_data['mobile'];
				$mobile['is_effect'] = app_conf("USER_VERIFY");
				$GLOBALS['db']->autoExecute(DB_PREFIX."mobile_list",$mobile,'INSERT','','SILENT');
			}
			
		}*/
		
		if($res['status'] == 1)
		{
			$user_id = intval($res['data']);
			//更新来路
			$GLOBALS['db']->query("update ".DB_PREFIX."user set referer = '".$GLOBALS['referer']."' where id = ".$user_id);
			$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$user_id);
			if($user_info['is_effect']==1)
			{
				//在此自动登录
				$result = do_login_user($user_data['user_name'],$user_data['user_pwd']);
				$GLOBALS['tmpl']->assign('integrate_result',$result['msg']);
				
				app_redirect(url("debit","debit_uc_center"));
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
		$login_info = es_session::get("user_info");
		if($login_info)
		{
			app_redirect(url("debit","debit_uc_center"));		
		}
				
		$GLOBALS['tmpl']->caching = true;
		$cache_id  = md5(MODULE_NAME.ACTION_NAME.$GLOBALS['deal_city']['id']);		
		if (!$GLOBALS['tmpl']->is_cached('user_login.html', $cache_id))	
		{
			$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['USER_LOGIN']);
			$GLOBALS['tmpl']->assign("CREATE_TIP",$GLOBALS['lang']['REGISTER']);
			 
		}
		$GLOBALS['tmpl']->display("debit/debit_user_login.html",$cache_id);
	}
	/*public function api_login()
	{		
		$s_api_user_info = es_session::get("api_user_info");
		if($s_api_user_info)
		{
			 
			$GLOBALS['tmpl']->assign("page_title",$s_api_user_info['name'].$GLOBALS['lang']['HELLO'].",".$GLOBALS['lang']['USER_LOGIN_BIND']);
			$GLOBALS['tmpl']->assign("CREATE_TIP",$GLOBALS['lang']['REGISTER_BIND']);
			$GLOBALS['tmpl']->assign("api_callback",true);
			$GLOBALS['tmpl']->display("user_login.html");
		}
		else
		{
			showErr($GLOBALS['lang']['INVALID_VISIT']);
		}
	}	*/
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
		//验证码
		if(app_conf("VERIFY_IMAGE")==1)
		{
			$verify = md5(trim($_REQUEST['verify']));
			$session_verify = es_session::get('verify');
			if($verify!=$session_verify)
			{				
				showErr($GLOBALS['lang']['VERIFY_CODE_ERROR'],$ajax,url("debit","debit_user#login"));
			}
		}
		
		require_once APP_ROOT_PATH."system/libs/user.php";
		
		$_POST['user_pwd'] = trim(FW_DESPWD($_POST['user_pwd']));
		
		if(check_ipop_limit(CLIENT_IP,"user_dologin",intval(app_conf("SUBMIT_DELAY")))){
			$result = do_login_user($_POST['email'],$_POST['user_pwd']);
		}
		else
			showErr($GLOBALS['lang']['SUBMIT_TOO_FAST'],$ajax,url("debit","debit_user#login"));
		if($result['status'])
		{	
			$s_user_info = es_session::get("user_info");
			if(intval($_POST['auto_login'])==1)
			{
				//自动登录，保存cookie
				$user_data = $s_user_info;
				es_cookie::set("user_name",$user_data['email'],3600*24*30);			
				es_cookie::set("user_pwd",md5($user_data['user_pwd']."_EASE_COOKIE"),3600*24*30);
			}
			if($ajax==0&&trim(app_conf("INTEGRATE_CODE"))=='')
			{
				//$redirect = $_SERVER['HTTP_REFERER']?$_SERVER['HTTP_REFERER']:url("debit","debit");
				$redirect = url("debit","debit");
				app_redirect($redirect);
			}
			else
			{	
				$jump_url = get_gopreview();
				//print_r($jump_url);die;
				$s_user_info = es_session::get("user_info");
				if($s_user_info['ips_acct_no']=="" && app_conf("OPEN_IPS")){			
					if($ajax==1)
					{
						$return['status'] = 2;
						$return['info'] = "本站需绑定第三方托管账户，是否马上去绑定";
						$return['data'] = $result['msg'];
						$return['jump'] = $jump_url;
						$return['jump1'] = APP_ROOT."/index.php?ctl=collocation&act=CreateNewAcct&user_type=0&user_id=".$s_user_info['id'];
						ajax_return($return);
					}
					else
					{
						$GLOBALS['tmpl']->assign('integrate_result',$result['msg']);					
						showSuccess($GLOBALS['lang']['LOGIN_SUCCESS'],$ajax,$jump_url);
					}
				}
				else{
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
	
	/*
	
	public function steptwo(){
		if(intval($GLOBALS['user_info']['id'])==0)
		{
			es_session::set('before_login',$_SERVER['REQUEST_URI']);
			app_redirect(url("shop","user#login"));
		}
		$GLOBALS['tmpl']->display("user_step_two.html");
		exit;
	}
	*/
	/*
	public function stepsave(){
		if(intval($GLOBALS['user_info']['id'])==0)
		{
			es_session::set('before_login',$_SERVER['REQUEST_URI']);
			app_redirect(url("shop","user#login"));
		}
		$user_id=intval($GLOBALS['user_info']['id']);
		$focus_list = explode(",",$_REQUEST['user_ids']);
		foreach($focus_list as $k=>$focus_uid)
		{
			if(intval($focus_uid) > 0){
				$focus_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_focus where focus_user_id = ".$user_id." and focused_user_id = ".intval($focus_uid));
				if(!$focus_data)
				{
						$focused_user_name = $GLOBALS['db']->getOne("select user_name from ".DB_PREFIX."user where id = ".$focus_uid);
						$focus_data = array();
						$focus_data['focus_user_id'] = $user_id;
						$focus_data['focused_user_id'] = $focus_uid;
						$focus_data['focus_user_name'] = $GLOBALS['user_info']['user_name'];
						$focus_data['focused_user_name'] = $focused_user_name;
						$GLOBALS['db']->autoExecute(DB_PREFIX."user_focus",$focus_data,"INSERT");
						$GLOBALS['db']->query("update ".DB_PREFIX."user set focus_count = focus_count + 1 where id = ".$user_id);
						$GLOBALS['db']->query("update ".DB_PREFIX."user set focused_count = focused_count + 1 where id = ".$focus_uid);
				}
			}
		}		
		showSuccess($GLOBALS['lang']['REGISTER_SUCCESS'],0,url("shop","uc_center"));
	}
	*/
	public function loginout()
	{
		require_once APP_ROOT_PATH."system/libs/user.php";
		$result = loginout_user();
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
			app_redirect(url("debit"));		
		}
	}
	
	public function getpassword()
	{
		$GLOBALS['tmpl']->caching = true;
		$cache_id  = md5(MODULE_NAME.ACTION_NAME.$GLOBALS['deal_city']['id']);		
		if (!$GLOBALS['tmpl']->is_cached('debit_user_get_password.html', $cache_id))	
		{
			 
			$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['GET_PASSWORD_BACK']);
		}
		$GLOBALS['tmpl']->display("debit/debit_user_get_password.html",$cache_id);
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
			showErr("请输入手机验证码",1);
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
			//载入会员整合
			/*$integrate_code = trim(app_conf("INTEGRATE_CODE"));
			if($integrate_code!='')
			{
				$integrate_file = APP_ROOT_PATH."system/integrate/".$integrate_code."_integrate.php";
				if(file_exists($integrate_file))
				{
					require_once $integrate_file;
					$integrate_class = $integrate_code."_integrate";
					$integrate_obj = new $integrate_class;
				}
			}*/
				
			if($integrate_obj)
			{
				$result = $integrate_obj->edit_user($user_info,$user_pwd);
			}
				
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
			//载入会员整合
			/*$integrate_code = trim(app_conf("INTEGRATE_CODE"));
			if($integrate_code!='')
			{
				$integrate_file = APP_ROOT_PATH."system/integrate/".$integrate_code."_integrate.php";
				if(file_exists($integrate_file))
				{
					require_once $integrate_file;
					$integrate_class = $integrate_code."_integrate";
					$integrate_obj = new $integrate_class;
				}	
			}*/
			
			if($integrate_obj)
			{
				$result = $integrate_obj->edit_user($user_info,$user_pwd);				
			}
			
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
	
	
	/*
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
	}*/
	
	public function do_re_name_id()
	{
		$id= $GLOBALS['user_info']['id'];
		$real_name = strim($_REQUEST['real_name']);
		$idno = strim($_REQUEST['idno']);
		$sex = strim($_REQUEST['sex']);
		$byear = strim($_REQUEST['byear']);
		$bmonth = strim($_REQUEST['bmonth']);
		$bday = strim($_REQUEST['bday']);
		
		$user_type = intval($_REQUEST['user_type']);
		if($user_type ==  1)
		{
			$enterpriseName = strim($_REQUEST['enterpriseName']);
			$bankLicense = strim($_REQUEST['bankLicense']);
			$orgNo = strim($_REQUEST['orgNo']);
			$businessLicense  = strim($_REQUEST['businessLicense']);
			$taxNo = strim($_REQUEST['taxNo']);
			
			if($enterpriseName==""){
				showErr("请输入企业名称");
			}
			if($bankLicense==""){
				showErr("请输入开户银行许可证");
			}
			if($orgNo==""){
				showErr("请输入组织机构代码");
			}
			if($businessLicense==""){
				showErr("请输入营业执照编号");
			}
			if($taxNo==""){
				showErr("请输入税务登记号");
			}
			
			$user_conpany = array();
			$user_conpany['company_name'] = $enterpriseName;
			$user_conpany['contact'] = $real_name;
			
			$user_conpany['bankLicense'] = $bankLicense;
			$user_conpany['orgNo'] = $orgNo;
			$user_conpany['businessLicense'] = $businessLicense;
			$user_conpany['taxNo'] = $taxNo;
			$GLOBALS['db']->autoExecute(DB_PREFIX."user_conpany",$user_conpany,"UPDATE","user_id=".$id);
		}
		
		if(!$id)
		{
			showErr("该用户尚未登陆",url("debit","debit_user#login")); 
		}
		
		if(!$real_name)
		{
			showErr("请输入真实姓名"); //姓名格式错误
		}
		
		
		if($idno==""){
			showErr("请输入身份证号");
		}
		
		if(getIDCardInfo($idno)==0){
    			showErr("身份证号码错误！");
    	}
    	
    	
		
	
	
		//判断该实名是否存在
		if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user where idno = '.$idno.' and id<> $id ") > 0 ){
			showErr("该实名已被其他用户认证，非本人请联系客服");
		}
		
		
		if($user = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id =".$id))
		{	
			$user_info_re = array();
			$user_info_re['id'] = $id;
			$user_info_re['real_name'] = $real_name;
			$user_info_re['idno'] = $idno;
			$user_info_re['sex'] = $sex;
			$user_info_re['byear'] = $byear;
			$user_info_re['bmonth'] = $bmonth;
			$user_info_re['bday'] = $bday;
			if( $user_type == 1){
				$user_info_re['enterpriseName'] = $enterpriseName;
				$user_info_re['bankLicense'] = $bankLicense;
				$user_info_re['orgNo'] = $orgNo;
				$user_info_re['businessLicense'] = $businessLicense;
				$user_info_re['taxNo'] = $taxNo;
				$user_info_re['mobile'] = $contactPhone;
			}
			
			if($user['email']=="" && (int)app_conf("OPEN_IPS") > 0){
				$user_info_re['email'] = get_site_email($id);
			}
			$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user_info_re,"UPDATE","id=".$id);
			
			$data['user_id'] = $GLOBALS['user_info']['id'];
	    	$data['type'] = "credit_identificationscanning";
	    	$data['status'] = 0;
	    	$data['create_time'] = TIME_UTC;
	    	$data['passed'] = 0;
	    	
	    	$condition = "";
	    	if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user_credit_file WHERE user_id=".$GLOBALS['user_info']['id']." AND type='credit_identificationscanning'") > 0)
	    	{
	    		$mode = "UPDATE";
	    		$condition = "user_id=".$GLOBALS['user_info']['id']." AND type='credit_identificationscanning'";
	    	}
	    	else{
	    		$mode = "INSERT";
	    	}
	    	$GLOBALS['db']->autoExecute(DB_PREFIX."user_credit_file",$data,$mode,$condition);
			
			if(app_conf("OPEN_IPS") == 1){
				showSuccess("验证成功",0,APP_ROOT."/index.php?ctl=collocation&act=CreateNewAcct&user_type=0&user_id=".$id);
			}
			else{
				showSuccess("注册成功",0,APP_ROOT."/");
				
			}
		}
		else{
			showErr("该用户尚未注册");  //尚未注册
		}
		
	}
	
}
?>