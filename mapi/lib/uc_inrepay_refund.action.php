<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_inrepay_refund
{
	public function index(){
		
		$root = array();
		
		$email = strim($GLOBALS['request']['email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['pwd']);//密码
		
		$id = intval($GLOBALS['request']['id']);
		
			
		//检查用户,用户密码
		$user = user_check($email,$pwd);
		$user_id  = intval($user['id']);
		if ($user_id >0){
			require APP_ROOT_PATH.'app/Lib/deal.php';
			
			$root['user_login_status'] = 1;

			$status = getUcInrepayRefund($id);
			if ($status['status'] == 1){
				//$deal = $status['deal'];
				$root['deal'] = $status['deal'];
				$root['true_all_manage_money'] = $status['true_all_manage_money'];					
				$root['impose_money'] = $status['impose_money'];
				$root['total_repay_money'] = $status['total_repay_money'];			
				$root['true_total_repay_money'] = $status['true_total_repay_money'];
					
				$root['true_all_manage_money_format'] = $status['true_all_manage_money_format'];
				$root['impose_money_format'] = $status['impose_money_format'];
				$root['total_repay_money_format'] = $status['total_repay_money_format'];
				$root['true_total_repay_money_format'] = $status['true_total_repay_money_format'];
			}else{				
				$root['show_err'] = $status['show_err'];
				$root['response_code'] = 0;
			}
			
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "提前还款";
		output($root);		
	}
}
?>
