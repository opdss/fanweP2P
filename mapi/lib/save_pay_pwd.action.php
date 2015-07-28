<?php

class save_pay_pwd{
	
	public function index()
	{
		
		$root = array();
		
		$email = strim($GLOBALS['request']['email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['pwd']);//密码		
		$verify = addslashes(htmlspecialchars(trim($GLOBALS['request']['mobile_code'])));//验证码		
		$user_pwd = addslashes(htmlspecialchars(trim($GLOBALS['request']['pay_pwd'])));//新支付密码
		$user_pwd_confirm = addslashes(htmlspecialchars(trim($GLOBALS['request']['pay_pwd_confirm'])));//确认支付密码
				
		if($user_pwd == null || $user_pwd =='')
		{
			$root['response_code'] = 0;
			$root['show_err'] = $GLOBALS['lang']['USER_PWD_ERROR'];
			output($root);
		}

		if($verify==""){
			$root['response_code'] = 0;
			$root['show_err'] = $GLOBALS['lang']['BIND_MOBILE_VERIFY_ERROR'];
			output($root);
		}		
		
		//检查用户,用户密码
		$user = user_check($email,$pwd);
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;
		if ($user_id >0){


			$sql = "select id,code from ".DB_PREFIX."user where id = '".$user_id."' and bind_verify = '".$verify."' and is_delete = 0";
			$user_info = $GLOBALS['db']->getRow($sql);
			$user_id = intval($user_info['id']);
			
			if($user_id == 0)
			{
				$root['response_code'] = 0;
				$root['show_err'] = $GLOBALS['lang']['BIND_MOBILE_VERIFY_ERROR'];
				output($root);
			}else{
			
				$new_pwd = md5($user_pwd);
					
				$sql = "update ".DB_PREFIX."user set paypassword='".$new_pwd."', bind_verify = '', verify_create_time = 0 where id = ".$user_id;
				$GLOBALS['db']->query($sql);
					
				$root['response_code'] = 1;
				$root['show_err'] = "密码更新成功!";//$GLOBALS['lang']['MOBILE_BIND_SUCCESS'];
				$root['sql'] = $sql;
				output($root);
			}
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}


		$root['program_title'] = "修改支付密码";
		output($root);

	}
}
?>