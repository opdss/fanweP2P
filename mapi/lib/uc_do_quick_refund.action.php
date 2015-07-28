<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
//执行正常还款
class uc_do_quick_refund
{
	public function index(){
		
		$root = array();
		
		$email = strim($GLOBALS['request']['email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['pwd']);//密码
		
		$id = intval($GLOBALS['request']['id']);
		$ids = strim($GLOBALS['request']['ids']);
		$paypassword = strim($GLOBALS['request']['paypassword']);	
		//检查用户,用户密码
		$user = user_check($email,$pwd);
		$user_id  = intval($user['id']);
		if ($user_id >0){
			require APP_ROOT_PATH.'app/Lib/deal.php';
			
			$root['user_login_status'] = 1;
			
			if($paypassword==""){
				$root["show_err"] = $GLOBALS['lang']['PAYPASSWORD_EMPTY'];
				output($root);
			}
		
			if(md5($paypassword)!=$GLOBALS['user_info']['paypassword']){
				$root["show_err"] = $GLOBALS['lang']['PAYPASSWORD_ERROR'];
				output($root);
			}
									
			$result = getUcRepayBorrowMoney($id,$ids);
			
			$root['status'] = $result['status'];
			if($result['status'] == 2){				
				$root['response_code'] = 1;
				$root['app_url'] = $result['jump'];
			}else{
				$root['response_code'] = $result['status'];
				$root['show_err'] = $result['show_err'];
			}
			$root["show_err"] = "还款成功";
			output($root);
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		output($root);
	}
}
?>
