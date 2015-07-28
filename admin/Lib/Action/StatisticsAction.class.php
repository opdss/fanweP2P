<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class StatisticsAction extends CommonAction{

    public function index() {
    	if(trim($_REQUEST['search'])=="do"){
	    	$start_time = trim($_REQUEST['start_time']);
	    	if($start_time==""){
	    		$this->error("请选择开始时间");	
	    		die();
	    	}
	    	$this->assign("start_time",$start_time);
	    	$end_time = trim($_REQUEST['end_time']);
	    	
	    	if($end_time==""){
	    		$this->error("请选择结束时间");
	    		die();
	    	}
	    	
	    	$this->assign("end_time",$end_time);
	    	
	    	$start_time = to_timespan($start_time,"Y-m-d");
	    	$end_time = to_timespan($end_time,"Y-m-d");
	    	
	    	if($end_time < $start_time){
	    		$this->error("结束时间必须大于开始时间");
	    		die();
	    	}
	    	
	    	$now_time = to_timespan(to_date(TIME_UTC,"Y-m-d"),"Y-m-d");
	    	
	    	if($end_time > $now_time){
	    		$end_time = $now_time;
	    	}
	    	
	    	//开始时间跟结束时间差多少天
	    	$day = ($end_time - $start_time) /24 /3600;
	    	
	    	
	    	$list = array();
	    	$day_time = $start_time;
	    	
	    	//标分类
	    	$deal_cate = load_auto_cache("cache_deal_cate");
	    	
	    	/*foreach($deal_cate as $kk=>$vv){
	    		if(strpos($vv['name'],"智能")!==false){
	    			unset($deal_cate[$kk]);
	    		}
	    	}*/
	    	$this->assign("deal_cate",$deal_cate);
	    	
	    	//获取改时间段内所有的 还款中和 已还清的贷款
	    	//$deals = $GLOBALS['db']->getAll("SELECT id FROM ".DB_PREFIX."deal where deal_status in(4,5) and is_effect=1 and is_delete=0 and publish_wait=0 AND success_time >= $start_time and  ((loantype=1 and (success_time + repay_time*31*24*3600) >=$end_time) or (loantype=0 and (success_time + (repay_time+1)*24*3600)>=$end_time))");
	    	$deals = $GLOBALS['db']->getAll("SELECT id FROM ".DB_PREFIX."deal where deal_status in(4,5) and is_effect=1 and is_delete=0 and publish_wait=0 AND success_time >= $start_time");
	    	
	    	$temp_deals = array();
	    	
	    	require_once APP_ROOT_PATH."app/Lib/common.php";
    		require_once APP_ROOT_PATH."app/Lib/deal.php";
    		require_once APP_ROOT_PATH."system/libs/user.php";
	    	
	    	for($i = 0 ; $i<=$day; $i++){
	    		$day_date = to_date($day_time,"Y-m-d");
	    		$list[$i]['day'] = $day_time;
	    		//线上充值金额
	    		$list[$i]['online_pay'] = floatval($GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."payment_notice where is_paid = 1 and pay_date = '".$day_date."'  and payment_id not in (SELECT id from ".DB_PREFIX."payment where class_name='Otherpay') "));
	    		//线下充值金额
	    		$list[$i]['below_pay'] = floatval($GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."payment_notice where is_paid = 1 and pay_date = '".$day_date."' and payment_id in (SELECT id from ".DB_PREFIX."payment where class_name='Otherpay') "));
	    		
	    		foreach($deal_cate as $kk=>$vv){
	    			//if(strpos($vv['name'],"智能")===false)
	    				$list[$i][$vv['id']]['borrow_amount'] = floatval($GLOBALS['db']->getOne("SELECT sum(borrow_amount) FROM ".DB_PREFIX."deal where deal_status>=4 and is_delete = 0 and publish_wait = 0 and success_time between $day_time and $day_time+24*3600 and cate_id=".$vv['id']));
	    		}
	    		
	    		//投资总额[投标者]
	    		$list[$i]['load_amount'] = floatval($GLOBALS['db']->getOne("SELECT sum(self_money) FROM ".DB_PREFIX."deal_repay where create_date = '".$day_date."'"));
	    		
	    		//已获利息总额[投标者]
	    		$list[$i]['load_lixi_amount'] = floatval($GLOBALS['db']->getOne("SELECT (sum(true_interest_money)) FROM ".DB_PREFIX."deal_load_repay where repay_date = '".$day_date."'  AND has_repay=1 "));
	    		
	    		
	    		$ss_rs = floatval($GLOBALS['db']->getRow("SELECT sum(impose_money) as total_impose_money,sum(self_money) as total_self_money,sum(interest_money) as total_interest_money FROM ".DB_PREFIX."deal_load_repay where repay_date = '".$day_date."'  "));
	    		
	    		//应付本金
	    		$list[$i]['benjin_amount'] = $ss_rs['total_self_money'];
	    		//应付利息 
	    		$list[$i]['pay_lxi_amount'] = $ss_rs['total_interest_money'];
	    		//应付罚息
	    		$list[$i]['impose_amount'] = $ss_rs['total_impose_money'];
	    		
	    		//已付本金
	    		$list[$i]['has_repay_benjin_amount']=floatval($GLOBALS['db']->getOne("SELECT sum(self_money) FROM ".DB_PREFIX."deal_load_repay where repay_date = '".$day_date."'  AND has_repay=1 "));
	    		//已付利息
	    		$list[$i]['has_repay_lxi_amount']= floatval($GLOBALS['db']->getOne("SELECT sum(true_interest_money) FROM ".DB_PREFIX."deal_load_repay where repay_date = '".$day_date."' AND has_repay=1 "));
	    		//已付罚息
	    		$list[$i]['has_repay_impose_amount'] = floatval($GLOBALS['db']->getOne("SELECT (sum(impose_money)) FROM ".DB_PREFIX."deal_load_repay where repay_date ='".$day_date."' AND has_repay=1 "));;
	    		
	    		
	    		//待还本金
	    		$list[$i]['wait_repay_benjin_amount'] = floatval($GLOBALS['db']->getOne("SELECT sum(self_money) FROM ".DB_PREFIX."deal_load_repay where repay_date = '".$day_date."'  AND has_repay=0 "));
	    		//待还利息
	    		$list[$i]['wait_repay_lxi_amount'] = floatval($GLOBALS['db']->getOne("SELECT sum(interest_money) FROM ".DB_PREFIX."deal_load_repay where repay_date = '".$day_date."'  AND has_repay=0 "));
	    		//待还罚息
	    		$list[$i]['wait_repay_impose_amount'] = floatval($GLOBALS['db']->getOne("SELECT sum(impose_money) FROM ".DB_PREFIX."deal_load_repay where repay_date = '".$day_date."'  AND has_repay=0 "));
	    		
	    		
	    		//申请提现总额
	    		$list[$i]['carry'] = floatval($GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."user_carry where create_date = '".$day_date."' "));
	    		
	    		//成功提现金额
	    		$list[$i]['suc_carry'] = floatval($GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."user_carry where status=1 and create_date ='".$day_date."' "));
	    		
	    		//待投资资金
	    		$list[$i]['user_amount'] = floatval($GLOBALS['db']->getOne("SELECT sum(account_money) FROM ".DB_PREFIX."user_money_log where create_time_ymd = '".$day_date."' "));
	    		
	    		$sql_ua = "SELECT sum(account_money) FROM (SELECT * FROM " .
	    					"(SELECT * FROM ".DB_PREFIX."user_money_log WHERE id in" .
			    				" (SELECT id FROM ".DB_PREFIX."user_money_log WHERE user_id not in" .
			    						"(SELECT user_id FROM ".DB_PREFIX."user_money_log  where create_time_ymd = '".$day_date."')" .
			    				") AND create_time<".$day_time." ORDER BY id DESC) A " .
	    				" GROUP BY user_id) B ";
	    			
	    		//echo $sql_ua;die();
	    		$list[$i]['user_amount'] += floatval($GLOBALS['db']->getOne($sql_ua));
	    		
	    		$day_time +=24 * 3600;
	    	}
	    	
	    	
			
	    	
	    	$this->assign("list",$list);
    	}
    	$this->display();
    }
}
?>