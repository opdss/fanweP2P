<?php

class reset_pay_pwd{
	
	public function index()
	{
		
		$root = array();
		
		$email = strim($GLOBALS['request']['email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['pwd']);//密码		
		
		//检查用户,用户密码
		$user = user_check($email,$pwd);
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;
		if ($user_id >0){
			
			$root['mobile'] = hideMobile($user['mobile']);
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}


		$root['program_title'] = "重置支付密码";
		output($root);

	}
}
?>