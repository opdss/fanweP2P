<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/deal.php';
class deal_dobid
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
			
			$id = intval($GLOBALS['request']['id']);
			$deal = get_deal($id);
			$bid_money = floatval($GLOBALS['request']["bid_money"]);
			$buy_number = $GLOBALS['request']["buy_number"];
			if($deal['uloadtype'] == 1 && $buy_number > 1){
				$bid_money = $buy_number * $bid_money;
			}
			
			$bid_paypassword = strim($GLOBALS['request']['bid_paypassword']);

			
			$status = dobid2($id,$bid_money,$bid_paypassword);
			$root['status'] = $status['status'];
			if($status['status'] == 2){
				$root['response_code'] = 1;
				$root['app_url'] = $status['jump'];
			}else if($status['status'] != 1){
				$root['response_code'] = 0;
				$root['show_err'] = $status['show_err'];
			}else{
				$root['response_code'] = 1;
				$root['show_err'] = $GLOBALS['lang']['DEAL_BID_SUCCESS'];
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
