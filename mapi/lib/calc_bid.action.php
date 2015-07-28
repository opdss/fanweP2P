<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/deal.php';
class calc_bid
{
	public function index(){
		
		$root = array();
		
		require_once APP_ROOT_PATH."app/Lib/deal_func.php";
		
		$id = intval($GLOBALS['request']['id']);
		$minmoney = floatval($GLOBALS['request']['money']);
		$number = floatval($GLOBALS['request']['number']);
				
		$deal = $GLOBALS['cache']->get("MOBILE_DEAL_BY_ID_".$id);
		if($deal===false)
		{
			$deal = get_deal($id);
			$GLOBALS['cache']->set("MOBILE_DEAL_BY_ID_".$id,$deal,300);	
		}	
		
		$parmas = array();
		//$parmas['uloantype'] = 1;
		
		$parmas['uloantype'] =  $deal['uloadtype'];
		if($deal['uloadtype'] == 1){
			$parmas['minmoney'] = $minmoney;
			$parmas['money'] = $number;
		}else{
			$parmas['money'] = $minmoney;
		}
		
		$parmas['loantype'] = $deal['loantype'];
		$parmas['rate'] = $deal['rate'];
		$parmas['repay_time'] = $deal['repay_time'];
		$parmas['repay_time_type'] = $deal['repay_time_type'];
		$parmas['user_loan_manage_fee'] = $deal['user_loan_manage_fee'];
		$parmas['user_loan_interest_manage_fee'] = $deal['user_loan_interest_manage_fee'];
		
		$root['profit'] = bid_calculate($parmas);
		
		$root['profit'] = "¥".$root['profit'] ;
		$root['response_code'] = 1;
	
		output($root);		
	}
}
?>
