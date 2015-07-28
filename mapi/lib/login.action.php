<?php
class login{
	public function index()
	{		
		$email = strim($GLOBALS['request']['email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['pwd']);//密码
		
		$result = user_login($email,$pwd);
		if($result['status'])
		{
			$user_data = $GLOBALS['user_info'];//$result['user'];
			$root['response_code'] = 1;
			$root['user_login_status'] = 1;//用户登陆状态：1:成功登陆;0：未成功登陆
			$root['show_err'] = "用户登陆成功";		
			$root['id'] = $user_data['id'];
			$root['user_name'] = $user_data['user_name'];
			$root['user_pwd'] = $user_data['user_pwd'];
			$root['user_money'] = $user_data['money'];
			$root['user_money_format'] = format_price($user_data['money']);//用户金额	
			
			/*
			$root['home_user']['fans'] = $user_data['focused_count'];
			$root['home_user']['photos'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."topic_image where user_id = ".$user_data['id']);
			$root['home_user']['goods'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."topic where user_id = ".$user_data['id']." and topic_group = 'Fanwe' and is_delete = 0 and is_effect = 1");
			$root['home_user']['follows'] = $user_data['focus_count'];	
			$root['home_user']['favs'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."topic where user_id = ".$user_data['id']." and fav_id <> 0");
			
			$root['home_user']['user_avatar'] = get_abs_img_root(get_muser_avatar($user_data['id'],"big"));
			$root['user_avatar'] = get_abs_img_root(get_muser_avatar($user_data['id'],"big"));
			
			if(strim($GLOBALS['request']['sina_id'])!='')
			{
				if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where sina_id = '".strim($GLOBALS['request']['sina_id'])."'")==0)
				{
					$access_token =  trim($GLOBALS['request']['access_token']);
					$GLOBALS['db']->query("update ".DB_PREFIX."user set sina_id = '".strim($GLOBALS['request']['sina_id'])."',sina_token = '".$access_token."' where id = ".$user_data['id']);				
				
				}
				

			}
			if(strim($GLOBALS['request']['tencent_id'])!='')
			{
				if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where tencent_id = '".strim($GLOBALS['request']['tencent_id'])."'")==0)
				{
				$GLOBALS['db']->query("update ".DB_PREFIX."user set tencent_id = '".strim($GLOBALS['request']['tencent_id'])."' where id = ".$user_data['id']);
			
				$openid = trim($GLOBALS['request']['openid']);
				$openkey = trim($GLOBALS['request']['openkey']);
		 		$access_token =  trim($GLOBALS['request']['access_token']);
				$GLOBALS['db']->query("update ".DB_PREFIX."user set t_access_token ='".$access_token."',t_openkey = '".$openkey."',t_openid = '".$openid."', login_ip = '".CLIENT_IP."',login_time= ".get_gmtime()." where id =".$user_data['id']);	
				}
				
			}
			*/
			
		}
		else
		{			
			$root['response_code'] = 0;
			$root['user_login_status'] = 0;//用户登陆状态：1:成功登陆;0：未成功登陆
			$root['show_err'] = $result['msg'];		
			$root['id'] = 0;
			$root['user_name'] = $email;
			$root['user_email'] = $email;					
		}
		
		//$root['act'] = "login";
		$root['program_title'] = "登陆";
		output($root);		
	}
}
?>