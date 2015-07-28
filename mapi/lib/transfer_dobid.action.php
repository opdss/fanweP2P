<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/deal.php';
class transfer_dobid
{
	public function index(){
		
		$root = array();
		
		$email = strim($GLOBALS['request']['email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['pwd']);//密码
		
		//检查用户,用户密码
		$user = user_check($email,$pwd);
		$user_id  = intval($user['id']);
		if ($user_id >0){
			$root['user_login_status'] = 1;
			
			$id = intval($_REQUEST['id']);
			$paypassword = strim($GLOBALS['request']['paypassword']);
			$id = intval($GLOBALS['request']['id']);
			
			
			$status = dotrans($id,$paypassword);
			
			$root['status'] = $status['status'];
			if($status['status'] == 2){
				$root['response_code'] = 1;
				$root['app_url'] = $status['jump'];
			}else if($status['status'] != 1){
				$root['response_code'] = 0;
				$root['show_err'] = $status['show_err'];
			}else{
				$root['response_code'] = 1;
				$root['show_err'] = $status['show_err'];
				$root['id'] = $id;
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
