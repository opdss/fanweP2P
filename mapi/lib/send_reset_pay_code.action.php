<?php

class send_reset_pay_code{
	public function index()
	{
		$email = strim($GLOBALS['request']['email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['pwd']);//密码		
		
		
		if(app_conf("SMS_ON")==0)
		{
			$root['response_code'] = 0;
			$root['show_err'] = $GLOBALS['lang']['SMS_OFF'];//短信未开启
			output($root);
		}
				
		//检查用户,用户密码
		$user = user_check($email,$pwd);
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;
		if ($user_id >0){
			$mobile = $user['mobile'];
			$code = intval($user['bind_verify']);
			
			if($mobile == '')
			{
				$root['response_code'] = 0;
				$root['show_err'] = $GLOBALS['lang']['MOBILE_EMPTY_TIP'];
				output($root);
			}
			
			if(!check_mobile($mobile))
			{
				$root['response_code'] = 0;
				$root['show_err'] = $GLOBALS['lang']['FILL_CORRECT_MOBILE_PHONE'];
				output($root);
			}
			
			if(!check_ipop_limit(CLIENT_IP,"mobile_verify",60,0))
			{
				$root['response_code'] = 0;
				$root['show_err'] = $GLOBALS['lang']['MOBILE_SMS_SEND_FAST']; //短信发送太快
				output($root);
			}
	
			
			//开始生成手机验证
			if ($code == 0){
				//已经生成过了，则使用旧的验证码；反之生成一个新的
				$code = rand(1111,9999);
				$GLOBALS['db']->query("update ".DB_PREFIX."user set bind_verify = '".$code."',verify_create_time = '".TIME_UTC."' where id = ".$user_id);
			}
			
			//使用立即发送方式
			$result = send_verify_sms($mobile,$code,$user,true);//
			$root['response_code'] = $result['status'];			
			
			if ($root['response_code'] == 1){
				$root['show_err'] = $GLOBALS['lang']['MOBILE_VERIFY_SEND_OK'];
			}else{
				$root['show_err'] = $result['msg'];
				if ($root['show_err'] == null || $root['show_err'] == ''){
					$root['show_err'] = "验证码发送失败";
				}
			}
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;			
		}
		
		output($root);
	}
	
}
?>