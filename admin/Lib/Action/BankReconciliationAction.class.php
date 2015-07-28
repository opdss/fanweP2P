<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class BankReconciliationAction extends CommonAction{
	
	public function com_search(){
		$map = array ();
	
		if (!isset($_REQUEST['end_time']) || $_REQUEST['end_time'] == '') {
			$_REQUEST['end_time'] = to_date(get_gmtime(), 'Y-m-d');
		}
		
		
		if (!isset($_REQUEST['start_time']) || $_REQUEST['start_time'] == '') {
			$_REQUEST['start_time'] = dec_date($_REQUEST['end_time'], 7);// $_SESSION['q_start_time_7'];
		}
	
	
	
	
		$map['start_time'] = trim($_REQUEST['start_time']);
		$map['end_time'] = trim($_REQUEST['end_time']);
		$map['is_paid'] = 1;
	
		$this->assign("start_time",$map['start_time']);
		$this->assign("end_time",$map['end_time']);

	
		if ($map['start_time'] == ''){
			$this->error('开始时间 不能为空');
			exit;
		}
	
		if ($map['end_time'] == ''){
			$this->error('结束时间 不能为空');
			exit;
		}
	
		$d = explode('-',$map['start_time']);
		if (checkdate($d[1], $d[2], $d[0]) == false){
			$this->error("开始时间不是有效的时间格式:{$map['start_time']}(yyyy-mm-dd)");
			exit;
		}
	
		$d = explode('-',$map['end_time']);
		if (checkdate($d[1], $d[2], $d[0]) == false){
			$this->error("结束时间不是有效的时间格式:{$map['end_time']}(yyyy-mm-dd)");
			exit;
		}
	
		if (to_timespan($map['start_time']) > to_timespan($map['end_time'])){
			$this->error('开始时间不能大于结束时间:'.$map['start_time'].'至'.$map['end_time']);
			exit;
		}
	
		$q_date_diff = 31;
		$this->assign("q_date_diff",$q_date_diff);
		//echo abs(to_timespan($map['end_time']) - to_timespan($map['start_time'])) / 86400 + 1;
		if ($q_date_diff > 0 && (abs(to_timespan($map['end_time']) - to_timespan($map['start_time'])) / 86400  + 1 > $q_date_diff)){
			$this->error("查询时间间隔不能大于  {$q_date_diff} 天");
			exit;
		}
	
	
	
		return $map;
	}
	
	public function index()
	{
		
		
		$map = $this->com_search();
		
		//定义条件
		$where = ' is_paid = 1 ';
		
		//日期期间使用in形式，以确保能正常使用到索引
		if( isset($map['start_time']) && $map['start_time'] <> '' && isset($map['end_time']) && $map['end_time'] <> ''){
			$where .= " and pay_date in (".date_in($map['start_time'],$map['end_time']).")";
		}
		
		
		$sql = "select payment_id,pay_date, sum(money) as money from ".DB_PREFIX."payment_notice where ".$where." group by payment_id,pay_date";
		$money_list = $GLOBALS['db']->getAll($sql);
		
		$sql = "select id,name from ".DB_PREFIX."payment where is_effect = 1 or total_amount > 0";
		$payment_list = $GLOBALS['db']->getAll($sql);
		
		$date_list = explode(",",date_in($map['start_time'],$map['end_time'],false));
		
		//print_r($money_list);
		//print_r($date_list);
		
		$list = array();
		foreach($date_list as $k=>$v){
			
			$row = array();
			$row['pay_date'] = $v;
			foreach($payment_list as $pk=>$pv){
				//$payment_list[$pk]['money'] = 0;
				
				//$row[$pv['id'].'_id'] = $pv['id'];
				$money = array();
				$money['pay_id'] = $pv['id'];
				$money['pay_name'] = $pv['name'];
				$money['money'] = format_price($this->getMoney($pv['id'],$v,$money_list));
				$money['url'] = U("PaymentNotice/index",array("is_paid"=>1,'start_time'=>$v,'end_time'=>$v,'payment_id'=>$pv['id']));
				$row['pay_money'][] = $money; 
				
			}
			
			$list[] = $row;
		}
		
		//print_r($list);
		
		
		//$this->assign("default_map",$map);
		
		$this->assign("list",$list);
		parent::index();
	}
	
	public function getMoney($pay_id, $pay_date, $list)
	{
		foreach($list as $k=>$v){
			if ($v['payment_id'] == $pay_id && to_timespan($v['pay_date'],'Y-m-d') == to_timespan($pay_date,'Y-m-d')){
				return $v['money'];
			}
		}
		
		return 0;
	}
}
?>