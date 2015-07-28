<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/deal.php';
class transfer_show
{
	public function index(){
		
		$root = array();
		
		$id = intval($GLOBALS['request']['id']);
		$deal_id = intval($GLOBALS['request']['deal_id']);
		$email = strim($GLOBALS['request']['email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['pwd']);//密码
		
		//检查用户,用户密码
		$user = user_check($email,$pwd);
		$user_id  = intval($user['id']);
		if ($user_id >0){
			
			$root['is_faved'] = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."deal_collect WHERE deal_id = ".$id." AND user_id=".$user_id);	
		}else{
			$root['is_faved'] = 0;//0：未关注;>0:已关注
		}
		
		$root['response_code'] = 1;
		
		$condition = ' AND dlt.id='.$id.' AND d.deal_status >= 4 and d.is_effect=1 and d.is_delete=0  and d.repay_time_type =1 and  d.publish_wait=0 ';
		$union_sql = " LEFT JOIN ".DB_PREFIX."deal_load_transfer dlt ON dlt.deal_id = dl.deal_id ";
		
		$transfer = get_transfer($union_sql,$condition);
		$root['transfer'] = $transfer;
		$root['deal_id'] = $deal_id;
		
		$root['open_ips'] = intval(app_conf("OPEN_IPS"));
		$root['ips_acct_no'] = $user['ips_acct_no'];
		
		$root['ips_bill_no'] = $GLOBALS['db']->getOne("SELECT ips_bill_no FROM ".DB_PREFIX."deal WHERE id = ".$deal_id);
			
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
		
		output($root);		
	}
}
?>

