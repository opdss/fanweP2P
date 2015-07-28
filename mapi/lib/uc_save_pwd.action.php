<?php

class uc_save_pwd{
	
	public function index()
	{

		
		$root = array();

		$email = strim($GLOBALS['request']['email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['pwd']);//密码
		
		$user_pwd = addslashes(htmlspecialchars(trim($GLOBALS['request']['user_pwd'])));
		$user_pwd_confirm = addslashes(htmlspecialchars(trim($GLOBALS['request']['user_pwd_confirm'])));
				
		if($user_pwd != $user_pwd_confirm)
		{
			$root['response_code'] = 0;
			$root['show_err'] = $GLOBALS['lang']['USER_PWD_CONFIRM_ERROR'];
			output($root);
		}
			
		if($user_pwd == null || $user_pwd =='')
		{
			$root['response_code'] = 0;
			$root['show_err'] = $GLOBALS['lang']['USER_PWD_ERROR'];
			output($root);
		}
				
		//检查用户,用户密码
		$user = user_check($email,$pwd);
		$user_id  = intval($user['id']);
		if ($user_id >0){		
		
			$code = $user['code'];
			
			if($user_id == 0)
			{
				$root['response_code'] = 0;
				$root['show_err'] = $GLOBALS['lang']['BIND_MOBILE_VERIFY_ERROR'];
				output($root);
			}else{
							
				$new_pwd = md5($user_pwd.$code);
				
				$GLOBALS['db']->query("update ".DB_PREFIX."user set user_pwd='".$new_pwd."', bind_verify = '', verify_create_time = 0 where id = ".$user_id);
				
				$root['response_code'] = 1;
				$root['show_err'] = "密码更新成功!";//$GLOBALS['lang']['MOBILE_BIND_SUCCESS'];
				output($root);
			}
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="用户未登录成功(旧密码验证失败)";
			$root['user_login_status'] = 0;
		}
		output($root);
	}
}
?>