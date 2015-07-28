<?php
// +----------------------------------------------------------------------
// | Fanwe 方维订餐小秘书商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class ajaxModule extends SiteBaseModule
{
	public function __construct(){
		parent::__construct();
		if(!check_hash_key()){
			showErr("非法请求!",1);
		}
	}
	public function check_field()
	{
		$field_name = addslashes(trim($_REQUEST['field_name']));
		$field_data = addslashes(trim($_REQUEST['field_data']));
		require_once APP_ROOT_PATH."system/libs/user.php";
		$res = check_user($field_name,$field_data);
		$result = array("status"=>1,"info"=>'');
		if($res['status'])
		{
			ajax_return($result);
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
			$result['status'] = 0;
			$result['info'] = $error_msg;
			ajax_return($result);
		}
	}
	
	function check_user(){
		
		$val = strim($_REQUEST['val']);
		if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user WHERE mobile ='".$val."' OR user_name='".$val."'") > 0){
			$result['status'] = 1;
			ajax_return($result);
		}
		else{
			$result['status'] = 0;
			ajax_return($result);
		}
	}
	
	//用户注册_生成邮箱验证码
	public function get_email_verify()
	{
		//开始生成邮箱验证码
		$verify_data['verify_code'] = rand(111111,999999);
		$verify_data['email'] = $_REQUEST['user_email'];
		$verify_data['create_time'] = TIME_UTC;
		$verify_data['client_ip'] = CLIENT_IP;
		
		if($vid = $GLOBALS['db']->getOne("SELECT id FROM ".DB_PREFIX."email_verify_code WHERE email='".$verify_data['email']."'"))
			$GLOBALS['db']->autoExecute(DB_PREFIX."email_verify_code",$verify_data,"UPDATE","id=".$vid);
		else
			$GLOBALS['db']->autoExecute(DB_PREFIX."email_verify_code",$verify_data,"INSERT");
		$email_x = $verify_data['email'];
		send_user_verify_mail($email_x,$verify_data['verify_code']);
		$data['status'] = 1;
		$data['info'] = "验证邮件已经发送，请注意查收";
		ajax_return($data);
	}
	
	//密码取回_生成邮箱验证码
	public function get_email_verifyss()
	{
		$email = $verify_data['email'] = strim($_REQUEST['user_email']);
		if(!check_email($email)){
			$data['status'] = 0;
			$data['info'] = "邮箱格式错误";
			ajax_return($data);
		}
		$user_info =  $GLOBALS['db']->getOne("select * from ".DB_PREFIX."user where email='".$email."'");
		if(!$user_info){
			$data['status'] = 0;
			$data['info'] = "邮箱对应会员不存在";
			ajax_return($data);
		}
		//开始生成邮箱验证码
		$verify_data['verify_code'] = rand(111111,999999);
		$verify_data['create_time'] = TIME_UTC;
		$verify_data['client_ip'] = CLIENT_IP;
	
		if($vid = $GLOBALS['db']->getOne("SELECT id FROM ".DB_PREFIX."email_verify_code WHERE email='".$verify_data['email']."'"))
			$GLOBALS['db']->autoExecute(DB_PREFIX."email_verify_code",$verify_data,"UPDATE","id=".$vid);
		else
			$GLOBALS['db']->autoExecute(DB_PREFIX."email_verify_code",$verify_data,"INSERT");

		send_user_verify_mails($email,$verify_data['verify_code'],$user_info);
		$data['status'] = 1;
		$data['info'] = "验证邮件已经发送，请注意查收";
		ajax_return($data);
	}
	
	//密码取回_生成邮箱验证码
	public function unit_get_email_verifyss()
	{
		$email = $verify_data['email'] = strim($_REQUEST['user_email']);
		if(!check_email($email)){
			$data['status'] = 0;
			$data['info'] = "邮箱格式错误";
			ajax_return($data);
		}
		$user_info =  $GLOBALS['db']->getOne("select * from ".DB_PREFIX."user where email='".$email."'");
		if(!$user_info){
			$data['status'] = 0;
			$data['info'] = "邮箱对应会员不存在";
			ajax_return($data);
		}
		//开始生成邮箱验证码
		$verify_data['verify_code'] = rand(111111,999999);
		$verify_data['create_time'] = TIME_UTC;
		$verify_data['client_ip'] = CLIENT_IP;
	
		if($vid = $GLOBALS['db']->getOne("SELECT id FROM ".DB_PREFIX."email_verify_code WHERE email='".$verify_data['email']."'"))
			$GLOBALS['db']->autoExecute(DB_PREFIX."email_verify_code",$verify_data,"UPDATE","id=".$vid);
		else
			$GLOBALS['db']->autoExecute(DB_PREFIX."email_verify_code",$verify_data,"INSERT");
		
		
		
		
		send_user_verify_mails($email,$verify_data['verify_code'],$user_info);
		$data['status'] = 1;
		$data['info'] = "验证邮件已经发送，请注意查收";
		ajax_return($data);
	}
	
	//邮箱绑定_发送保存验证码
	public function get_authorized_email_verifys()
	{
		$GLOBALS['authorized_info']  = es_session::get("authorized_info");
		$new_email = strim($_REQUEST['user_email']);
		//开始生成邮箱验证码
		$user= $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where email = '".$new_email."' and id<>".intval($GLOBALS['authorized_info']['id']));
		if($user)
		{
			$data['status'] =0;
			$data['info'] = "该邮箱已被其他用户绑定";
			ajax_return($data);
		}
		$email = $new_email;
		$user_id = intval($GLOBALS['authorized_info']['id']);
		$code = rand(111111,999999);
		$GLOBALS['db']->query("update ".DB_PREFIX."user set verify = '".$code."',verify_create_time = '".TIME_UTC."' where id = ".$user_id);
		
		send_user_verify_mails($email,$code,$GLOBALS['authorized_info']);
		$data['status'] = 1;
		$data['info'] = "验证邮件已经发送，请注意查收";
		ajax_return($data);
		//if($vid = $GLOBALS['db']->getOne("SELECT id FROM ".DB_PREFIX."user WHERE email='".$verify_data['email']."'"))
	}
	
	//邮箱绑定_发送保存验证码
	public function get_email_verifys()
	{
		$new_email = strim($_REQUEST['user_email']);
		//开始生成邮箱验证码
		$user= $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where email = '".$new_email."' and id<>".intval($GLOBALS['user_info']['id']));
		if($user)
		{
			$data['status'] =0;
			$data['info'] = "该邮箱已被其他用户绑定";
			ajax_return($data);
		}
		$email = $new_email;
		$user_id = intval($GLOBALS['user_info']['id']);
		$code = rand(111111,999999);
		$GLOBALS['db']->query("update ".DB_PREFIX."user set verify = '".$code."',verify_create_time = '".TIME_UTC."' where id = ".$user_id);
		
		send_user_verify_mails($email,$code,$GLOBALS['user_info']);
		$data['status'] = 1;
		$data['info'] = "验证邮件已经发送，请注意查收";
		ajax_return($data);
		//if($vid = $GLOBALS['db']->getOne("SELECT id FROM ".DB_PREFIX."user WHERE email='".$verify_data['email']."'"))
	}
	
	//邮箱绑定_发送保存验证码--担保机构
	public function get_unit_email_verifys()
	{
		$GLOBALS['manageagency_info']  = es_session::get("manageagency_info");
		$new_email = strim($_REQUEST['user_email']);
		//开始生成邮箱验证码
		$user= $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where email = '".$new_email."' and id<>".intval($GLOBALS['manageagency_info']['id']));
		if($user)
		{
			$data['status'] = 0;
			$data['info'] = "该邮箱已被其他用户绑定";
			ajax_return($data);
		}
		$email = $new_email;
		
		$user_id = intval($GLOBALS['manageagency_info']['id']);
		$code = rand(111111,999999);
		$GLOBALS['db']->query("update ".DB_PREFIX."user set verify = '".$code."',verify_create_time = '".TIME_UTC."' where id = ".$user_id);
		
		send_user_verify_mails($email,$code,$GLOBALS['manageagency_info']);
		$data['status'] = 1;
		$data['info'] = "验证邮件已经发送，请注意查收";
		ajax_return($data);
		//if($vid = $GLOBALS['db']->getOne("SELECT id FROM ".DB_PREFIX."user WHERE email='".$verify_data['email']."'"))
	}
	
	//获取手机注册验证码
	public function get_register_verify_code()
	{
		if(app_conf("SMS_ON")==0)
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['SMS_OFF'];
			ajax_return($data);
		}
		$user_mobile = strim($_REQUEST['user_mobile']);
			
		if($user_mobile == '')
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['VERIFY_MOBILE_EMPTY'];
			ajax_return($data);
		}
	
		if(!check_mobile($user_mobile))
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['FILL_CORRECT_MOBILE_PHONE'];
			ajax_return($data);
		}
	
		if(!check_ipop_limit(CLIENT_IP,"register_mobile_verify",60,0))
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['VERIFY_CODE_SEND_FAST'];
			ajax_return($data);
		}
	
	
		//开始生成手机验证
		$verify_data['verify_code'] = rand(111111,999999);
		$verify_data['mobile'] = $user_mobile;
		$verify_data['create_time'] = TIME_UTC;
		$verify_data['client_ip'] = CLIENT_IP;
		
		if($vid = $GLOBALS['db']->getOne("SELECT id FROM ".DB_PREFIX."mobile_verify_code WHERE mobile='".$user_mobile."'"))
			$GLOBALS['db']->autoExecute(DB_PREFIX."mobile_verify_code",$verify_data,"UPDATE","id=".$vid);	
		else
			$GLOBALS['db']->autoExecute(DB_PREFIX."mobile_verify_code",$verify_data,"INSERT");
		
		send_verify_sms($user_mobile,$verify_data['verify_code']);
		$data['status'] = 1;
		$data['info'] = $GLOBALS['lang']['MOBILE_VERIFY_SEND_OK'];
		ajax_return($data);
	}
	
	public function get_pwd_verify_code()
	{
		if(app_conf("SMS_ON")==0)
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['SMS_OFF'];
			ajax_return($data);
		}
		$user_mobile = strim($_REQUEST['user_mobile']);
			
		if($user_mobile == '')
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['VERIFY_MOBILE_EMPTY'];
			ajax_return($data);
		}
	
		if(!check_mobile($user_mobile))
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['FILL_CORRECT_MOBILE_PHONE'];
			ajax_return($data);
		}
		
		if($users = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user WHERE mobile='".$user_mobile."'")){
			if($users['is_delete'] == 1 || $users['is_effect']==0){
				$data['status'] = 0;
				$data['info'] = "会员被锁定或者删除";
				ajax_return($data);
			}
		}
		else{
			$data['status'] = 0;
			$data['info'] = "会员不存在";
			ajax_return($data);
		}
	
		if(!check_ipop_limit(CLIENT_IP,"register_mobile_verify",60,0))
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['VERIFY_CODE_SEND_FAST'];
			ajax_return($data);
		}
	
	
		//开始生成手机验证
		$verify_data['verify_code'] = rand(111111,999999);
		$verify_data['mobile'] = $user_mobile;
		$verify_data['create_time'] = TIME_UTC;
		$verify_data['client_ip'] = CLIENT_IP;
		
		if($vid = $GLOBALS['db']->getOne("SELECT id FROM ".DB_PREFIX."mobile_verify_code WHERE mobile='".$user_mobile."'"))
			$GLOBALS['db']->autoExecute(DB_PREFIX."mobile_verify_code",$verify_data,"UPDATE","id=".$vid);	
		else
			$GLOBALS['db']->autoExecute(DB_PREFIX."mobile_verify_code",$verify_data,"INSERT");
		
		send_verify_sms($user_mobile,$verify_data['verify_code']);
		$data['status'] = 1;
		$data['info'] = $GLOBALS['lang']['MOBILE_VERIFY_SEND_OK'];
		ajax_return($data);
	}
	
	//手机验证短信_取回密码
	public function get_re_pwd_verify_code()
	{
		if(app_conf("SMS_ON")==0)
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['SMS_OFF'];
			ajax_return($data);
		}
		$user_mobile = strim($_REQUEST['user_mobile']);
			
		if($user_mobile == '')
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['VERIFY_MOBILE_EMPTY'];
			ajax_return($data);
		}
	
		if(!check_mobile($user_mobile))
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['FILL_CORRECT_MOBILE_PHONE'];
			ajax_return($data);
		}
	
		if($users = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user WHERE mobile='".$user_mobile."'")){
			if($users['is_delete'] == 1 || $users['is_effect']==0){
				$data['status'] = 0;
				$data['info'] = "会员被锁定或者删除";
				ajax_return($data);
			}
		}
		else{
			$data['status'] = 0;
			$data['info'] = "会员不存在";
			ajax_return($data);
		}
	
		if(!check_ipop_limit(CLIENT_IP,"register_mobile_verify",60,0))
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['VERIFY_CODE_SEND_FAST'];
			ajax_return($data);
		}
	
	
		//开始生成手机验证
		$verify_data['verify_code'] = rand(111111,999999);
		$verify_data['mobile'] = $user_mobile;
		$verify_data['create_time'] = TIME_UTC;
		$verify_data['client_ip'] = CLIENT_IP;
	
		if($vid = $GLOBALS['db']->getOne("SELECT id FROM ".DB_PREFIX."mobile_verify_code WHERE mobile='".$user_mobile."'"))
			$GLOBALS['db']->autoExecute(DB_PREFIX."mobile_verify_code",$verify_data,"UPDATE","id=".$vid);
		else
			$GLOBALS['db']->autoExecute(DB_PREFIX."mobile_verify_code",$verify_data,"INSERT");
	
		send_verify_sms($user_mobile,$verify_data['verify_code'],$users);
		$data['status'] = 1;
		$data['info'] = $GLOBALS['lang']['MOBILE_VERIFY_SEND_OK'];
		ajax_return($data);
	}
	
	//手机验证短信_取回密码----管理机构
	public function unit_get_re_pwd_verify_code()
	{
		if(app_conf("SMS_ON")==0)
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['SMS_OFF'];
			ajax_return($data);
		}
		$user_mobile = strim($_REQUEST['user_mobile']);
			
		if($user_mobile == '')
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['VERIFY_MOBILE_EMPTY'];
			ajax_return($data);
		}
	
		if(!check_mobile($user_mobile))
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['FILL_CORRECT_MOBILE_PHONE'];
			ajax_return($data);
		}
	
		if($users = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user WHERE mobile='".$user_mobile."'")){
			/*if($users['is_delete'] == 1 || $users['is_effect']==0){
				$data['status'] = 0;
				$data['info'] = "会员被锁定或者删除";
				ajax_return($data);
			}*/
		}
		else{
			$data['status'] = 0;
			$data['info'] = "会员不存在";
			ajax_return($data);
		}
	
		if(!check_ipop_limit(CLIENT_IP,"register_mobile_verify",60,0))
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['VERIFY_CODE_SEND_FAST'];
			ajax_return($data);
		}
	
	
		//开始生成手机验证
		$verify_data['verify_code'] = rand(111111,999999);
		$verify_data['mobile'] = $user_mobile;
		$verify_data['create_time'] = TIME_UTC;
		$verify_data['client_ip'] = CLIENT_IP;
	
		if($vid = $GLOBALS['db']->getOne("SELECT id FROM ".DB_PREFIX."mobile_verify_code WHERE mobile='".$user_mobile."'"))
			$GLOBALS['db']->autoExecute(DB_PREFIX."mobile_verify_code",$verify_data,"UPDATE","id=".$vid);
		else
			$GLOBALS['db']->autoExecute(DB_PREFIX."mobile_verify_code",$verify_data,"INSERT");
	
		send_verify_sms($user_mobile,$verify_data['verify_code'],$users);
		$data['status'] = 1;
		$data['info'] = $GLOBALS['lang']['MOBILE_VERIFY_SEND_OK'];
		ajax_return($data);
	}
	
	//验证取回手机状态
	public function mobile_get_pwd_check_field()
	{
		$user_mobile = strim($_REQUEST['user_mobile']);
		
		if($user_mobile == '')
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['VERIFY_MOBILE_EMPTY'];
			ajax_return($data);
		}
	
		if(!check_mobile($user_mobile))
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['FILL_CORRECT_MOBILE_PHONE'];
			ajax_return($data);
		}
		
		if($users = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user WHERE mobile='".$user_mobile."'")){
			if($users['is_delete'] == 1 || $users['is_effect']==0){
				$data['status'] = 0;
				$data['info'] = "对应会员被锁定或者删除";
				ajax_return($data);
			}
		}
		else{
			$data['status'] = 0;
			$data['info'] = "对应会员不存在";
			ajax_return($data);
		}
		
		$data['status'] = 1;
		ajax_return($data);
	}
	//验证邮箱取回状态
	public function email_get_pwd_check_field()
	{
		$user_email = strim($_REQUEST['user_email']);
	
		if($user_email == '')
		{
			$data['status'] = 0;
			$data['info'] = "请输入邮箱";
			ajax_return($data);
		}
		
		if(!check_email($user_email)){
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['MAIL_FORMAT_ERROR'];
			ajax_return($data);
		}
	
		if($users = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user WHERE email='".$user_email."'")){
				
			if($users['is_delete'] == 1 || $users['is_effect']==0){
				$data['status'] = 0;
				$data['info'] = "对应会员被锁定或者删除";
				ajax_return($data);
			}
				
		}
		else{
			$data['status'] = 0;
			$data['info'] = "对应会员不存在";
			ajax_return($data);
		}
	
		$data['status'] = 1;
		ajax_return($data);
	}
	
	//验证取回手机状态---担保机构
	public function unit_mobile_get_pwd_check_field()
	{
		$user_mobile = strim($_REQUEST['user_mobile']);
		
		if($user_mobile == '')
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['VERIFY_MOBILE_EMPTY'];
			ajax_return($data);
		}
	
		if(!check_mobile($user_mobile))
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['FILL_CORRECT_MOBILE_PHONE'];
			ajax_return($data);
		}
		
		if($users = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user WHERE mobile='".$user_mobile."'")){
			/*if($users['is_delete'] == 1 || $users['is_effect']==0){
				$data['status'] = 0;
				$data['info'] = "对应会员被锁定或者删除";
				ajax_return($data);
			}*/
		}
		else{
			$data['status'] = 0;
			$data['info'] = "对应会员不存在";
			ajax_return($data);
		}
		
		$data['status'] = 1;
		ajax_return($data);
	}
	
	//验证取回邮箱状态---担保机构
	public function unit_email_get_pwd_check_field()
	{
		$user_email = strim($_REQUEST['user_email']);
		
		if($user_email == '')
		{
			$data['status'] = 0;
			$data['info'] = "请输入邮箱";
			ajax_return($data);
		}
		
		if(!check_email($user_email)){
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['MAIL_FORMAT_ERROR'];
			ajax_return($data);
		}
		if($users = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user WHERE email='".$user_email."'")){
				
			/*if($users['is_delete'] == 1 || $users['is_effect']==0){
				$data['status'] = 0;
				$data['info'] = "对应会员被锁定或者删除";
				ajax_return($data);
			}*/
				
		}
		else{
			$data['status'] = 0;
			$data['info'] = "对应会员不存在";
			ajax_return($data);
		}
	
		$data['status'] = 1;
		ajax_return($data);
	}
	
	
	
	public function get_verify_code()
	{
		if(app_conf("SMS_ON")==0)
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['SMS_OFF'];
			ajax_return($data);		
		}
		$user_mobile = strim($_REQUEST['user_mobile']);
		$user_id = intval($GLOBALS['user_info']['id']);
		if($user_id == 0)
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['PLEASE_LOGIN_FIRST'];
			ajax_return($data);
		}

		if($user_mobile == '')
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['VERIFY_MOBILE_EMPTY'];
			ajax_return($data);
		}
		
		if(!check_mobile($user_mobile))
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['FILL_CORRECT_MOBILE_PHONE'];
			ajax_return($data);
		}
		
		
		//查询是否有用户绑定
		$user= $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where mobile = '".$user_mobile."' and id <> ".$user_id);
		
		if($user)
		{
			if($user['id'] == intval($GLOBALS['user_info']['id']))
			{
				$data['status'] = 1;
				$data['info'] = $GLOBALS['lang']['MOBILE_VERIFIED'];
			}
			else
			{
				$data['status'] = 0;
				$data['info'] = $GLOBALS['lang']['MOBILE_USED_BIND'];
				
			}
			ajax_return($data);
			
		}
		
		if(!check_ipop_limit(CLIENT_IP,"bind_mobile_verify",60,0))
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['VERIFY_CODE_SEND_FAST'];
			ajax_return($data);
		}
		
		//开始生成手机验证
		$verify_data['verify_code'] = rand(111111,999999);
		$verify_data['mobile'] = $user_mobile;
		$verify_data['create_time'] = TIME_UTC;
		$verify_data['client_ip'] = CLIENT_IP;
	
		if($vid = $GLOBALS['db']->getOne("SELECT id FROM ".DB_PREFIX."mobile_verify_code WHERE mobile='".$user_mobile."'"))
			$GLOBALS['db']->autoExecute(DB_PREFIX."mobile_verify_code",$verify_data,"UPDATE","id=".$vid);
		else
			$GLOBALS['db']->autoExecute(DB_PREFIX."mobile_verify_code",$verify_data,"INSERT");
	
		send_verify_sms($user_mobile,$verify_data['verify_code'],$GLOBALS['user_info']);
		$data['status'] = 1;
		$data['info'] = $GLOBALS['lang']['MOBILE_VERIFY_SEND_OK'];
		
		ajax_return($data);
	}
	
	public function get_unit_verify_code()
	{
		$GLOBALS['manageagency_info'] = es_session::get("manageagency_info");
		if(app_conf("SMS_ON")==0)
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['SMS_OFF'];
			ajax_return($data);		
		}
		$user_mobile = addslashes(htmlspecialchars(trim($_REQUEST['user_mobile'])));
		$user_id = intval($GLOBALS['manageagency_info']['id']);
		if($user_id == 0)
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['PLEASE_LOGIN_FIRST'];
			ajax_return($data);
		}

		if($user_mobile == '')
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['VERIFY_MOBILE_EMPTY'];
			ajax_return($data);
		}
		
		if(!check_mobile($user_mobile))
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['FILL_CORRECT_MOBILE_PHONE'];
			ajax_return($data);
		}
		
		
		//查询是否有用户绑定
		$user= $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where mobile = '".$user_mobile."' and id <> ".$user_id);
		
		if($user)
		{
			if($user['id'] == intval($GLOBALS['manageagency_info']['id']))
			{
				$data['status'] = 1;
				$data['info'] = $GLOBALS['lang']['MOBILE_VERIFIED'];
			}
			else
			{
				$data['status'] = 0;
				$data['info'] = $GLOBALS['lang']['MOBILE_USED_BIND'];
			}
			
			ajax_return($data);
		}
		
		if(!check_ipop_limit(CLIENT_IP,"bind_mobile_verify",60,0))
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['VERIFY_CODE_SEND_FAST'];
			ajax_return($data);
		}
		
		//开始生成手机验证
		$verify_data['verify_code'] = rand(111111,999999);
		$verify_data['mobile'] = $user_mobile;
		$verify_data['create_time'] = TIME_UTC;
		$verify_data['client_ip'] = CLIENT_IP;
	
		if($vid = $GLOBALS['db']->getOne("SELECT id FROM ".DB_PREFIX."mobile_verify_code WHERE mobile='".$user_mobile."'"))
			$GLOBALS['db']->autoExecute(DB_PREFIX."mobile_verify_code",$verify_data,"UPDATE","id=".$vid);
		else
			$GLOBALS['db']->autoExecute(DB_PREFIX."mobile_verify_code",$verify_data,"INSERT");
			
		send_verify_sms($user_mobile,$verify_data['verify_code'],$GLOBALS['manageagency_info']);
		$data['status'] = 1;
		$data['info'] = $GLOBALS['lang']['MOBILE_VERIFY_SEND_OK'];
		
		ajax_return($data);
	}
	
	public function get_authorized_verify_code()
	{
		$GLOBALS['authorized_info'] = es_session::get("authorized_info");
		if(app_conf("SMS_ON")==0)
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['SMS_OFF'];
			ajax_return($data);		
		}
		$user_mobile = addslashes(htmlspecialchars(trim($_REQUEST['user_mobile'])));
		$user_id = intval($GLOBALS['authorized_info']['id']);
		if($user_id == 0)
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['PLEASE_LOGIN_FIRST'];
			ajax_return($data);
		}

		if($user_mobile == '')
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['VERIFY_MOBILE_EMPTY'];
			ajax_return($data);
		}
		
		if(!check_mobile($user_mobile))
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['FILL_CORRECT_MOBILE_PHONE'];
			ajax_return($data);
		}
		
		
		//查询是否有用户绑定
		$user= $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where mobile = '".$user_mobile."' and id <> ".$user_id);
		
		if($user)
		{
			if($user['id'] == intval($GLOBALS['manageagency_info']['id']))
			{
				$data['status'] = 1;
				$data['info'] = $GLOBALS['lang']['MOBILE_VERIFIED'];
			}
			else
			{
				$data['status'] = 0;
				$data['info'] = $GLOBALS['lang']['MOBILE_USED_BIND'];
			}
			
			ajax_return($data);
		}
		
		if(!check_ipop_limit(CLIENT_IP,"bind_mobile_verify",60,0))
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['VERIFY_CODE_SEND_FAST'];
			ajax_return($data);
		}
		
		//开始生成手机验证
		$verify_data['verify_code'] = rand(111111,999999);
		$verify_data['mobile'] = $user_mobile;
		$verify_data['create_time'] = TIME_UTC;
		$verify_data['client_ip'] = CLIENT_IP;
	
		if($vid = $GLOBALS['db']->getOne("SELECT id FROM ".DB_PREFIX."mobile_verify_code WHERE mobile='".$user_mobile."'"))
			$GLOBALS['db']->autoExecute(DB_PREFIX."mobile_verify_code",$verify_data,"UPDATE","id=".$vid);
		else
			$GLOBALS['db']->autoExecute(DB_PREFIX."mobile_verify_code",$verify_data,"INSERT");
		
		send_verify_sms($user_mobile,$verify_data['verify_code'],$GLOBALS['authorized_info']);
		$data['status'] = 1;
		$data['info'] = $GLOBALS['lang']['MOBILE_VERIFY_SEND_OK'];
		
		ajax_return($data);
	}
	
