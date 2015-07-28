<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_financial_statistics
{
	public function index(){
		
		$root = array();
		
		$email = strim($GLOBALS['request']['email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['pwd']);//密码
		
		$id = intval($GLOBALS['request']['id']);
		//$user_id = intval($GLOBALS['user_info']['id']);
		
		//检查用户,用户密码
		$user = user_check($email,$pwd);
		$user_id  = intval($user['id']);
		if ($user_id >0){
			require APP_ROOT_PATH.'app/Lib/deal.php';
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
			
			//充值已支付
			$root['incharge_count'] = $GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."payment_notice where  is_paid = 1 and user_id = ".$user_id." ");
			$root['incharge_count'] = format_price($root['incharge_count']);
			
			//提现成功
			$root['carry_money'] = $GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."user_carry where  status = 1 and user_id = ".$user_id." ");
			$root['carry_money'] = format_price($root['carry_money']);
			//已付管理费
			$root['true_repay_manage_money'] = $GLOBALS['db']->getOne("SELECT sum(true_repay_manage_money) FROM ".DB_PREFIX."deal_load_repay where  has_repay = 1 and user_id = ".$user_id." ");
			$root['true_repay_manage_money'] = format_price($root['true_repay_manage_money']);
			
			$user_statistics = sys_user_status($user_id);
			$root['user_statistics'] = $user_statistics;
			
			
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "理财统计";
		output($root);		
	}
}
?>
