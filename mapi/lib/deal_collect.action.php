<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class deal_collect
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
			$root['user_login_status'] = 1;
				
			$root['response_code'] = 1;
			$root['is_faved'] = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."deal_collect WHERE deal_id = ".$id." AND user_id=".$user_id);		
			
			
			
			$root['ips_bill_no'] = $GLOBALS['db']->getOne("SELECT ips_bill_no FROM ".DB_PREFIX."deal WHERE id = ".$id);
			
			if (!empty($root['ips_bill_no'])){
				//第三方托管标
				if (!empty($user['ips_acct_no'])){
					$result = GetIpsUserMoney($user_id,0);
					
					$root['user_money'] = $result['pBalance'];
				}else{
					$root['user_money'] = 0;					
				}
			}else{
				$root['user_money'] = $user['money'];
			}
			
			$root['user_money_format'] = format_price($user['user_money']);//用户金额
			
			
			$root['open_ips'] = intval(app_conf("OPEN_IPS"));
			$root['ips_acct_no'] = $user['ips_acct_no'];//当前用户是否有申请，第三方托管帐户
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		
		output($root);		
	}
}
?>