//手机绑定
	public function check_verify_code(){
		$ajax = intval($_REQUEST['ajax']);
		$verify = strim($_REQUEST['verify']);
		$old_mobile = strim($_REQUEST['old_mobile']);
		if($GLOBALS['user_info']['mobile']!=""){
			if($old_mobile != $GLOBALS['user_info']['mobile']){
				showErr("原手机号码不正确",$ajax);
			}
		}
		if($verify==""){
			showErr("验证码不能为空",$ajax);
		}
		
		$user_mobile = strim($_REQUEST['mobile']);
		
		$inum= $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where mobile = '".$user_mobile."' and id <> ".intval($GLOBALS['user_info']['id']));
		if ($inum > 0){
			showErr($user_mobile." 手机号码已被占用",$ajax);
		}
				
		if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."mobile_verify_code WHERE mobile='".$user_mobile."' AND verify_code='".$verify."' AND create_time + ".SMS_EXPIRESPAN." > ".TIME_UTC." ")==0){
			showErr("手机验证码出错,或已过期",$ajax);
		}	
		else 
		{
			$GLOBALS['db']->query("update ".DB_PREFIX."user set mobile='$user_mobile',mobilepassed=1, bind_verify = '', verify_create_time = 0 where id = ".intval($GLOBALS['user_info']['id']));
			if($GLOBALS['db']->affected_rows() > 0){
				showSuccess($GLOBALS['lang']['MOBILE_BIND_SUCCESS'],$ajax);
			}
			else{
				showErr("绑定失败",$ajax);
			}
		}
		
	}
	
	//手机绑定
	public function check_unit_verify_code(){
		$GLOBALS['manageagency_info'] = es_session::get("manageagency_info");
		
		$GLOBALS['manageagency_info']  = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = '".intval($GLOBALS['manageagency_info']['id'])."'");
		
		$ajax = intval($_REQUEST['ajax']);
		$verify = strim($_REQUEST['verify']);
		$old_mobile = strim($_REQUEST['old_mobile']);
		if($GLOBALS['manageagency_info']['mobile']!=""){
			if($old_mobile != $GLOBALS['manageagency_info']['mobile']){
				showErr("原手机号码不正确",$ajax);
			}
		}
		if($verify==""){
			showErr("验证码不能为空。");/*xsz  $GLOBALS['lang']['BIND_MOBILE_VERIFY_ERROR'],$ajax*/
		}
		$user = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".intval($GLOBALS['manageagency_info']['id']));
		$user_mobile = addslashes(htmlspecialchars(trim($_REQUEST['mobile'])));	
		
		$inum= $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where mobile = '".$user_mobile."' and id <> ".intval($GLOBALS['manageagency_info']['id']));
		if ($inum > 0){
			showErr($user_mobile." 手机号码已被占用",$ajax);
		}
				
		if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."mobile_verify_code WHERE mobile='".$user_mobile."' AND verify_code='".$verify."' AND create_time + ".SMS_EXPIRESPAN." > ".TIME_UTC." ")==0){
			showErr("手机验证码出错,或已过期",$ajax);
		}	
		else 
		{
			$GLOBALS['db']->query("update ".DB_PREFIX."user set mobile='$user_mobile',mobilepassed=1, bind_verify = '', verify_create_time = 0 where id = ".intval($GLOBALS['manageagency_info']['id']));
			if($GLOBALS['db']->affected_rows() > 0){
				showSuccess($GLOBALS['lang']['MOBILE_BIND_SUCCESS'],$ajax);
			}
			else{
				showErr("绑定失败",$ajax);
			}
		}
	}
	
	//手机绑定
	public function check_authorized_verify_code(){
		$GLOBALS['authorized_info'] = es_session::get("authorized_info");
		
		$GLOBALS['authorized_info']  = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = '".intval($GLOBALS['authorized_info']['id'])."'");
		
		$ajax = intval($_REQUEST['ajax']);
		$verify = strim($_REQUEST['verify']);
		$old_mobile = strim($_REQUEST['old_mobile']);
		if($GLOBALS['authorized_info']['mobile']!=""){
			if($old_mobile != $GLOBALS['authorized_info']['mobile']){
				showErr("原手机号码不正确",$ajax);
			}
		}
		if($verify==""){
			showErr("验证码不能为空。");/*xsz  $GLOBALS['lang']['BIND_MOBILE_VERIFY_ERROR'],$ajax*/
		}
		$user = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".intval($GLOBALS['authorized_info']['id']));
		$user_mobile = addslashes(htmlspecialchars(trim($_REQUEST['mobile'])));	
		$inum= $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where mobile = '".$user_mobile."' and id <> ".intval($GLOBALS['authorized_info']['id']));
		if ($inum > 0){
			showErr($user_mobile." 手机号码已被占用",$ajax);
		}
				
		if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."mobile_verify_code WHERE mobile='".$user_mobile."' AND verify_code='".$verify."' AND create_time + ".SMS_EXPIRESPAN." > ".TIME_UTC." ")==0){
			showErr("手机验证码出错,或已过期",$ajax);
		}	
		else 
		{
			$GLOBALS['db']->query("update ".DB_PREFIX."user set mobile='$user_mobile',mobilepassed=1, bind_verify = '', verify_create_time = 0 where id = ".intval($GLOBALS['authorized_info']['id']));
			if($GLOBALS['db']->affected_rows() > 0){
				showSuccess($GLOBALS['lang']['MOBILE_BIND_SUCCESS'],$ajax);
			}
			else{
				showErr("绑定失败",$ajax);
			}
		}
	}
	
	public function get_paypwd_verify_code()
	{
		if(app_conf("SMS_ON")==0)
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['SMS_OFF'];
			ajax_return($data);		
		}
		
		$user_id = intval($GLOBALS['user_info']['id']);
		if($user_id == 0)
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['PLEASE_LOGIN_FIRST'];
			ajax_return($data);
		}
		
		$user_mobile = $GLOBALS['user_info']['mobile'];
		
		//开始生成手机验证
		$verify_data['verify_code'] = rand(111111,999999);
		$verify_data['mobile'] = $user_mobile;
		$verify_data['create_time'] = TIME_UTC;
		$verify_data['client_ip'] = CLIENT_IP;
	
		if($vid = $GLOBALS['db']->getOne("SELECT id FROM ".DB_PREFIX."mobile_verify_code WHERE mobile='".$user_mobile."'"))
			$GLOBALS['db']->autoExecute(DB_PREFIX."mobile_verify_code",$verify_data,"UPDATE","id=".$vid);
		else
			$GLOBALS['db']->autoExecute(DB_PREFIX."mobile_verify_code",$verify_data,"INSERT");
		
		send_verify_sms($user_mobile,$verify_data['verify_code'],$GLOBALS['user_info']);
		$data['status'] = 1;
		$data['info'] = $GLOBALS['lang']['MOBILE_VERIFY_SEND_OK'];
						
		ajax_return($data);
	}
	
	public function check_paypwd_verify_code(){
		$ajax = intval($_REQUEST['ajax']);
		$verify = strim($_REQUEST['verify']);
		if(!$GLOBALS['user_info']){
			showErr($GLOBALS['lang']['PLEASE_LOGIN_FIRST'],$ajax);
		}
		if($verify==""){
			showErr($GLOBALS['lang']['BIND_MOBILE_VERIFY_ERROR'],$ajax);
		}
	
		$paypassword = trim(FW_DESPWD($_REQUEST['paypassword']));
		
				
		if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."mobile_verify_code WHERE mobile='".$GLOBALS['user_info']['mobile']."' AND verify_code='".$verify."' AND create_time + ".SMS_EXPIRESPAN." > ".TIME_UTC." ")==0){
			showErr("手机验证码出错,或已过期",$ajax);
		}	
		else 
		{
			$GLOBALS['db']->query("update ".DB_PREFIX."user set paypassword='".md5($paypassword)."', bind_verify = '', verify_create_time = 0 where id = ".intval($GLOBALS['user_info']['id']));
			if($GLOBALS['db']->affected_rows() > 0){
				showSuccess($GLOBALS['lang']['MOBILE_BIND_SUCCESS'],$ajax);
			}
			else{
				showErr("绑定失败",$ajax);
			}
		}
	}
	
	public function get_authorized_paypwd_verify_code()
	{
		$authorized_info  = es_session::get("authorized_info");
		if(app_conf("SMS_ON")==0)
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['SMS_OFF'];
			ajax_return($data);		
		}
		
		$user_id = intval($authorized_info['id']);
		if($user_id == 0)
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['PLEASE_LOGIN_FIRST'];
			ajax_return($data);
		}
		
		
		$user_mobile = $authorized_info['mobile'];

		//开始生成手机验证
		$verify_data['verify_code'] = rand(111111,999999);
		$verify_data['mobile'] = $user_mobile;
		$verify_data['create_time'] = TIME_UTC;
		$verify_data['client_ip'] = CLIENT_IP;
	
		if($vid = $GLOBALS['db']->getOne("SELECT id FROM ".DB_PREFIX."mobile_verify_code WHERE mobile='".$user_mobile."'"))
			$GLOBALS['db']->autoExecute(DB_PREFIX."mobile_verify_code",$verify_data,"UPDATE","id=".$vid);
		else
			$GLOBALS['db']->autoExecute(DB_PREFIX."mobile_verify_code",$verify_data,"INSERT");
		
		send_verify_sms($user_mobile,$verify_data['verify_code'],$GLOBALS['authorized_info']);
		$data['status'] = 1;
		$data['info'] = $GLOBALS['lang']['MOBILE_VERIFY_SEND_OK'];
						
		ajax_return($data);
	}
	
	public function check_authorized_paypwd_verify_code(){
		$ajax = intval($_REQUEST['ajax']);
		$verify = strim($_REQUEST['verify']);
		if($verify==""){
			showErr($GLOBALS['lang']['BIND_MOBILE_VERIFY_ERROR'],$ajax);
		}
		$authorized_info  = es_session::get("authorized_info");
		$user = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".intval($authorized_info['id']));
		$paypassword = strim(FW_DESPWD($_REQUEST['paypassword']));
		
		$inum= $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where mobile = '".$user_mobile."' and id <> ".intval($GLOBALS['authorized_info']['id']));
		if ($inum > 0){
			showErr($user_mobile." 手机号码已被占用",$ajax);
		}
				
		if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."mobile_verify_code WHERE mobile='".$user_mobile."' AND verify_code='".$verify."' AND create_time + ".SMS_EXPIRESPAN." > ".TIME_UTC." ")==0){
			showErr("手机验证码出错,或已过期",$ajax);
		}	
		else 
		{
			$GLOBALS['db']->query("update ".DB_PREFIX."user set paypassword='".md5($paypassword)."', bind_verify = '', verify_create_time = 0 where id = ".intval($authorized_info['id']));
			if($GLOBALS['db']->affected_rows() > 0){
				showSuccess($GLOBALS['lang']['MOBILE_BIND_SUCCESS'],$ajax);
			}
			else{
				showErr("绑定失败",$ajax);
			}
		}
	}
	
	public function set_sort()
	{
		$type = strim($_REQUEST['type']);
		es_cookie::set("shop_sort_field",$type); 
		if($type!='sort')
		{
			$sort_type = trim(es_cookie::get("shop_sort_type")); 
			if($sort_type&&$sort_type=='desc')
			{
				es_cookie::set("shop_sort_type",'asc'); 
			}
			else
			{
				es_cookie::set("shop_sort_type",'desc'); 
			}		
		}
		else
		{
			es_cookie::set("shop_sort_type",'desc'); 
		}
	}
	
	public function set_store_sort()
	{
		$type = htmlspecialchars(addslashes(trim($_REQUEST['type'])));
		if(!in_array($type,array("default","dp_count","avg_point","ref_avg_price")))
		{
			$type = "default";
		}
		es_cookie::set("store_sort_field",$type); 
		if($type!='sort')
		{
			$sort_type = trim(es_cookie::get("store_sort_type")); 
			if($sort_type&&$sort_type=='desc')
			{
				es_cookie::set("store_sort_type",'asc'); 
			}
			else
			{
				es_cookie::set("store_sort_type",'desc'); 
			}		
		}
		else
		{
			es_cookie::set("store_sort_type",'desc'); 
		}
	}

	public function set_event_sort()
	{
		$type = htmlspecialchars(addslashes(trim($_REQUEST['type'])));
		es_cookie::set("event_sort_field",$type); 
		if($type!='sort')
		{
			$sort_type = trim(es_cookie::get("event_sort_type")); 
			if($sort_type&&$sort_type=='desc')
			{
				es_cookie::set("event_sort_type",'asc'); 
			}
			else
			{
				es_cookie::set("event_sort_type",'desc'); 
			}		
		}
		else
		{
			es_cookie::set("event_sort_type",'desc'); 
		}
	}
	
	public function load_filter_group()
	{
		$cate_id = intval($_REQUEST['cate_id']);	
		$ids = load_auto_cache("shop_sub_parent_cate_ids",array("cate_id"=>$cate_id));		
		$filter_group_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."filter_group where is_effect = 1 and cate_id in (".implode(",",$ids).") order by sort desc");
		
		$GLOBALS['tmpl']->assign("filter_group_list",$filter_group_list);
		$GLOBALS['tmpl']->display("inc/inc_filter_group.html");
	}
	
	public function collect()
	{
		if(!$GLOBALS['user_info'])
		{
			$GLOBALS['tmpl']->assign("ajax",1);
			$html = $GLOBALS['tmpl']->fetch("inc/login_form.html");
			//弹出窗口处理
			$res['open_win'] = 1;
			$res['html'] = $html;
			ajax_return($res);
		}
		else
		{
			$goods_id = intval($_REQUEST['id']);
			$goods_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".$goods_id." and is_effect = 1 and is_delete = 0");
			if($goods_info)
			{
				$sql = "INSERT INTO `".DB_PREFIX."deal_collect` (`id`,`deal_id`, `user_id`, `create_time`) select '0','".$goods_info['id']."','".intval($GLOBALS['user_info']['id'])."','".TIME_UTC."' from dual where not exists (select * from `".DB_PREFIX."deal_collect` where `deal_id`= '".$goods_info['id']."' and `user_id` = ".intval($GLOBALS['user_info']['id']).")";
				$GLOBALS['db']->query($sql);
				if($GLOBALS['db']->affected_rows()>0)
				{
					//添加到动态
					insert_topic("deal_collect",$goods_id,intval($GLOBALS['user_info']['id']),$GLOBALS['user_info']['user_name']);
					$res['info'] = $GLOBALS['lang']['COLLECT_SUCCESS'];
				}
				else
				{
					$res['info'] = $GLOBALS['lang']['GOODS_COLLECT_EXIST'];
				}
				$res['open_win'] = 0;
				ajax_return($res);
			}
			else
			{
				$res['open_win'] = 0;
				$res['info'] = $GLOBALS['lang']['INVALID_GOODS'];
				ajax_return($res);
			}
		}
	}
	
	public function focus()
	{
		$user_id = intval($GLOBALS['user_info']['id']);
		if($user_id==0)
		{
			$data['tag'] = 4;
			$data['html'] = $GLOBALS['lang']['PLEASE_LOGIN_FIRST'];
			ajax_return($data);
		}
		$focus_uid = intval($_REQUEST['uid']);
		if($user_id==$focus_uid)
		{
			$data['tag'] = 3;
			$data['html'] = $GLOBALS['lang']['FOCUS_SELF'];
			ajax_return($data);
		}
		
		$focus_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_focus where focus_user_id = ".$user_id." and focused_user_id = ".$focus_uid);
		if(!$focus_data&&$user_id>0&&$focus_uid>0)
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
				$data['tag'] = 1;
				$data['html'] = $GLOBALS['lang']['CANCEL_FOCUS'];
				
				//添加到动态
				insert_topic("focus",$focus_uid,$user_id,$GLOBALS['user_info']['user_name']);
				
				ajax_return($data);
		}
		elseif($focus_data&&$user_id>0&&$focus_uid>0)
		{
			$GLOBALS['db']->query("delete from ".DB_PREFIX."user_focus where focus_user_id = ".$user_id." and focused_user_id = ".$focus_uid);
			$GLOBALS['db']->query("update ".DB_PREFIX."user set focus_count = focus_count - 1 where id = ".$user_id);
			$GLOBALS['db']->query("update ".DB_PREFIX."user set focused_count = focused_count - 1 where id = ".$focus_uid);		
			$data['tag'] =2;
			$data['html'] = $GLOBALS['lang']['FOCUS_THEY'];
			ajax_return($data);
		}
		
	}
	
	public function randuser()
	{
		$user_id = intval($GLOBALS['user_info']['id']);	
		$user_list = get_rand_user(24,0,$user_id);	
		$GLOBALS['tmpl']->assign("user_list",$user_list);		
		$GLOBALS['tmpl']->display("inc/uc/randuser.html");
	}
	
	
	public function relay_topic()
	{
		$topic = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."topic where id = ".intval($_REQUEST['id']));
		$GLOBALS['tmpl']->assign("topic_info",$topic);
		$GLOBALS['tmpl']->assign("user_info",$GLOBALS['user_info']);
		if($topic['origin_id']!=$topic['id'])
		{
			$origin_topic = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."topic where id = ".$topic['origin_id']);
			$GLOBALS['tmpl']->assign("origin_topic_info",$origin_topic);
		}
		$GLOBALS['tmpl']->display("inc/ajax_relay_box.html");
	}
	public function fav_topic()
	{
		$topic = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."topic where id = ".intval($_REQUEST['id']));
		$GLOBALS['tmpl']->assign("topic_info",$topic);
		if($topic['origin_id']!=$topic['id'])
		{
			$origin_topic = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."topic where id = ".$topic['origin_id']);
			$GLOBALS['tmpl']->assign("origin_topic_info",$origin_topic);
		}
		$GLOBALS['tmpl']->display("inc/ajax_relay_box.html");
	}	
	public function do_relay_topic()
	{
		if(intval($GLOBALS['user_info']['id'])==0)
		{
			$result['status'] = 0;
			$result['info'] = $GLOBALS['lang']['PLEASE_LOGIN_FIRST'];
		}
		else
		{
			$result['status'] = 1;
			$content = addslashes(htmlspecialchars(trim(valid_str($_REQUEST['content']))));
			$id = intval($_REQUEST['id']);
			$tid = insert_topic($content,$title="",$type="",$group="", $id, $fav_id=0);
			if($tid)
			{
				increase_user_active(intval($GLOBALS['user_info']['id']),"转发了一则分享");
				$GLOBALS['db']->query("update ".DB_PREFIX."topic set source_name = '网站' where id = ".intval($tid));
			}
			$result['info'] = $GLOBALS['lang']['RELAY_SUCCESS'];
		}
		ajax_return($result);
	}
	public function do_fav_topic()
	{	
		if(intval($GLOBALS['user_info']['id'])==0)
		{
			$result['status'] = 0;
			$result['info'] = $GLOBALS['lang']['PLEASE_LOGIN_FIRST'];
		}
		else
		{					
			$id = intval($_REQUEST['id']);
			$topic = $GLOBALS['db']->getRow("select id,user_id from ".DB_PREFIX."topic where id = ".$id);
			if(!$topic)
			{
				$result['status'] = 0;
				$result['info'] = $GLOBALS['lang']['TOPIC_NOT_EXIST'];
			}
			else
			{
				if($topic['user_id']==intval($GLOBALS['user_info']['id']))
				{
					$result['status'] = 0;
					$result['info'] = $GLOBALS['lang']['TOPIC_SELF'];
				}
				else
				{					
					$count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."topic where (fav_id = ".$id." or (origin_id = ".$id." and fav_id <> 0)) and user_id = ".intval($GLOBALS['user_info']['id']));
					if($count>0)
					{
						$result['status'] = 0;
						$result['info'] = $GLOBALS['lang']['TOPIC_FAVED'];
					}
					else
					{
						$result['status'] = 1;
						$tid = insert_topic($content,$title="",$type="",$group="", $relay_id = 0, $id);
						if($tid)
						{
							increase_user_active(intval($GLOBALS['user_info']['id']),"喜欢了一则分享");
							$GLOBALS['db']->query("update ".DB_PREFIX."topic set source_name = '网站' where id = ".intval($tid));
						}
						$result['info'] = $GLOBALS['lang']['FAV_SUCCESS'];
					}
				}
			}
		}
		ajax_return($result);
	}
	
	public function msg_reply(){
		$ajax = 1;
		$user_info = $GLOBALS['user_info'];
		if(!$user_info)
		{
			showErr($GLOBALS['lang']['PLEASE_LOGIN_FIRST'],$ajax);
		}
		if($_REQUEST['content']=='')
		{
			showErr($GLOBALS['lang']['MESSAGE_CONTENT_EMPTY'],$ajax);
		}
		
		if(!check_ipop_limit(CLIENT_IP,"message",intval(app_conf("SUBMIT_DELAY")),0))
		{
			showErr($GLOBALS['lang']['MESSAGE_SUBMIT_FAST'],$ajax);
		}
		
		$rel_table = strim($_REQUEST['rel_table']);
		$message_type = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."message_type where type_name='".$rel_table."'");
		if(!$message_type)
		{
			showErr($GLOBALS['lang']['INVALID_MESSAGE_TYPE'],$ajax);
		}			
		//添加留言
		$message['title'] = $_REQUEST['title']?strim($_REQUEST['title']):btrim(valid_str($_REQUEST['content']));
		$message['content'] = btrim(valid_str($_REQUEST['content']));
		$message['title'] = valid_str($message['title']);
			
		$message['create_time'] = TIME_UTC;
		$message['rel_table'] = $rel_table;
		$message['rel_id'] = intval($_REQUEST['rel_id']);
		$message['user_id'] = intval($GLOBALS['user_info']['id']);
		$message['pid'] = intval($_REQUEST['pid']);
		
		if(app_conf("USER_MESSAGE_AUTO_EFFECT")==0)
		{
			$message_effect = 0;
		}
		else
		{
			$message_effect = $message_type['is_effect'];
		}
		$message['is_effect'] = $message_effect;		
		$GLOBALS['db']->autoExecute(DB_PREFIX."message",$message);
		
		if($rel_table == "deal"){
			$l_user_id =  $GLOBALS['db']->getOne("SELECT user_id FROM ".DB_PREFIX."deal WHERE id=".$message['rel_id']);
		}
		else{
			$l_user_id =  $GLOBALS['db']->getOne("SELECT user_id FROM ".DB_PREFIX."deal_load_transfer WHERE id=".$message['rel_id']);
		}
		
		//添加到动态
		insert_topic($rel_table."_message_reply",$message['rel_id'],$message['user_id'],$GLOBALS['user_info']['user_name'],$l_user_id);
		
		if($rel_table == "deal"){
			
			require_once APP_ROOT_PATH.'app/Lib/deal.php';
			$deal = get_deal($message['rel_id']);
			$msg_u_id = $GLOBALS['db']->getOne("SELECT user_id FROM ".DB_PREFIX."message WHERE id=".$message['pid']);
			
			if($message['user_id'] != $msg_u_id){
				$msg_conf = get_user_msg_conf($deal['user_id']);
				//站内信
				if($msg_conf['sms_answer']==1){
					
					$notices['user_name'] = get_user_name($message['user_id']);
					$notices['url'] =  "“<a href=\"".$deal['url']."\">".$deal['name']."</a>”";
					$notices['msg'] = "“".$message['content']."”";
					
					$tmpl_contents = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_REPLY_MSG'",false);
					$GLOBALS['tmpl']->assign("notice",$notices);
					$content = $GLOBALS['tmpl']->fetch("str:".$tmpl_contents['content']);
					send_user_msg("",$content,0,$msg_u_id,TIME_UTC,0,true,14,$message['rel_id']);
				}
				
				//邮件
				if($msg_conf['mail_answer']==1 && app_conf('MAIL_ON')==1){
					$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$msg_u_id);
					$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_MAIL_DEAL_REPLY_MSG'",false);
					$tmpl_content = $tmpl['content'];
					
					$notice['user_name'] = $user_info['user_name'];
					$notice['msg_user_name'] = get_user_name($message['user_id'],false);
					$notice['deal_name'] = $deal['name'];
					$notice['deal_url'] = SITE_DOMAIN.url("index","deal",array("id"=>$deal['id']));
					$notice['message'] = $message['content'];
					$notice['site_name'] = app_conf("SHOP_TITLE");
					$notice['site_url'] = SITE_DOMAIN.APP_ROOT;
					$notice['help_url'] = SITE_DOMAIN.url("index","helpcenter");
					
					
					$GLOBALS['tmpl']->assign("notice",$notice);
					
					$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
					$msg_data['dest'] = $user_info['email'];
					$msg_data['send_type'] = 1;
					$msg_data['title'] = "用户".get_user_name($message['user_id'],false)."回复了你的留言！";
					$msg_data['content'] = addslashes($msg);
					$msg_data['send_time'] = 0;
					$msg_data['is_send'] = 0;
					$msg_data['create_time'] = TIME_UTC;
					$msg_data['user_id'] = $user_info['id'];
					$msg_data['is_html'] = $tmpl['is_html'];
					$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
				}
			}
		}
		
		showSuccess($GLOBALS['lang']['REPLY_POST_SUCCESS'],$ajax);
	}
	
	public function ajax_login()
	{
		$GLOBALS['tmpl']->display("inc/login_form.html");
	}

	public function drop_pm()
	{
		if($GLOBALS['user_info'])
		{
			$user_id = intval($GLOBALS['user_info']['id']);
			$res = $_REQUEST['pm_key'];
			foreach($res as $key)
			{
				$sql = "update  ".DB_PREFIX."msg_box set is_delete = 1 where ((to_user_id = ".$user_id." and `type` = 0) or (from_user_id = ".$user_id." and `type` = 1)) and group_key = '".$key."'";
				$GLOBALS['db']->query($sql);
			}
			$result['status'] = 1;
			$result['info'] = $GLOBALS['lang']['DELETE_SUCCESS'];
		}
		else
		{
			$result['status'] = 0;
			$result['info'] = $GLOBALS['lang']['PLEASE_LOGIN_FIRST'];
		}
		ajax_return($result);
	}
	
	public function drop_pmxiaoxi()
	{
		if($GLOBALS['user_info'])
		{
			$user_id = intval($GLOBALS['user_info']['id']);
			$res = $_REQUEST['pm_key'];
			foreach($res as $key)
			{
				$sql = "update  ".DB_PREFIX."msg_box set is_delete = 1 where ((to_user_id = ".$user_id." and `type` = 0) or (from_user_id = ".$user_id." and `type` = 1)) and group_key = '".$key."'";
				$GLOBALS['db']->query($sql);
			}
			$result['status'] = 1;
			$result['info'] = $GLOBALS['lang']['DELETE_SUCCESS'];
		}
		else
		{
			$result['status'] = 0;
			$result['info'] = $GLOBALS['lang']['PLEASE_LOGIN_FIRST'];
		}
		ajax_return($result);
	}
	
	public function drop_pm_item()
	{
		if($GLOBALS['user_info'])
		{
			$user_id = intval($GLOBALS['user_info']['id']);
			$res = $_REQUEST['id'];
			foreach($res as $id)
			{
				$sql = "update  ".DB_PREFIX."msg_box set is_delete = 1 where id = '".intval($id)."'";
				$GLOBALS['db']->query($sql);
			}
			$result['status'] = 1;
			$result['info'] = $GLOBALS['lang']['DELETE_SUCCESS'];
		}
		else
		{
			$result['status'] = 0;
			$result['info'] = $GLOBALS['lang']['PLEASE_LOGIN_FIRST'];
		}
		ajax_return($result);
	}	
	public function check_send()
	{
		/*$user_name = addslashes(trim($_REQUEST['user_name']));
		if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_focus where focused_user_name = '".$GLOBALS['user_info']['user_name']."' and focus_user_name = '".$user_name."'")>0)
		{
			//是粉丝
			$result['status'] = 1;
		}
		else
		{
			$result['status'] = 0;
		}*/
		$result['status'] = 1;
		ajax_return($result);
	}
	
	public function send_pm()
	{
		if($GLOBALS['user_info'])
		{
			$user_name = strim($_REQUEST['user_name']);
			$user_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."user where user_name = '".$user_name."'");
			if(intval($user_id)==0)
			{
				$result['status'] = 0;
				$result['info'] = $GLOBALS['lang']['TO_USER_EMPTY'];
				ajax_return($result);
			}
			/*if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_focus where focused_user_name = '".$GLOBALS['user_info']['user_name']."' and focus_user_name = '".$user_name."'")==0)
			{
				//不是粉丝,验证是否有来信记录
				$sql = "select count(*) from ".DB_PREFIX."msg_box 
						where is_delete = 0 and 
						(to_user_id = ".intval($GLOBALS['user_info']['id'])." and `type` = 0 and from_user_id = ".$user_id.")";
				$inbox_count = $GLOBALS['db']->getOne($sql);
				if($inbox_count==0)
				{
					$result['status'] = 0;
					$result['info'] = $GLOBALS['lang']['FANS_ONLY'];
					ajax_return($result);
				}			
			}*/
			$content = btrim($_REQUEST['content']);
			send_user_msg("",$content,intval($GLOBALS['user_info']['id']),$user_id,TIME_UTC);
			$result['status'] = 1;
			$key = array($user_id,intval($GLOBALS['user_info']['id']));
			sort($key);
			$group_key = implode("_",$key);
			$result['info'] = url("shop","uc_msg#deal",array("id"=>$group_key));
		}
		else
		{
			$result['status'] = 0;
			$result['info'] = $GLOBALS['lang']['PLEASE_LOGIN_FIRST'];
		}
		ajax_return($result);
	}
	
	public function usercard()
	{
		$uid = intval($_REQUEST['uid']);		
		$uinfo = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$uid." and is_delete = 0 and is_effect = 1");		
		if($uinfo)
		{
			$user_id = intval($GLOBALS['user_info']['id']);
			$focused_uid = intval($uid);
			$focus_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_focus where focus_user_id = ".$user_id." and focused_user_id = ".$focused_uid);
			if($focus_data)
				$uinfo['focused'] = 1; 		
			$uinfo['point_level'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."user_level where id = ".intval($uinfo['level_id']));
			$GLOBALS['tmpl']->assign("card_info",$uinfo);		
			$GLOBALS['tmpl']->display("inc/usercard.html");
		}
		else 
		{
			header("Content-Type:text/html; charset=utf-8");
			echo "<div class='load'>该会员已被删除或者已被禁用</div>";
		}
	}
	
	//采集分享
	/**
	 * 传入 class_name,url
	 * **
	 * 传出 
	 *  array("status"=>"","info"=>"", "group"=>"","type"=>"","group_data"=>"","content"=>"","tags"=>"","images"=>array("id"=>"","url"=>""));					
	 */
	public function do_fetch()
	{
		$class_name = addslashes(trim($_REQUEST['class_name']));
		$url = trim($_REQUEST['url']);
		$result['status'] = 0;
		if(file_exists(APP_ROOT_PATH."system/fetch_topic/".$class_name."_fetch_topic.php"))
		{
			require_once APP_ROOT_PATH."system/fetch_topic/".$class_name."_fetch_topic.php";
			$class = $class_name."_fetch_topic";
			if(class_exists($class))
			{
				$api = new $class;
				$rs = $api->fetch($url);
				if($rs['status']==0)
				{
					$result['info'] = $rs['info'];
				}
				else
				{
					$result['status'] = 1;
					$result['group'] = $class_name;
					$result['group_data'] = $rs['group_data'];
					$result['content'] = $rs['content'];
					$result['type'] = $rs['type'];
					$result['tags'] = $rs['tags'];
					$result['images'] = $rs['images'];					 
				}
			}	
			else
			{
				$result['info'] = "接口不存在";
			}		
		}
		else
		{
			$result['info'] = "接口不存在";
		}
		
		ajax_return($result);
	}
	
	
	public function set_syn()
	{
		if($GLOBALS['user_info'])
		{
			$field = addslashes(trim($_REQUEST['field']));
			$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".intval($GLOBALS['user_info']['id']));
			$upd_value = intval($user_info[$field]) == 0? 1:0;
			$GLOBALS['db']->query("update ".DB_PREFIX."user set `".$field."` = ".$upd_value." where id = ".intval($GLOBALS['user_info']['id']));
			$result['info'] = "设置成功";
			$user_info[$field] = $upd_value;
			es_session::set("user_info",$user_info);
		}
		else
		{
			$result['info'] = $GLOBALS['lang']['PLEASE_LOGIN_FIRST'];
		}
		ajax_return($result);
	}
	
	
	//ajax同步发微博
	public function syn_to_weibo()
	{
		set_time_limit(0);
		$topic_id = intval($_REQUEST['topic_id']);
		$user_id = intval($GLOBALS['user_info']['id']);
		$api_class_name = addslashes(htmlspecialchars(trim($_REQUEST['class_name'])));
		es_session::close();
		$topic = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."topic where id = ".$topic_id);	
		if($topic['topic_group']!="share")
		{
			$group = $topic['topic_group'];
			if(file_exists(APP_ROOT_PATH."system/fetch_topic/".$group."_fetch_topic.php"))
			{
				require_once APP_ROOT_PATH."system/fetch_topic/".$group."_fetch_topic.php";
				$class_name = $group."_fetch_topic";
				if(class_exists($class_name))
				{
					$fetch_obj = new $class_name;
					$data = $fetch_obj->decode_weibo($topic);
				}
			}
		}
		else
		{
			$data['content'] = $topic['content'];
			
			//图片
			$topic_image = $GLOBALS['db']->getRow("select o_path from ".DB_PREFIX."topic_image where topic_id = ".$topic['id']);
			if($topic_image)
			$data['img'] = SITE_DOMAIN.APP_ROOT."/".$topic_image['o_path'];
		}
		
		$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$user_id);
		$api = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."api_login where is_weibo = 1 and class_name = '".$api_class_name."'");
		if($user_info["is_syn_".strtolower($api['class_name'])]==1)
		{
				//发送本微博
				require_once APP_ROOT_PATH."system/api_login/".$api_class_name."_api.php";
				$api_class = $api_class_name."_api";
				$api_obj = new $api_class($api);
				$api_obj->send_message($data);
		}
	}
	
	public function load_api_url()
	{
		$type = intval($_REQUEST['type']);  //0:小登录图标 1:大登录图标 2:绑定图标
		$class_name = addslashes(htmlspecialchars(trim($_REQUEST['class_name'])));
		if(file_exists(APP_ROOT_PATH."system/api_login/".$class_name."_api.php"))
		{
				require_once APP_ROOT_PATH."system/api_login/".$class_name."_api.php";
				$api_class = $class_name."_api";
				$api = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."api_login where class_name = '".$class_name."'");
				$api_obj = new $api_class($api);
				if($type==0)
				$url = $api_obj->get_api_url();
				elseif($type==1)
				$url = $api_obj->get_big_api_url();
				else
				$url = $api_obj->get_bind_api_url();				
		}
		$domain = app_conf("PUBLIC_DOMAIN_ROOT")==''?SITE_DOMAIN.$GLOBALS['IMG_APP_ROOT']:app_conf("PUBLIC_DOMAIN_ROOT");	
		$url = str_replace("./public/",$domain."/public/",$url);	
		header("Content-Type:text/html; charset=utf-8");
		echo $url;
	}
	
	public function update_user_tip()
	{
			require_once APP_ROOT_PATH."app/Lib/insert_libs.php";
			header("Content-Type:text/html; charset=utf-8");
			echo  insert_load_user_tip();
	}
	
	public function check_login_status()
	{
		if($GLOBALS['user_info'])		
		$result['status'] = 1;		
		else
		$result['status'] = 0;
		ajax_return($result);
	}
	
	//验证验证码
	public function checkverify()
	{
		$ajax = intval($_REQUEST['ajax']);
		
		if(app_conf("VERIFY_IMAGE")==1)
		{
			$verify = md5(trim($_REQUEST['verify']));
			$session_verify = es_session::get('verify');
			if($verify!=$session_verify)
			{				
				showErr($GLOBALS['lang']['VERIFY_CODE_ERROR'],$ajax);
			}
			else
			{
				showSuccess("验证成功",$ajax);
			}
		}
		else
		{
			showSuccess("验证成功",$ajax);
		}
	}
	
	public function signin()
	{
		$user_id = intval($GLOBALS['user_info']['id']);
		if($user_id==0)
		{
			$result['status'] = 2;
			ajax_return($result);
		}
		else
		{
			
			$t_begin_time = to_timespan(to_date(TIME_UTC,"Y-m-d"));  //今天开始
			$t_end_time = to_timespan(to_date(TIME_UTC,"Y-m-d"))+ (24*3600 - 1);  //今天结束
			$y_begin_time = $t_begin_time - (24*3600); //昨天开始
			$y_end_time = $t_end_time - (24*3600);  //昨天结束
			
			$t_sign_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_sign_log where user_id = ".$user_id." and sign_date between ".$t_begin_time." and ".$t_end_time);
			if($t_sign_data)
			{
				$result['status'] = 1;
				$result['info'] = "您已经签到过了";
			}
			else
			{
				$y_sign_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_sign_log where user_id = ".$user_id." and sign_date between ".$y_begin_time." and ".$y_end_time);
				$total_signcount = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_sign_log where user_id = ".$user_id);
				if($y_sign_data&&$total_signcount>=3)
				{
					$point = intval(app_conf("USER_LOGIN_KEEP_POINT"));
					$score = intval(app_conf("USER_LOGIN_KEEP_SCORE"));
					$money = doubleval(app_conf("USER_LOGIN_KEEP_MONEY"));
				}
				else
				{
					$point = intval(app_conf("USER_LOGIN_POINT"));
					$score = intval(app_conf("USER_LOGIN_SCORE"));
					$money = doubleval(app_conf("USER_LOGIN_MONEY"));
				}
				if($point>0||$score>0||$money>0)
				{
					require_once APP_ROOT_PATH."system/libs/user.php";
					if($money>0)
						$data["money"]=$money;
					if($score>0)
						$data["score"]=$score;
					if($point>0)
						$data["point"]=$point;
					modify_account($data,$user_id,"您在".to_date(TIME_UTC)."签到成功",25);
					$sign_log['user_id'] = $user_id;
					$sign_log['sign_date'] = TIME_UTC;
					$GLOBALS['db']->autoExecute(DB_PREFIX."user_sign_log",$sign_log);					
				}
				$result['status'] = 1;
				$result['info'] = "签到成功";
			}
			ajax_return($result);
		}
	}
	
	public function gopreview()
	{		
		header("Content-Type:text/html; charset=utf-8");
		echo get_gopreview();		
	}
	
	/**
	 * 举报用户
	 */
	public function reportguy(){
		if(!$GLOBALS['user_info'])
			exit();
			
		$user_id = intval($_REQUEST['user_id']);
		if($user_id==0)
			exit();
		$u_info = get_user("id,user_name",$user_id);
		
		$GLOBALS['tmpl']->assign("u_info",$u_info);
		
		
		$GLOBALS['tmpl']->display("inc/ajax/reportguy.html");
	}
	
	public function savereportguy(){
		$result  = array("status"=>0,"message"=>"");
		if(!$GLOBALS['user_info']){
			$result['message'] = $GLOBALS['lang']['PLEASE_LOGIN_FIRST'];
			ajax_return($result);
			exit();
		}
		
		if(!check_ipop_limit(CLIENT_IP,"savereportguy",10,0))
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['MESSAGE_SUBMIT_FAST'];
			ajax_return($data);
		}
		
		$user_id = intval($_REQUEST['user_id']);
		if($user_id==0){
			$result['message'] = "没有该用户";
			ajax_return($result);
			exit();
		}
		
		$data['user_id'] = $GLOBALS['user_info']['id'];
		$data['r_user_id'] = $user_id;
		$data['reason'] = htmlspecialchars($_REQUEST['reason']);
		$data['content'] = htmlspecialchars($_REQUEST['content']);
		
		$GLOBALS['db']->autoExecute(DB_PREFIX."reportguy",$data,"INSERT");
		
		$result['status'] = 1;
		ajax_return($result);
	}
	
	/**
	 * 站内信
	 */
	public function send_msg(){
		if(!$GLOBALS['user_info'])
			exit();
			
		$user_id = intval($_REQUEST['user_id']);
		if($user_id==0)
			exit();
		$u_info = get_user("id,user_name",$user_id);
		
		$GLOBALS['tmpl']->assign("u_info",$u_info);
		
		
		$GLOBALS['tmpl']->display("inc/ajax/send_msg.html");
	}
	
	public function send_mobile_verify_code()
	{
		if(app_conf("SMS_ON")==0)
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['SMS_OFF'];
			ajax_return($data);
		}
		$mobile = addslashes(htmlspecialchars(trim($_REQUEST['mobile'])));
	
		if($mobile == '')
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['MOBILE_EMPTY_TIP'];
			ajax_return($data);
		}
	
		if(!check_mobile($mobile))
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['FILL_CORRECT_MOBILE_PHONE'];
			ajax_return($data);
		}
	
		$field_name = addslashes(trim($_REQUEST['mobile']));
		$field_data = $mobile;
		require_once APP_ROOT_PATH."system/libs/user.php";
		$res = check_user($field_name,$field_data);
		$result = array("status"=>1,"info"=>'');
		if(!$res['status'])
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
			$result['status'] = 0;
			$result['info'] = $error_msg;
			ajax_return($result);
		}
	
	
		if(!check_ipop_limit(CLIENT_IP,"mobile_verify",60,0))
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['MOBILE_SMS_SEND_FAST'];
			ajax_return($data);
		}
	
		if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."mobile_verify_code where mobile = '".$mobile."' and client_ip='".CLIENT_IP."' and create_time>=".(get_gmtime()-60)." ORDER BY id DESC") > 0)
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['MOBILE_SMS_SEND_FAST'];
			ajax_return($data);
		}
	
		/*
		//删除超过5分钟的验证码
		$GLOBALS['db']->query("DELETE FROM ".DB_PREFIX."mobile_verify_code WHERE create_time <=".get_gmtime()-300);
		//开始生成手机验证
		$code = rand(1111,9999);
		$GLOBALS['db']->autoExecute(DB_PREFIX."mobile_verify_code",array("verify_code"=>$code,"mobile"=>$mobile,"create_time"=>get_gmtime(),"client_ip"=>CLIENT_IP),"INSERT");
		send_verify_sms($mobile,$code);
		$data['status'] = 1;
		$data['info'] = "验证码发送成功";
		*/
		
		//删除超过5分钟的验证码
		$GLOBALS['db']->query("DELETE FROM ".DB_PREFIX."mobile_verify_code WHERE create_time <=".get_gmtime()-300);
		
		$verify_code = $GLOBALS['db']->getOne("select verify_code from ".DB_PREFIX."mobile_verify_code where mobile = '".$mobile."' and create_time>=".(TIME_UTC-180)." ORDER BY id DESC");
		if(intval($verify_code) == 0)
		{
			//如果数据库中存在验证码，则取数据库中的（上次的 ）；确保连接发送时，前后2条的验证码是一至的.==为了防止延时
			//开始生成手机验证
			$verify_code = rand(111111,999999);
			$GLOBALS['db']->autoExecute(DB_PREFIX."mobile_verify_code",array("verify_code"=>$verify_code,"mobile"=>$mobile,"create_time"=>get_gmtime(),"client_ip"=>CLIENT_IP),"INSERT");
		}
		
		//使用立即发送方式	
		$result = send_verify_sms($mobile,$verify_code,null,true);//
		$data['status'] = $result['status'];		
		
		if ($data['status'] == 1){
			$data['info'] = $GLOBALS['lang']['MOBILE_VERIFY_SEND_OK'];
		}else{
			$data['info'] = $result['msg'];
			if ($data['info'] == null || $data['info'] == ''){
				$data['info'] = "验证码发送失败";
			}
		}
		
		
		ajax_return($data);
	}
	/**
	 * 检查用户资料
	 */
	function check_user_info(){
		$user_id = $GLOBALS['user_info']['id'];
		if($user_id == 0){
			showErr('请先登录',1);
		}
		$user_type = intval(trim($_REQUEST['user_type']));
	
		$data = array();
		if ($user_type == 0){
			$data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$user_id);
		}else{
			$data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$user_id);
		}
		
		$err_msg = "";
		if (empty($data)){
			showErr('用户不存在',1);
		}else{
			if (empty($data['ips_acct_no'])){			
				if (empty($data['idno'])){
					$err_msg .='身份证号码不能为空&nbsp;<a href="'.url("index","uc_account#security").'">去补充</a><br>';
				}else if (empty($data['real_name'])){
					$err_msg .='真实姓名不能为空&nbsp;<a href="'.url("index","uc_account#security").'">去补充</a><br>';
				}else if (empty($data['mobile'])){
					$err_msg .='手机号码不能为空&nbsp;<a href="'.url("index","uc_account#security").'">去补充</a><br>';
				}else if (empty($data['email'])){
					$err_msg .='邮箱不能为空&nbsp;<a href="'.url("index","uc_account#security").'">去补充</a><br>';
				}		
			}else{
				$err_msg .='该用户已经申请过资金托管帐户:'.$data['ips_acct_no'];
			}
		}
		if($err_msg!=""){
			showErr($err_msg,1);
		}
		else
			showSuccess("验证成功",1,APP_ROOT."/index.php?ctl=collocation&act=CreateNewAcct&user_type=$user_type&user_id=".$user_id);
		
	}
	
	function get_user_load_item(){
		$deal_id = intval($_REQUEST['deal_id']);
		$l_key = intval($_REQUEST['l_key']);
		$obj = strim($_REQUEST['obj']);
		if($deal_id==0){
			showErr("数据错误",1);
		}
		require_once APP_ROOT_PATH."app/Lib/deal.php";
		$deal_info = get_deal($deal_id);
		
		if(!$deal_info){
			showErr("借款不存在",1);
		}
		
		
		//输出投标列表
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
			
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		
		$result = get_deal_user_load_list($deal_info,0,$l_key,-1,0,0,1,$limit);
		$rs_count = $result['count'];
		$page_all = ceil($rs_count/app_conf("PAGE_SIZE"));
		
		$GLOBALS['tmpl']->assign("load_user",$result['item']);
		$GLOBALS['tmpl']->assign("l_key",$l_key);
		$GLOBALS['tmpl']->assign("page_all",$page_all);
		$GLOBALS['tmpl']->assign("rs_count",$rs_count);
		$GLOBALS['tmpl']->assign("page",$page);
		$GLOBALS['tmpl']->assign("deal_id",$deal_id);
		$GLOBALS['tmpl']->assign("obj",$obj);
		$GLOBALS['tmpl']->assign("page_prev",$page - 1);
		$GLOBALS['tmpl']->assign("page_next",$page + 1);
		
		
		$html = $GLOBALS['tmpl']->fetch("inc/uc/ajax_load_user.html");
		
		showSuccess($html,1);
	}
	
	function bid_calculate(){
		require_once APP_ROOT_PATH."app/Lib/deal_func.php";
		
		
		echo bid_calculate($_POST);
	}
	
	function payment_fee(){
		$id = intval($_POST['id']);
		$return = array("fee_type"=>0,"fee_amount"=>0);
		$payment_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."payment WHERE id=".$id,false);
		if($payment_info){
			$return['fee_type'] = $payment_info['fee_type'];
			$return['fee_amount'] = $payment_info['fee_amount'];
		}
		ajax_return($return);
	}
}
?>