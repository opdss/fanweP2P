<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class ReferralsAction extends CommonAction{
	public function index()
	{
		$log_begin_time  = trim($_REQUEST['log_begin_time'])==''?0:to_timespan($_REQUEST['log_begin_time']);
		$log_end_time  = trim($_REQUEST['log_end_time'])==''?0:to_timespan($_REQUEST['log_end_time']);
		
		$condtion ="  ";
		if($log_begin_time > 0 && $log_end_time==0)
		{
			$condtion .= " and r.repay_time > $log_begin_time " ;	
		}
		elseif($log_begin_time == 0 && $log_end_time>0)
		{
			$condtion .= " and r.repay_time < $log_begin_time " ;	
		}
		elseif($log_begin_time > 0 && $log_end_time > 0)
			$condtion .= " and (r.repay_time between $log_begin_time and $log_end_time )" ;
		
		
		$sql_count = "SELECT count(r.id) FROM ".DB_PREFIX."referrals r WHERE 1=1 $condtion ";
		
		$count = $GLOBALS['db']->getOne($sql_count);
		
		if (! empty ( $_REQUEST ['listRows'] )) {
			$listRows = $_REQUEST ['listRows'];
		} else {
			$listRows = '';
		}
		
		$p = new Page ( $count, $listRows );
		if($count>0){
			$sql = "SELECT r.*,r.l_key +1 as l_key,u.referral_rate,dlr.true_interest_money,dlr.true_self_money,dlr.repay_date FROM ".DB_PREFIX."referrals r " .
					"LEFT JOIN ".DB_PREFIX."deal d ON d.id =r.deal_id  " .
					"LEFT JOIN ".DB_PREFIX."deal_load_repay dlr ON dlr.l_key =r.l_key AND dlr.load_id=r.load_id  " .
					"LEFT JOIN ".DB_PREFIX."user u ON u.id =r.user_id  " .
					"WHERE 1=1 $condtion ORDER BY r.id DESC LIMIT ".($p->firstRow . ',' . $p->listRows);
			
			$list = $GLOBALS['db']->getAll($sql);
			$this->assign("list",$list);
		}
	
		$page = $p->show();
		$this->assign ( "page", $page );
		
		$this->display();
	}
	public function pay()
	{
		$id = intval($_REQUEST['id']);
		$res = pay_referrals($id);
		if($res)
		{
			save_log("ID:".$id.l("REFERRALS_PAY_SUCCESS"),1);
			$this->success(l("REFERRALS_PAY_SUCCESS"));
		}
		else
		{
			save_log("ID:".$id.l("REFERRALS_PAY_FAILED"),0);
			$this->error(l("REFERRALS_PAY_FAILED"));
		}
	}
	
	function foreach_pay($page = 1){
		$log_begin_time  = trim($_REQUEST['log_begin_time'])==''?0:to_timespan($_REQUEST['log_begin_time']);
		$log_end_time  = trim($_REQUEST['log_end_time'])==''?0:to_timespan($_REQUEST['log_end_time']);
		$condtion =" AND (r.pay_time is null OR r.pay_time = 0) ";
		if($log_begin_time > 0 && $log_end_time==0)
		{
			$condtion .= " and r.repay_time > $log_begin_time " ;	
		}
		elseif($log_begin_time == 0 && $log_end_time>0)
		{
			$condtion .= " and r.repay_time < $log_begin_time " ;	
		}
		elseif($log_begin_time > 0 && $log_end_time > 0)
			$condtion .= " and (r.repay_time between $log_begin_time and $log_end_time )" ;
		
		
		$sql_count = "SELECT count(r.id) FROM ".DB_PREFIX."referrals r WHERE 1=1 $condtion ";
		
		$count = $GLOBALS['db']->getOne($sql_count);
		
		if (! empty ( $_REQUEST ['listRows'] )) {
			$listRows = $_REQUEST ['listRows'];
		} else {
			$listRows = '';
		}
		
		$p = new Page ( $count, $listRows );
		if($count>0){
			$sql = "SELECT r.id FROM ".DB_PREFIX."referrals r WHERE 1=1 $condtion ORDER BY r.id DESC LIMIT ".($p->firstRow . ',' . $p->listRows);
			
			$list = $GLOBALS['db']->getAll($sql);
			foreach($list as $k=>$v){
				if($v['pay_time'] ==  0){
					$res = pay_referrals($v['id']);
					if($res)
					{
						save_log("ID:".$v['id'].l("REFERRALS_PAY_SUCCESS"),1);
					}
					else
					{
						save_log("ID:".$v['id'].l("REFERRALS_PAY_FAILED"),0);
					}
				}
			}
		}
		
		if($p->nowPage >=  $p->totalPages){
			$this->success(l("REFERRALS_PAY_SUCCESS"));
		}
		else{
			register_shutdown_function(array(&$this, 'foreach_pay'), $page+1);
		}
		
	}
	
	
	public function export_csv($page = 1)
	{
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
	
		//定义条件
		$log_begin_time  = trim($_REQUEST['log_begin_time'])==''?0:to_timespan($_REQUEST['log_begin_time']);
		$log_end_time  = trim($_REQUEST['log_end_time'])==''?0:to_timespan($_REQUEST['log_end_time']);
		
		$condtion ="  ";
		if($log_begin_time > 0 && $log_end_time==0)
		{
			$condtion .= " and r.repay_time > $log_begin_time " ;	
		}
		elseif($log_begin_time == 0 && $log_end_time>0)
		{
			$condtion .= " and r.repay_time < $log_begin_time " ;	
		}
		elseif($log_begin_time > 0 && $log_end_time > 0)
			$condtion .= " and (r.repay_time between $log_begin_time and $log_end_time )" ;
		
		$sql = "SELECT r.*,r.l_key +1 as l_key,u.referral_rate,dlr.true_interest_money,dlr.true_self_money,dlr.repay_date FROM ".DB_PREFIX."referrals r " .
					"LEFT JOIN ".DB_PREFIX."deal d ON d.id =r.deal_id  " .
					"LEFT JOIN ".DB_PREFIX."deal_load_repay dlr ON dlr.l_key =r.l_key AND dlr.load_id=r.load_id  " .
					"LEFT JOIN ".DB_PREFIX."user u ON u.id =r.user_id  " .
					"WHERE 1=1 $condtion ORDER BY r.id DESC LIMIT ".$limit;
		
		$list = $GLOBALS['db']->getAll($sql);
	
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv'), $page+1);
				$referrals_value = array('id'=>'""','rel_user_id'=>'""','user_id'=>'""','true_self_money'=>'""','true_interest_money'=>'""','referral_rate'=>'""','referral_type'=>'""','money'=>'""','deal_id'=>'""','load_id'=>'""','l_key'=>'""','repay_date'=>'""','pay_time'=>'""');
			if($page == 1)
				$content = iconv("utf-8","gbk","编号,推荐人,投资人,本金总额,利息总额,抽成比%,返利方式,返利金额,借款,投标编号,第几期,返利时间,返利发放时间");
			$content = $content . "\n";
			foreach($list as $k=>$v)
			{
				$deal_name = D("Deal")->where("id=".$v['deal_id'])->getfield("name");
				$referrals_value = array();
				$referrals_value['id'] = iconv('utf-8','gbk','"' . $v['id'] . '"');
				$referrals_value['rel_user_id'] = iconv('utf-8','gbk','"' . get_user_name_reals($v['rel_user_id']) . '"');
				$referrals_value['user_id'] = iconv('utf-8','gbk','"' . get_user_name_reals($v['user_id']) . '"');
				$referrals_value['true_self_money'] = iconv('utf-8','gbk','"' . format_price( $v['true_self_money']) . '"');
				$referrals_value['true_interest_money'] = iconv('utf-8','gbk','"' . format_price( $v['true_interest_money']) . '"');
				$referrals_value['referral_rate'] = iconv('utf-8','gbk','"' . $v['referral_rate'] . '"');
				$referrals_value['referral_type'] = iconv('utf-8','gbk','"' . get_referral_type($v['referral_type']) . '"');
				$referrals_value['money'] = iconv('utf-8','gbk','"' . $v['money'] . '"');
				$referrals_value['deal_id'] = iconv('utf-8','gbk','"' . $deal_name . '"');
				$referrals_value['load_id'] = iconv('utf-8','gbk','"' . $v['load_id'] . '"');
				$referrals_value['l_key'] = iconv('utf-8','gbk','"' . $v['l_key'] . '"');
				$referrals_value['repay_date'] = iconv('utf-8','gbk','"' . $v['repay_date'] . '"');
				$referrals_value['pay_time'] = iconv('utf-8','gbk','"' . to_date($v['pay_time']) . '"');
				$content .= implode(",", $referrals_value) . "\n";
			}
			header("Content-Disposition: attachment; filename=referrals_list.csv");
			echo $content;
		}
		else
		{
			if($page==1)
				$this->error(L("NO_RESULT"));
		}
	}
}

function get_referral_type($type){
	if($type==1){
		return "本金";
	}
	else{
		return "利息";
	}
}
?>