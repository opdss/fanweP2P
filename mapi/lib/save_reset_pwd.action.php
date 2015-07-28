<?php

class save_reset_pwd{
	
	public function index()
	{
		$mobile = addslashes(htmlspecialchars(trim($GLOBALS['request']['mobile'])));
		$verify = addslashes(htmlspecialchars(trim($GLOBALS['request']['mobile_code'])));
		$user_pwd = addslashes(htmlspecialchars(trim($GLOBALS['request']['user_pwd'])));
		$user_pwd_confirm = addslashes(htmlspecialchars(trim($GLOBALS['request']['user_pwd_confirm'])));
		
		$root = array();		
		
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

		
		if($verify==""){
			$root['response_code'] = 0;
			$root['show_err'] = $GLOBALS['lang']['BIND_MOBILE_VERIFY_ERROR'];
			output($root);			
		}
		
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

		$sql = "select id,code from ".DB_PREFIX."user where mobile = '".$mobile."' and bind_verify = '".$verify."' and is_delete = 0";
		$user_info = $GLOBALS['db']->getRow($sql);
		$user_id = intval($user_info['id']);
		$code = $user_info['code'];
		
		if($user_id == 0)
		{
			$root['response_code'] = 0;
			$root['show_err'] = $GLOBALS['lang']['BIND_MOBILE_VERIFY_ERROR'];
			output($root);
		}else{
						
			$new_pwd = md5($user_pwd.$code);
			
			$sql = "update ".DB_PREFIX."user set user_pwd='".$new_pwd."', bind_verify = '', verify_create_time = 0 where id = ".$user_id;
			$GLOBALS['db']->query($sql);
			
			$root['response_code'] = 1;
			$root['show_err'] = "密码更新成功!";//$GLOBALS['lang']['MOBILE_BIND_SUCCESS'];
			$root['sql'] = $sql;
			output($root);
		}
		$root['program_title'] = "修改密码";
		output($root);
	}
}
?>