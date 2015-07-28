<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class StatisticsBorrowAction extends CommonAction{

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
	
	
		$this->assign("start_time",$map['start_time']);
		$this->assign("end_time",$map['end_time']);
	
	
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
			$this->error('开始时间不能大于结束时间');
			exit;
		}
	
		$q_date_diff = 70;
		$this->assign("q_date_diff",$q_date_diff);
//		echo abs(to_timespan($map['end_time']) - to_timespan($map['start_time'])) / 86400 + 1;
		if ($q_date_diff > 0 && (abs(to_timespan($map['end_time']) - to_timespan($map['start_time'])) / 86400  + 1 > $q_date_diff)){
			$this->error("查询时间间隔不能大于  {$q_date_diff} 天");
			exit;
		}
		
		
		return $map;
	}	
	
	//投资金额
	public function tender_account_total(){
		
		$map =  $this->com_search();
		
		foreach ( $map as $key => $val ) {
			//dump($key);
			if ((!is_array($val)) && ($val <> '')){
				$parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		}
		
		$sql_str = "select		
		create_date as 时间,
		count(*) as	投资人次,
		sum(money) as 投资总额,
		sum(if(is_has_loans = 1, money,0)) as 投资成功,
		sum(if(is_has_loans = 0 and is_repay = 0, money,0)) as 冻结投资额,
		sum(if(is_has_loans = 0 and is_repay = 1, money,0)) as 投资失败,
		sum(if(is_has_loans = 1, rebate_money,0)) as 已获奖励
		
		from ".DB_PREFIX."deal_load where 1 = 1 ";
		
		
		//日期期间使用in形式，以确保能正常使用到索引
		if( isset($map['start_time']) && $map['start_time'] <> '' && isset($map['end_time']) && $map['end_time'] <> ''){
			$sql_str .= " and create_date in (".date_in($map['start_time'],$map['end_time']).")";
		}
		
		
		$sql_str .= " group by create_date ";
		
		$model = D();
		
		//echo $sql_str;
		$voList = $this->_Sql_list($model, $sql_str, "&".$parameter, '时间', false);
		//var_dump($voList);exit;
		
		//$this->assign("list",$voList);
		//$this->assign("new_sort", M("Delivery")->max("sort")+1);
		
		require('./admin/Tpl/default/Common/js/flash/php-ofc-library/open-flash-chart.php');
		
		$total_array=array(
				array(
					array('投资人次','时间','投资人次'),
					array('投资总额','时间','投资总额'),
					array('投资成功','时间','投资成功'),
					array('冻结投资额','时间','冻结投资额'),
					array('投资失败','时间','投资失败'),
					array('已获奖励','时间','已获奖励')
				),
		);
		krsort($voList);
		$chart_list=$this->get_jx_json_all($voList,$total_array);
		$this->assign("chart_list",$chart_list);
		//dump($chart_list);
		
		$this->display();		
	}
	
	//投资金额明细
	public function tender_account_info(){
		
		$begin_time  = trim($_REQUEST['begin_time'])==''?0:to_timespan($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time'])==''?0:to_timespan($_REQUEST['end_time']);
		$time=trim($_REQUEST['time']);
		if(trim($_REQUEST['time'])){
			$condtion = " where  (a.create_date = '$time')";
		}else{
			$condtion = " where 1=1 ";
		}
		
		if(trim($_REQUEST['id'])!='')
		{
			$id=intval(trim($_REQUEST['id']));
		}
		if(trim($_REQUEST['user_name'])!='')
		{
			$user_name= trim($_REQUEST['user_name']);
		}
		if(trim($_REQUEST['deal_sn'])!='')
		{
			$deal_sn= trim($_REQUEST['deal_sn']);
		}
		if(trim($_REQUEST['sub_name'])!='')
		{
			$sub_name= trim($_REQUEST['sub_name']);
		}
		if(trim($_REQUEST['is_has_loans'])!='')
		{
			$is_has_loans= trim($_REQUEST['is_has_loans']);
		}
		
		
		$sql_str = "select 
		a.id as 投资ID,
		a.user_id as	投资人,
		a.money as	投资金额,
		case 
		 when a.is_has_loans = 1 then '成功'
		 when a.is_has_loans = 0 and a.is_repay = 0  then '冻结'
		when a.is_repay = 1  then '失败'
		else ''
		end 投资状态,
		if((select count(*) from ".DB_PREFIX."deal_load_transfer t where t.user_id != t.t_user_id and t.t_user_id > 0 and t.deal_id = a.deal_id and t.load_id = a.id) > 0, '是','否') as 是否转让,
		FROM_UNIXTIME(a.create_time + 28800, '%Y-%m-%d %H:%i:%S') as 投资时间,
		b.sub_name as 借款标题,
		b.deal_sn as 借款编号,
		b.borrow_amount as	借款总额,
		if(is_auto = 1,'是','否') as	自动投标
		from ".DB_PREFIX."deal_load as a LEFT JOIN ".DB_PREFIX."deal as b on b.id = a.deal_id $condtion  ";
		
		if($id){
			$sql_str="$sql_str  and a.id = '$id'";
		}
		if($user_name){
			$sql_str="$sql_str and a.user_name like '%$user_name%'";
		}
		if($deal_sn){
			$sql_str="$sql_str and b.deal_sn like '%$deal_sn%'";
		}
		if($sub_name){
			$sql_str="$sql_str and b.sub_name like '%$sub_name%'";
		}
		
		if(isset($_REQUEST['is_has_loans'])){
			if($is_has_loans==4){
				$sql_str="$sql_str";
			}elseif($is_has_loans==1){
				$sql_str="$sql_str and a.is_has_loans = 1 ";
			}elseif($is_has_loans==2){
				$sql_str="$sql_str and a.is_has_loans = 0 and a.is_repay = 0 ";
			}elseif($is_has_loans==3){
				$sql_str="$sql_str and a.is_repay = 1 ";
			}
			
		}
		
		if($begin_time > 0 || $end_time > 0){
			if($begin_time>0 && $end_time==0){
				$sql_str = "$sql_str and (a.create_time > $begin_time)";
			}elseif($begin_time==0 && $end_time>0){
				$sql_str = "$sql_str and (a.create_time < $end_time )";
			}elseif($begin_time >0 && $end_time>0){
				$sql_str = "$sql_str and (a.create_time between $begin_time and $end_time )";
			}
			
		}
		
		$model = D();
		//echo $sql_str;
		$voList = $this->_Sql_list($model, $sql_str, '时间', false);
		
		$this->display();		
	}
	
	//已回款
	public function tender_hasback_total(){
		
		$map =  $this->com_search();
		
		foreach ( $map as $key => $val ) {
			//dump($key);
			if ((!is_array($val)) && ($val <> '')){
				$parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		}
		
		$sql_str = "select 
		true_repay_date as 时间,
		sum(repay_money + impose_money - manage_money) as	投资者回款总额,
		sum(self_money) as	投资者回款本金,
		sum(repay_money - self_money) as 投资者回款利息, 	 	 
		sum(if(status = 0, impose_money,0)) as 提前还款罚息,
		sum(if(status = 2 or status = 3, impose_money,0)) as 逾期还款罚金,
		sum(manage_money) as 投资者付管理费,
		sum(repay_manage_money + repay_manage_impose_money) as 借款者付管理费,
		sum(manage_money + repay_manage_money + repay_manage_impose_money) as 平台收入,
		count(*) as 收款人次
		from ".DB_PREFIX."deal_load_repay as a where has_repay = 1 ";
		
		//日期期间使用in形式，以确保能正常使用到索引
		if( isset($map['start_time']) && $map['start_time'] <> '' && isset($map['end_time']) && $map['end_time'] <> ''){
			$sql_str .= " and true_repay_date in (".date_in($map['start_time'],$map['end_time']).")";
		}
		
		$sql_str .= "  group by true_repay_date";
		$model = D();
		
		//echo $sql_str;
		$voList = $this->_Sql_list($model, $sql_str, "&".$parameter, '时间', false);
		//var_dump($voList);exit;
		
		require('./admin/Tpl/default/Common/js/flash/php-ofc-library/open-flash-chart.php');
		
		$total_array=array(
				array(
					array('投资者回款总额','时间','投资者回款总额'),
					array('投资者回款本金','时间','投资者回款本金'),
					array('投资者回款利息','时间','投资者回款利息'),
					array('提前还款罚息','时间','提前还款罚息'),
					array('逾期还款罚金','时间','逾期还款罚金'),
					array('投资者付管理费','时间','投资者付管理费'),
					array('借款者付管理费','时间','借款者付管理费'),
					array('平台收入','时间','平台收入'),
					array('收款人次','时间','收款人次')
				),
		);
		krsort($voList);
		$chart_list=$this->get_jx_json_all($voList,$total_array);
		$this->assign("chart_list",$chart_list);
		//dump($chart_list);
		
		$this->display();		
	}
	
	//已回款明细
	public function tender_hasback_info(){
		
		$begin_time  = trim($_REQUEST['begin_time'])==''?0:to_timespan($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time'])==''?0:to_timespan($_REQUEST['end_time']);
		//$condtion = "  (a.repay_time between $start_time and $end_time )";
		$time=trim($_REQUEST['time']);
		if(trim($_REQUEST['time'])){
			$condtion = " and  (a.true_repay_date = '$time')";
		}
		
		if(trim($_REQUEST['user_name'])!='')
		{
			$user_name= trim($_REQUEST['user_name']);
		}
		if(trim($_REQUEST['deal_sn'])!='')
		{
			$deal_sn= trim($_REQUEST['deal_sn']);
		}
		if(trim($_REQUEST['sub_name'])!='')
		{
			$sub_name= trim($_REQUEST['sub_name']);
		}
		if(trim($_REQUEST['status'])!='')
		{
			$status= trim($_REQUEST['status']);
		}
		
		if(trim($_REQUEST['cate_id'])!='')
		{
			$cate_id= trim($_REQUEST['cate_id']);
		}
		$this->assign("cate_list",M("DealCate")->where('is_effect = 1 and is_delete = 0 order by sort')->findAll());
		
		
		$sql_str = "select 
		u.id as 收款人,
		d.deal_sn as 贷款号,
		d.sub_name as 借款标题,
		c.`name` as 借款类型,
		a.repay_money as 还款本息,
		a.impose_money as 投资者罚息收入,
		a.manage_money as 投资者付管理费,
		a.repay_manage_money + a.repay_manage_impose_money as 借款者付管理费,
		if (datediff(a.true_repay_date,FROM_UNIXTIME(a.repay_time + 28800, '%Y-%m-%d')) > 0, 
		datediff(a.true_repay_date,FROM_UNIXTIME(a.repay_time + 28800, '%Y-%m-%d')),
		0) as 逾期天数, 
		FROM_UNIXTIME(a.repay_time + 28800, '%Y-%m-%d') as  应收时间,
		 a.true_repay_date as	实收时间,
		 a.repay_money + a.impose_money - a.manage_money as	投资者实收总额,	
		a.manage_money + a.repay_manage_money + a.repay_manage_impose_money as 平台收入,
		if(has_repay = 1,
		case status
		when 0 then '提前收款'
		when 1 then '准时收款'
		when 2 then '逾期收款'
		when 3 then '严重逾期收款'
		else 
		 '已收款'
		end
		,'未收款') as 状态,
		a.deal_id as deal_id
		 from ".DB_PREFIX."deal_load_repay as a 
		LEFT JOIN ".DB_PREFIX."user u on u.id = a.user_id
		LEFT JOIN ".DB_PREFIX."deal d on d.id = a.deal_id
		LEFT JOIN ".DB_PREFIX."deal_cate c on c.id = d.cate_id
		where a.has_repay = 1 $condtion ";
		
		if($user_name){
			$sql_str="$sql_str and u.user_name like '%$user_name%'";
		}
		if($deal_sn){
			$sql_str="$sql_str and d.deal_sn like '%$deal_sn%'";
		}
		if($sub_name){
			$sql_str="$sql_str and d.sub_name like '%$sub_name%'";
		}
		
		if($cate_id){
			$sql_str="$sql_str and c.id = '$cate_id'";
		}
		
		if(isset($_REQUEST['status'])){
			if($status==5){
				$sql_str="$sql_str";
			}elseif($status==1){
				$sql_str="$sql_str and status = 0 ";
			}elseif($status==2){
				$sql_str="$sql_str and status = 1 ";
			}elseif($status==3){
				$sql_str="$sql_str and status = 2 ";
			}elseif($status==4){
				$sql_str="$sql_str and status = 3 ";
			}
			
		}
		
		if($begin_time > 0 || $end_time > 0){
			if($begin_time>0 && $end_time==0){
				$sql_str = "$sql_str and (a.true_repay_time > $begin_time)";
			}elseif($begin_time==0 && $end_time>0){
				$sql_str = "$sql_str and (a.true_repay_time < $end_time )";
			}elseif($begin_time >0 && $end_time>0){
				$sql_str = "$sql_str and (a.true_repay_time between $begin_time and $end_time )";
			}
			
		}
		
		$model = D();
		
		//echo $sql_str;
		$voList = $this->_Sql_list($model, $sql_str, '时间', false);
		
		$this->display();			
	}
	
	//待收款
	public function tender_tobe_receivables(){
		
		$map =  $this->com_search();
		
		foreach ( $map as $key => $val ) {
			//dump($key);
			if ((!is_array($val)) && ($val <> '')){
				$parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		}
		
		$sql_str = "select 
		repay_date as 时间,
		sum(repay_money + impose_money - manage_money) as	待收总额,
		sum(self_money) as	待收本金,
		sum(repay_money - self_money) as 待收利息, 	 	 
		count(*) as 待收款人次

		from ".DB_PREFIX."deal_load_repay as a where has_repay = 0 ";
		
		//日期期间使用in形式，以确保能正常使用到索引
		if( isset($map['start_time']) && $map['start_time'] <> '' && isset($map['end_time']) && $map['end_time'] <> ''){
			$sql_str .= " and repay_date in (".date_in($map['start_time'],$map['end_time']).")";
		}
		
		$sql_str .= "  group by repay_date";
		$model = D();
		
		//echo $sql_str;
		$voList = $this->_Sql_list($model, $sql_str, "&".$parameter, '时间', false);
		
		require('./admin/Tpl/default/Common/js/flash/php-ofc-library/open-flash-chart.php');
		
		$total_array=array(
				array(
					array('待收总额','时间','待收总额'),
					array('待收本金','时间','待收本金'),
					array('待收利息','时间','待收利息'),
					array('待收款人次','时间','待收款人次')
				),
		);
		krsort($voList);
		$chart_list=$this->get_jx_json_all($voList,$total_array);
		$this->assign("chart_list",$chart_list);
		//dump($chart_list);
		
		$this->display();		
	}
	
	//待收款明细
	public function tender_tobe_receivablesinfo(){
		
		$begin_time  = trim($_REQUEST['begin_time'])==''?0:to_timespan($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time'])==''?0:to_timespan($_REQUEST['end_time']);
		//$condtion = "  (a.repay_time between $start_time and $end_time )";
		$time=trim($_REQUEST['time']);
		if(trim($_REQUEST['time'])){
			$condtion = " and  (a.repay_date = '$time')";
		}
		
		if(trim($_REQUEST['user_name'])!='')
		{
			$user_name= trim($_REQUEST['user_name']);
		}
		if(trim($_REQUEST['deal_sn'])!='')
		{
			$deal_sn= trim($_REQUEST['deal_sn']);
		}
		if(trim($_REQUEST['sub_name'])!='')
		{
			$sub_name= trim($_REQUEST['sub_name']);
		}
		if(trim($_REQUEST['cate_id'])!='')
		{
			$cate_id= trim($_REQUEST['cate_id']);
		}
		
		//$model = D();
		//echo $sql_str;
		$this->assign("cate_list",M("DealCate")->where('is_effect = 1 and is_delete = 0 order by sort')->findAll());
		$now_day=to_date(TIME_UTC,"Y-m-d");
		$sql_str = "select 
		u.id as 收款人,
		d.deal_sn as	贷款号,
		d.sub_name as 借款标题,
		c.`name` as 借款类型,
		a.repay_money as 还款本息,
		if (datediff($now_day ,FROM_UNIXTIME(a.repay_time + 28800, '%Y-%m-%d')) > 0, 
		datediff($now_day,FROM_UNIXTIME(a.repay_time + 28800, '%Y-%m-%d')),
		0) as 逾期天数, 
		FROM_UNIXTIME(a.repay_time + 28800, '%Y-%m-%d') as  应收时间,
		'未收款' as 状态,
		a.deal_id as deal_id
		 from ".DB_PREFIX."deal_load_repay as a 
		LEFT JOIN ".DB_PREFIX."user u on u.id = a.user_id
		LEFT JOIN ".DB_PREFIX."deal d on d.id = a.deal_id
		LEFT JOIN ".DB_PREFIX."deal_cate c on c.id = d.cate_id
		where a.has_repay = 0  $condtion ";
		
		if($user_name){
			$sql_str="$sql_str and u.user_name like '%$user_name%'";
		}
		if($deal_sn){
			$sql_str="$sql_str and d.deal_sn like '%$deal_sn%'";
		}
		if($sub_name){
			$sql_str="$sql_str and d.sub_name like '%$sub_name%'";
		}
		
		if($sub_name){
			$sql_str="$sql_str and d.sub_name like '%$sub_name%'";
		}
		
		if($cate_id){
			$sql_str="$sql_str and c.id = '$cate_id'";
		}
		
		if($begin_time > 0 || $end_time > 0){
			if($begin_time>0 && $end_time==0){
				$sql_str = "$sql_str and (a.repay_time > $begin_time)";
			}elseif($begin_time==0 && $end_time>0){
				$sql_str = "$sql_str and (a.repay_time < $end_time )";
			}elseif($begin_time >0 && $end_time>0){
				$sql_str = "$sql_str and (a.repay_time between $begin_time and $end_time )";
			}
			
		}
		
		$model = D();
		//echo $sql_str;
		$voList = $this->_Sql_list($model, $sql_str, '时间', false);
		
		$this->display();			
	}
	
	//标种投资
	public function tender_borrow_type(){
		
		$map =  $this->com_search();
		
		foreach ( $map as $key => $val ) {
			//dump($key);
			if ((!is_array($val)) && ($val <> '')){
				$parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		}
		
		$cate_list = $GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."deal_cate where is_effect = 1 and is_delete = 0 order by sort");
		$sql_str = "select a.create_date as 时间,";
		
		/*
		$total_array=array(
				array(
					array('信用认证标','时间','信用认证标'),
					array('实地认证标','时间','实地认证标'),
					array('机构担保标','时间','机构担保标'),
					array('智能理财标','时间','智能理财标'),
					array('旅游考察标','时间','旅游考察标'),
					array('抵押标','时间','抵押标'),
					array('成功总人次','时间','成功总人次')
				),
		);
		*/
		$item_array = array();
		foreach ( $cate_list as $key => $val ) {
			$sql_str .= "sum(if( b.cate_id = ".$val['id'].", 1, 0)) as ".$val['name'].",";
		
			$item_array[] = array($val['name'],'时间',$val['name']);
		}
		
		$item_array[] = array('成功总人次','时间','成功总人次');
		
		$total_array=array($item_array);
		
		$sql_str .= " count(*) as 成功总人次
		from ".DB_PREFIX."deal_load a LEFT JOIN ".DB_PREFIX."deal b on b.id = a.deal_id where a.is_has_loans = 1 ";
		
		//日期期间使用in形式，以确保能正常使用到索引
		if( isset($map['start_time']) && $map['start_time'] <> '' && isset($map['end_time']) && $map['end_time'] <> ''){
			$sql_str .= " and a.create_date in (".date_in($map['start_time'],$map['end_time']).")";
		}
		
		$sql_str .= "  group by a.create_date";
		$model = D();
		
		//echo $sql_str;
		$voList = $this->_Sql_list($model, $sql_str, "&".$parameter, '时间', false);
		
		//var_dump($voList);
		
		require('./admin/Tpl/default/Common/js/flash/php-ofc-library/open-flash-chart.php');
		krsort($voList);
		$chart_list=$this->get_jx_json_all($voList,$total_array);
		$this->assign("chart_list",$chart_list);
		//dump($chart_list);
		
		$this->display();		
	}
	
	//投资人数
	public function tender_usernum_total(){
		
		$map =  $this->com_search();
		
		foreach ( $map as $key => $val ) {
			//dump($key);
			if ((!is_array($val)) && ($val <> '')){
				$parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		}
		
		$sql_str = "select create_date as 时间, count(DISTINCT user_id) as 投资用户数, sum(money) as 投资统计 
		from ".DB_PREFIX."deal_load  where is_has_loans = 1 ";
		
		//日期期间使用in形式，以确保能正常使用到索引
		if( isset($map['start_time']) && $map['start_time'] <> '' && isset($map['end_time']) && $map['end_time'] <> ''){
			$sql_str .= " and create_date in (".date_in($map['start_time'],$map['end_time']).")";
		}
		
		$sql_str .= "  group by create_date ";
		$model = D();
		
		//echo $sql_str;
		$voList = $this->_Sql_list($model, $sql_str, "&".$parameter, '时间', false);
		
		require('./admin/Tpl/default/Common/js/flash/php-ofc-library/open-flash-chart.php');
		
		$total_array=array(
				array(array('投资用户数','时间','投资用户数'),array('投资统计','时间','投资统计')),
		);
		krsort($voList);
		$chart_list=$this->get_jx_json_all($voList,$total_array);
		$this->assign("chart_list",$chart_list);
		//dump($chart_list);
		
		$this->display();		
	}
	
	//投资人数明细
	public function tender_usernum_info(){
		
		$begin_time  = trim($_REQUEST['begin_time'])==''?0:to_timespan($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time'])==''?0:to_timespan($_REQUEST['end_time']);
		
		$time=trim($_REQUEST['time']);
		if(trim($_REQUEST['time'])){
			$condtion = " and  (a.create_date = '$time')";
		}
		
		if(trim($_REQUEST['user_name'])!='')
		{
			$user_name= trim($_REQUEST['user_name']);
		}
		
		
		$sql_str = "select  
		u.id as 用户名, 
		a.money as 投资金额, 
		FROM_UNIXTIME(a.create_time + 28800, '%Y-%m-%d %H:%i:%S') as 时间 
		from ".DB_PREFIX."deal_load a  
		left join ".DB_PREFIX."user u on u.id = a.user_id
		where a.is_has_loans = 1  $condtion  ";
		
		if($user_name){
			$sql_str="$sql_str and u.user_name like '%$user_name%'";
		}
		
		if($begin_time > 0 || $end_time > 0){
			if($begin_time>0 && $end_time==0){
				$sql_str = "$sql_str and (a.create_time > $begin_time)";
			}elseif($begin_time==0 && $end_time>0){
				$sql_str = "$sql_str and (a.create_time < $end_time )";
			}elseif($begin_time >0 && $end_time>0){
				$sql_str = "$sql_str and (a.create_time between $begin_time and $end_time )";
			}
			
		}
		
		$model = D();
		
		//echo $sql_str;
		$voList = $this->_Sql_list($model, $sql_str, '时间', false);
		
		$this->display();		
	}
	
	
	//借出总统计
	public function tender_total(){
		$begin_time  = trim($_REQUEST['begin_time'])==''?0:to_timespan($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time'])==''?0:to_timespan($_REQUEST['end_time']);
		
		$sql_str = "select 
		count(DISTINCT user_id) as 投资人数,
		sum(self_money) as 成功投资金额, 
		(select ifnull(sum(rebate_money),0) from ".DB_PREFIX."deal_load m where m.is_has_loans = 1 and m.is_rebate = 1 ) as 奖励总额,
		sum(if(has_repay = 0, repay_money,0)) as 待收总额,
		sum(if(has_repay = 0, self_money,0)) as 待收本金总额,
		sum(if(has_repay = 0, repay_money - self_money,0)) as 待收利润总额,
		sum(if(has_repay = 1, repay_money,0)) as 已收总额,
		sum(if(has_repay = 1, self_money,0)) as 已收本金总额,
		sum(if(has_repay = 1, repay_money - self_money,0)) as 已收利润总额,
		sum(if(has_repay = 1 and status = 0, impose_money,0)) as 提前还款罚息总额,
		sum(if(has_repay = 1 and (status = 2 or status = 3), impose_money,0)) as 逾期还款罚金总额
		from ".DB_PREFIX."deal_load_repay as a where 1 = 1   ";
		
		if($begin_time > 0 || $end_time > 0){
			if($begin_time>0 && $end_time==0){
				$sql_str .= " and (a.repay_time > $begin_time)";
			}elseif($begin_time==0 && $end_time>0){
				$sql_str .= " and (a.repay_time < $end_time )";
			}elseif($begin_time >0 && $end_time>0){
				$sql_str .= " and (a.repay_time between $begin_time and $end_time )";
			}
			
		}
		
		$model = D();
		$voList = $this->_Sql_list($model, $sql_str, '时间', false);		

		$this->display();		
	}
	//所有投资人
	public function tender_total_info()
	{
		$begin_time  = trim($_REQUEST['begin_time'])==''?0:$_REQUEST['begin_time'];
		$end_time  = trim($_REQUEST['end_time'])==''?0:$_REQUEST['end_time'];
		
		$sql_str = "select 
			(select u.id from ".DB_PREFIX."user u where u.id=a.user_id) as 投资人,
			sum(self_money) as 成功投资金额, 
			(select ifnull(sum(rebate_money),0) from ".DB_PREFIX."deal_load m where m.is_has_loans = 1 and m.is_rebate = 1 and m.user_id = 
			a.user_id) as 奖励总额,
			sum(if(has_repay = 0, repay_money,0)) as 待收总额,
			sum(if(has_repay = 0, self_money,0)) as 待收本金总额,
			sum(if(has_repay = 0, repay_money - self_money,0)) as 待收利润总额,
			sum(if(has_repay = 1, repay_money,0)) as 已收总额,
			sum(if(has_repay = 1, self_money,0)) as 已收本金总额,
			sum(if(has_repay = 1, repay_money - self_money,0)) as 已收利润总额,
			sum(if(has_repay = 1 and status = 0, impose_money,0)) as 提前还款罚息总额,
			sum(if(has_repay = 1 and (status = 2 or status = 3), impose_money,0)) as 逾期还款罚金总额
			from ".DB_PREFIX."deal_load_repay as a left join ".DB_PREFIX."user as u on u.id=a.user_id where 1 = 1 ";
		if(trim($_REQUEST['user_name'])!='')
		{
			$sql_str .= " and u.user_name like '%".trim($_REQUEST['user_name'])."%'  ";	
		}
		
		if($begin_time > 0 || $end_time > 0){
			if($begin_time>0 && $end_time==0){
				$sql_str .= " and (a.repay_date > '$begin_time')";
			}elseif($begin_time==0 && $end_time>0){
				$sql_str .= " and (a.repay_date < '$end_time' )";
			}elseif($begin_time >0 && $end_time>0){
				$sql_str .= " and (a.repay_date between '$begin_time' and '$end_time' )";
			}
			
		}
		
		$sql_str .= "  group by user_id ";
		$model = D();
		//echo $sql_str;
		$voList = $this->_Sql_list($model, $sql_str, '时间', false);
		
		$this->display();
		
	}
	//投资排名
	public function tender_rank_list(){
		
		$map =  $this->com_search();
		
		foreach ( $map as $key => $val ) {
			//dump($key);
			if ((!is_array($val)) && ($val <> '')){
				$parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		}
		
		$sql_str = "SELECT
			(@rowNO := @rowNo + 1) AS 排名,
			c.投资人,
			c.成功投资总额
		FROM
			(
				SELECT
					a.user_id AS 投资人,
					sum(money) AS 成功投资总额
				FROM
					".DB_PREFIX."deal_load AS a,
					(SELECT @rowNO := 0) b
				WHERE
					a.is_has_loans = 1
			";	
		
		//日期期间使用in形式，以确保能正常使用到索引
		if( isset($map['start_time']) && $map['start_time'] <> '' && isset($map['end_time']) && $map['end_time'] <> ''){
			$sql_str .= " and create_date in (".date_in($map['start_time'],$map['end_time']).")";
		}
		
		$sql_str .= " GROUP BY a.user_id order by 成功投资总额  desc) c  ";
		
		$model = D();
		
		//echo $sql_str;
		
		$voList = $this->_Sql_list($model, $sql_str, '时间', false);

		$this->display();		
	}
	
	//投资额比例
	public function tender_account_ratio(){
		
		$map =  $this->com_search();
		
		foreach ( $map as $key => $val ) {
			//dump($key);
			if ((!is_array($val)) && ($val <> '')){
				$parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		}
		
		$sql_str = "select create_date as 时间,
		sum(if(money < 5000, 1, 0)) as 5千以下,
		sum(if(money >= 5000 and money < 10000, 1, 0)) as 5千至1万,
		sum(if(money >= 10000 and money < 50000, 1, 0)) as 1至5万,
		sum(if(money >= 50000 and money < 100000, 1, 0)) as 5至10万,
		sum(if(money >= 100000 and money < 200000, 1, 0)) as 10至20万,
		sum(if(money >= 200000 and money < 500000, 1, 0)) as 20至50万,
		sum(if(money >= 500000, 1, 0)) as 50万以上,
		count(*) as 成功总人次
		from ".DB_PREFIX."deal_load where is_has_loans = 1 ";
		
		//日期期间使用in形式，以确保能正常使用到索引
		if( isset($map['start_time']) && $map['start_time'] <> '' && isset($map['end_time']) && $map['end_time'] <> ''){
			$sql_str .= " and create_date in (".date_in($map['start_time'],$map['end_time']).")";
		}
		
		$sql_str .= " GROUP BY create_date ";
		$model = D();
		
		//echo $sql_str;
		$voList = $this->_Sql_list($model, $sql_str, "&".$parameter, '时间', false);
		
		require('./admin/Tpl/default/Common/js/flash/php-ofc-library/open-flash-chart.php');
		
		$total_array=array(
				array(
					array('5千以下','时间','5千以下'),
					array('5千至1万','时间','5千至1万'),
					array('1至5万','时间','1至5万'),
					array('5至10万','时间','5至10万'),
					array('10至20万','时间','10至20万'),
					array('20至50万','时间','20至50万'),
					array('50万以上','时间','50万以上'),
					array('成功总人次','时间','成功总人次')
				),
		);
		krsort($voList);
		$chart_list=$this->get_jx_json_all($voList,$total_array);
		$this->assign("chart_list",$chart_list);
		//dump($chart_list);
		
		$this->display();		
	}
	
	//投资金额导出
	public function export_csv_account_total($page = 1)
	{
		//定义条件
		$map =  $this->com_search();
		foreach ( $map as $key => $val ) {
			//dump($key);
			if ((!is_array($val)) && ($val <> '')){
				$parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		}
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
		
		$sql_str = "select		
		create_date as time,
		count(*) as	investment_num,
		sum(money) as investment_total,
		sum(if(is_has_loans = 1, money,0)) as success_investment,
		sum(if(is_has_loans = 0 and is_repay = 0, money,0)) as freeze_investment,
		sum(if(is_has_loans = 0 and is_repay = 1, money,0)) as failure_investment,
		sum(if(is_has_loans = 1, rebate_money,0)) as has_reward
		
		from ".DB_PREFIX."deal_load where 1 = 1 ";
		
		//日期期间使用in形式，以确保能正常使用到索引
		if( isset($map['start_time']) && $map['start_time'] <> '' && isset($map['end_time']) && $map['end_time'] <> ''){
			$sql_str .= " and create_date in (".date_in($map['start_time'],$map['end_time']).")";
		}
//		
//		 echo $_REQUEST['start_time'];
//		 exit;
//		 
//		$begin_time  = trim($_REQUEST['start_time'])==''?0:$_REQUEST['start_time'];
//		$end_time  = trim($_REQUEST['end_time'])==''?0:$_REQUEST['end_time'];
//		
//		if($begin_time > 0 || $end_time > 0){
//			if($begin_time>0 && $end_time==0){
//				$sql_str .= " and (create_date > '$begin_time')";
//			}elseif($begin_time==0 && $end_time>0){
//				$sql_str .= " and (create_date < '$end_time' )";
//			}elseif($begin_time >0 && $end_time>0){
//				$sql_str .= " and (create_date between '$begin_time' and '$end_time' )";
//			}
//		}
		$sql_str .= " group by create_date limit $limit ";
		
		$list = array();
		$list = $GLOBALS['db']->getAll($sql_str);
		
//		echo $sql_str;
//		exit;
//		var_dump($list);exit;
		
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv_account_total'), $page+1);
			
			$account_value = array('time'=>'""','investment_num'=>'""','investment_total'=>'""','success_investment'=>'""','freeze_investment'=>'""','failure_investment'=>'""','has_reward'=>'""');
			if($page == 1)
	    	$content = iconv("utf-8","gbk","时间,投资人次,投资总额,投资成功,冻结投资额,投资失败,已获奖励");
	    	  
	    	if($page == 1) 	
	    	$content = $content . "\n";
	    	
	    	foreach($list as $k=>$v)
			{	
				$account_value = array();
				$account_value['time'] = iconv('utf-8','gbk','"' . $v['time'] . '"');
				$account_value['investment_num'] = iconv('utf-8','gbk','"' . $v['investment_num'] . '"');
				$account_value['investment_total'] = iconv('utf-8','gbk','"' . number_format($v['investment_total'],2) . '"');
				$account_value['success_investment'] = iconv('utf-8','gbk','"' . number_format($v['success_investment'],2) . '"');
				$account_value['freeze_investment'] = iconv('utf-8','gbk','"' . number_format($v['freeze_investment'],2) . '"');
				$account_value['failure_investment'] = iconv('utf-8','gbk','"' . number_format($v['failure_investment'],2) . '"');
				$account_value['has_reward'] = iconv('utf-8','gbk','"' . number_format($v['has_reward'],2) . '"');
				
				$content .= implode(",", $account_value) . "\n";
			}	
			
			
			header("Content-Disposition: attachment; filename=account_total_list.csv");
	    	echo $content;  
		}
		else
		{
			if($page==1)
			$this->error(L("NO_RESULT"));
		}
		
	}
	
	//投资金额明细导出
	public function export_csv_account_info($page = 1){
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
		
		$begin_time  = trim($_REQUEST['begin_time'])==''?0:to_timespan($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time'])==''?0:to_timespan($_REQUEST['end_time']);
		$time=trim($_REQUEST['time']);
		if(trim($_REQUEST['time'])){
			$condtion = " where  (a.create_date = '$time')";
		}else{
			$condtion = " where 1=1 ";
		}
		
		if(trim($_REQUEST['id'])!='')
		{
			$id=intval(trim($_REQUEST['id']));
		}
		if(trim($_REQUEST['user_name'])!='')
		{
			$user_name= trim($_REQUEST['user_name']);
		}
		if(trim($_REQUEST['deal_sn'])!='')
		{
			$deal_sn= trim($_REQUEST['deal_sn']);
		}
		if(trim($_REQUEST['sub_name'])!='')
		{
			$sub_name= trim($_REQUEST['sub_name']);
		}
		if(trim($_REQUEST['is_has_loans'])!='')
		{
			$is_has_loans= trim($_REQUEST['is_has_loans']);
		}
		
		
		
		$sql_str = "select 
		a.id as tzID,
		a.user_name as	tzr,
		a.money as	tzje,
		case 
		 when a.is_has_loans = 1 then '成功'
		 when a.is_has_loans = 0 and a.is_repay = 0  then '冻结'
		when a.is_repay = 1  then '失败'
		else ''
		end tzzt,
		if((select count(*) from ".DB_PREFIX."deal_load_transfer t where t.user_id != t.t_user_id and t.t_user_id > 0 and t.deal_id = a.deal_id and t.load_id = a.id) > 0, '是','否') as sfzr,
		FROM_UNIXTIME(a.create_time + 28800, '%Y-%m-%d %H:%i:%S') as tzsj,
		b.sub_name as jkbt,
		b.deal_sn as jkbh,
		b.borrow_amount as	jkze,
		if(is_auto = 1,'是','否') as	zdtb
		from ".DB_PREFIX."deal_load as a LEFT JOIN ".DB_PREFIX."deal as b on b.id = a.deal_id $condtion  ";
		
		if($id){
			$sql_str.=" and a.id = '$id'";
		}
		if($user_name){
			$sql_str.=" and a.user_name like '%$user_name%'";
		}
		if($deal_sn){
			$sql_str.=" and b.deal_sn like '%$deal_sn%'";
		}
		if($sub_name){
			$sql_st.=" and b.sub_name like '%$sub_name%'";
		}
		
		if(isset($_REQUEST['is_has_loans'])){
			if($is_has_loans==4){
				
			}elseif($is_has_loans==1){
				$sql_str.=" and a.is_has_loans = 1 ";
			}elseif($is_has_loans==2){
				$sql_str.=" and a.is_has_loans = 0 and a.is_repay = 0 ";
			}elseif($is_has_loans==3){
				$sql_str.=" and a.is_repay = 1 ";
			}
			
		}
		
		if($begin_time > 0 || $end_time > 0){
			if($begin_time>0 && $end_time==0){
				$sql_str.= " and (a.create_time > $begin_time)";
			}elseif($begin_time==0 && $end_time>0){
				$sql_str.= " and (a.create_time < $end_time )";
			}elseif($begin_time >0 && $end_time>0){
				$sql_str.= " and (a.create_time between $begin_time and $end_time )";
			}
			
		}
		
		$sql_str.=" limit $limit";
		
		$list = array();
		$list = $GLOBALS['db']->getAll($sql_str);
		
//		echo $sql_str;
//		exit;
//		var_dump($list);exit;
		
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv_account_info'), $page+1);
			
			$account_info_value = array(
									'tzID'=>'""',
									'tzr'=>'""',
									'tzje'=>'""',
									'tzzt'=>'""',
									'sfzr'=>'""',
									'tzsj'=>'""',
									'jkbt'=>'""',
									'jkbh'=>'""',
									'jkze'=>'""',
									'zdtb'=>'""'
									);
			if($page == 1)
	    	$content_account_info = iconv("utf-8","gbk","投资ID,投资人,投资金额,投资状态,是否转让,投资时间,借款标题,借款编号,借款总额,自动投标");
	    	  
	    	if($page == 1) 	
	    	$content_account_info = $content_account_info . "\n";
	    	
	    	foreach($list as $k=>$v)
			{	
				$account_info_value = array();
				$account_info_value['tzID'] = iconv('utf-8','gbk','"' . $v['tzID'] . '"');
				$account_info_value['tzr'] = iconv('utf-8','gbk','"' . $v['tzr'] . '"');
				$account_info_value['tzje'] = iconv('utf-8','gbk','"' . number_format($v['tzje'],2) . '"');
				$account_info_value['tzzt'] = iconv('utf-8','gbk','"' . $v['tzzt'] . '"');
				$account_info_value['sfzr'] = iconv('utf-8','gbk','"' . $v['sfzr'] . '"');
				$account_info_value['tzsj'] = iconv('utf-8','gbk','"' . $v['tzsj'] . '"');
				$account_info_value['jkbt'] = iconv('utf-8','gbk','"' . $v['jkbt'] . '"');
				$account_info_value['jkbh'] = iconv('utf-8','gbk','"' . $v['jkbh'] . '"');
				$account_info_value['jkze'] = iconv('utf-8','gbk','"' . number_format($v['jkze'],2) . '"');
				$account_info_value['zdtb'] = iconv('utf-8','gbk','"' . $v['zdtb'] . '"');
				
				$content_account_info .= implode(",", $account_info_value) . "\n";
			}	
			
			
			header("Content-Disposition: attachment; filename=account_info_list.csv");
	    	echo $content_account_info;  
		}
		else
		{
			if($page==1)
			$this->error(L("NO_RESULT"));
		}		
	}
	
	
	//投资人数导出
	public function export_csv_usernum_total($page = 1){
		
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
		
		$map =  $this->com_search();
		
		foreach ( $map as $key => $val ) {
			//dump($key);
			if ((!is_array($val)) && ($val <> '')){
				$parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		}
		
		$sql_str = "select create_date as time, count(DISTINCT user_id) as ztyhs, sum(money) as tzdj 
		from ".DB_PREFIX."deal_load  where is_has_loans = 1 ";
		
		//日期期间使用in形式，以确保能正常使用到索引
		if( isset($map['start_time']) && $map['start_time'] <> '' && isset($map['end_time']) && $map['end_time'] <> ''){
			$sql_str .= " and create_date in (".date_in($map['start_time'],$map['end_time']).")";
		}
		$begin_time  = trim($_REQUEST['start_time'])==''?0:$_REQUEST['start_time'];
		$end_time  = trim($_REQUEST['end_time'])==''?0:$_REQUEST['end_time'];
		
		if($begin_time > 0 || $end_time > 0){
			if($begin_time>0 && $end_time==0){
				$sql_str .= " and (create_date > '$begin_time')";
			}elseif($begin_time==0 && $end_time>0){
				$sql_str .= " and (create_date < '$end_time' )";
			}elseif($begin_time >0 && $end_time>0){
				$sql_str .= " and (create_date between '$begin_time' and '$end_time' )";
			}
		}
		
		$sql_str .= "  group by create_date limit $limit ";
		
		$list = array();
		$list = $GLOBALS['db']->getAll($sql_str);
		
//		echo $sql_str;
//		exit;
//		var_dump($list);exit;
		
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv_usernum_total'), $page+1);
			
			$usernum_total_value = array(
									'time'=>'""',
									'ztyhs'=>'""',
									'tzdj'=>'""'
									);
			if($page == 1)
	    	$content_usernum_total = iconv("utf-8","gbk","时间,投资用户数,投资统计");
	    	  
	    	if($page == 1) 	
	    	$content_usernum_total = $content_usernum_total . "\n";
	    	
	    	foreach($list as $k=>$v)
			{	
				$usernum_total_value = array();
				$usernum_total_value['time'] = iconv('utf-8','gbk','"' . $v['time'] . '"');
				$usernum_total_value['ztyhs'] = iconv('utf-8','gbk','"' . $v['ztyhs'] . '"');
				$usernum_total_value['tzdj'] = iconv('utf-8','gbk','"' . number_format($v['tzdj'],2) . '"');
				
				$content_usernum_total .= implode(",", $usernum_total_value) . "\n";
			}	
			
			
			header("Content-Disposition: attachment; filename=usernum_total_list.csv");
	    	echo $content_usernum_total;  
		}
		else
		{
			if($page==1)
			$this->error(L("NO_RESULT"));
		}				
	}
	
	//投资人数明细导出
	public function export_csv_usernum_info($page = 1){
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
		
		$begin_time  = trim($_REQUEST['begin_time'])==''?0:to_timespan($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time'])==''?0:to_timespan($_REQUEST['end_time']);
		
		$time=trim($_REQUEST['time']);
		if(trim($_REQUEST['time'])){
			$condtion = " and  (a.create_date = '$time')";
		}
		
		if(trim($_REQUEST['user_name'])!='')
		{
			$user_name= trim($_REQUEST['user_name']);
		}
		
		
		$sql_str = "select  
		u.user_name as yjm, 
		a.money as tzje, 
		FROM_UNIXTIME(a.create_time + 28800, '%Y-%m-%d %H:%i:%S') as time 
		from ".DB_PREFIX."deal_load a  
		left join ".DB_PREFIX."user u on u.id = a.user_id
		where a.is_has_loans = 1  $condtion  ";
		
		if($user_name){
			$sql_str .=" and u.user_name like '%$user_name%'";
		}
		
		if($begin_time > 0 || $end_time > 0){
			if($begin_time>0 && $end_time==0){
				$sql_str .= " and (a.create_time > $begin_time)";
			}elseif($begin_time==0 && $end_time>0){
				$sql_str .= " and (a.create_time < $end_time )";
			}elseif($begin_time >0 && $end_time>0){
				$sql_str .= " and (a.create_time between $begin_time and $end_time )";
			}
			
		}
		$sql_str .= " limit $limit";
		
		$list = array();
		$list = $GLOBALS['db']->getAll($sql_str);
		
//		echo $sql_str;
//		exit;
//		var_dump($list);exit;
		
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv_usernum_info'), $page+1);
			
			$usernum_info_value = array(
									'yjm'=>'""',
									'tzje'=>'""',
									'time'=>'""'
									);
			if($page == 1)
	    	$content_usernum_info = iconv("utf-8","gbk","用户名,投资金额,时间");
	    	  
	    	if($page == 1) 	
	    	$content_usernum_info = $content_usernum_info . "\n";
	    	
	    	foreach($list as $k=>$v)
			{	
				$usernum_info_value = array();
				$usernum_info_value['yjm'] = iconv('utf-8','gbk','"' . $v['yjm'] . '"');
				$usernum_info_value['tzje'] = iconv('utf-8','gbk','"' . number_format($v['tzje'],2) . '"');
				$usernum_info_value['time'] = iconv('utf-8','gbk','"' . $v['time'] . '"');
				
				
				$content_usernum_info .= implode(",", $usernum_info_value) . "\n";
			}	
			
			
			header("Content-Disposition: attachment; filename=usernum_info_list.csv");
	    	echo $content_usernum_info;  
		}
		else
		{
			if($page==1)
			$this->error(L("NO_RESULT"));
		};		
	}

	//已回款导出
	public function export_csv_hasback_total($page = 1){
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
		
		$map =  $this->com_search();
		
		foreach ( $map as $key => $val ) {
			//dump($key);
			if ((!is_array($val)) && ($val <> '')){
				$parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		}
		
		$sql_str = "select 
		true_repay_date as time,
		sum(repay_money + impose_money - manage_money) as tzzhkze,
		sum(self_money) as	tzzhkbj,
		sum(repay_money - self_money) as tzzhklx, 	 	 
		sum(if(status = 0, impose_money,0)) as tqhkfx,
		sum(if(status = 2 or status = 3, impose_money,0)) as yqhkfj,
		sum(manage_money) as tzzfglf,
		sum(repay_manage_money + repay_manage_impose_money) as jkzfglf,
		sum(manage_money + repay_manage_money + repay_manage_impose_money) as ptsl,
		count(*) as skrc
		from ".DB_PREFIX."deal_load_repay as a where has_repay = 1 ";
		
		//日期期间使用in形式，以确保能正常使用到索引
		if( isset($map['start_time']) && $map['start_time'] <> '' && isset($map['end_time']) && $map['end_time'] <> ''){
			$sql_str .= " and true_repay_date in (".date_in($map['start_time'],$map['end_time']).")";
		}
		
		$sql_str .= "  group by true_repay_date limit $limit";
		
		$list = array();
		$list = $GLOBALS['db']->getAll($sql_str);
		
//		echo $sql_str;
//		exit;
//		var_dump($list);exit;
		
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv_hasback_total'), $page+1);
			
			$hasback_total_value = array(
									'time'=>'""',
									'tzzhkze'=>'""',
									'tzzhkbj'=>'""',
									'tzzhklx'=>'""',
									'tqhkfx'=>'""',
									'yqhkfj'=>'""',
									'tzzfglf'=>'""',
									'jkzfglf'=>'""',
									'ptsl'=>'""',
									'skrc'=>'""'
									);
			if($page == 1)
	    	$content_hasback_total = iconv("utf-8","gbk","时间,投资者回款总额,投资者回款本金,投资者回款利息,提前还款罚息,逾期还款罚金,投资者付管理费,借款者付管理费,平台收入,收款人次");
	    	  
	    	if($page == 1) 	
	    	$content_hasback_total = $content_hasback_total . "\n";
	    	
	    	foreach($list as $k=>$v)
			{	
				$hasback_total_value = array();
				$hasback_total_value['time'] = iconv('utf-8','gbk','"' . $v['time'] . '"');
				$hasback_total_value['tzzhkze'] = iconv('utf-8','gbk','"' . number_format($v['tzzhkze'],2) . '"');
				$hasback_total_value['tzzhkbj'] = iconv('utf-8','gbk','"' . number_format($v['tzzhkbj'],2) . '"');
				$hasback_total_value['tzzhklx'] = iconv('utf-8','gbk','"' . number_format($v['tzzhklx'],2) . '"');
				$hasback_total_value['tqhkfx'] = iconv('utf-8','gbk','"' . number_format($v['tqhkfx'],2) . '"');
				$hasback_total_value['yqhkfj'] = iconv('utf-8','gbk','"' . number_format($v['yqhkfj'],2) . '"');
				$hasback_total_value['tzzfglf'] = iconv('utf-8','gbk','"' . number_format($v['tzzfglf'],2) . '"');
				$hasback_total_value['jkzfglf'] = iconv('utf-8','gbk','"' . number_format($v['jkzfglf'],2) . '"');
				$hasback_total_value['ptsl'] = iconv('utf-8','gbk','"' . number_format($v['ptsl'],2) . '"');
				$hasback_total_value['skrc'] = iconv('utf-8','gbk','"' . $v['skrc'] . '"');
				
				
				$content_hasback_total .= implode(",", $hasback_total_value) . "\n";
			}	
			
			
			header("Content-Disposition: attachment; filename=hasback_total_list.csv");
	    	echo $content_hasback_total;  
		}
		else
		{
			if($page==1)
			$this->error(L("NO_RESULT"));
		};			
	}
	
	//已回款明细导出
	public function export_csv_hasback_info($page = 1){
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
		
		$begin_time  = trim($_REQUEST['begin_time'])==''?0:to_timespan($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time'])==''?0:to_timespan($_REQUEST['end_time']);
		//$condtion = "  (a.repay_time between $start_time and $end_time )";
		$time=trim($_REQUEST['time']);
		if(trim($_REQUEST['time'])){
			$condtion = " and  (a.true_repay_date = '$time')";
		}
		
		if(trim($_REQUEST['user_name'])!='')
		{
			$user_name= trim($_REQUEST['user_name']);
		}
		if(trim($_REQUEST['deal_sn'])!='')
		{
			$deal_sn= trim($_REQUEST['deal_sn']);
		}
		if(trim($_REQUEST['sub_name'])!='')
		{
			$sub_name= trim($_REQUEST['sub_name']);
		}
		if(trim($_REQUEST['status'])!='')
		{
			$status= trim($_REQUEST['status']);
		}
		
		if(trim($_REQUEST['cate_id'])!='')
		{
			$cate_id= trim($_REQUEST['cate_id']);
		}
		$this->assign("cate_list",M("DealCate")->where('is_effect = 1 and is_delete = 0 order by sort')->findAll());
		
		
		$sql_str = "select 
		u.user_name as skr,
		d.deal_sn as hkh,
		d.sub_name as jkbt,
		c.`name` as jklx,
		a.repay_money as hkbx,
		a.impose_money as tzzfxsl,
		a.manage_money as tzzfglf,
		a.repay_manage_money + a.repay_manage_impose_money as jkzfglf,
		if (datediff(a.true_repay_date,FROM_UNIXTIME(a.repay_time + 28800, '%Y-%m-%d')) > 0, 
		datediff(a.true_repay_date,FROM_UNIXTIME(a.repay_time + 28800, '%Y-%m-%d')),
		0) as yqts, 
		FROM_UNIXTIME(a.repay_time + 28800, '%Y-%m-%d') as  yssj,
		 a.true_repay_date as	sssj,
		 a.repay_money + a.impose_money - a.manage_money as	tzzssze,	
		a.manage_money + a.repay_manage_money + a.repay_manage_impose_money as ptsl,
		if(has_repay = 1,
		case status
		when 0 then '提前收款'
		when 1 then '准时收款'
		when 2 then '逾期收款'
		when 3 then '严重逾期收款'
		else 
		 '已收款'
		end
		,'未收款') as zt,
		a.deal_id as deal_id
		 from ".DB_PREFIX."deal_load_repay as a 
		LEFT JOIN ".DB_PREFIX."user u on u.id = a.user_id
		LEFT JOIN ".DB_PREFIX."deal d on d.id = a.deal_id
		LEFT JOIN ".DB_PREFIX."deal_cate c on c.id = d.cate_id
		where a.has_repay = 1 $condtion ";
		
		if($user_name){
			$sql_str .=" and u.user_name like '%$user_name%'";
		}
		if($deal_sn){
			$sql_str .=" and d.deal_sn like '%$deal_sn%'";
		}
		if($sub_name){
			$sql_str .=" and d.sub_name like '%$sub_name%'";
		}
		
		if($cate_id){
			$sql_str .=" and c.id = '$cate_id'";
		}
		
		if(isset($_REQUEST['status'])){
			if($status==5){
				
			}elseif($status==1){
				$sql_str .=" and status = 0 ";
			}elseif($status==2){
				$sql_str .=" and status = 1 ";
			}elseif($status==3){
				$sql_str .=" and status = 2 ";
			}elseif($status==4){
				$sql_str .=" and status = 3 ";
			}
			
		}
		
		if($begin_time > 0 || $end_time > 0){
			if($begin_time>0 && $end_time==0){
				$sql_str .= " and (a.true_repay_time > $begin_time)";
			}elseif($begin_time==0 && $end_time>0){
				$sql_str .= " and (a.true_repay_time < $end_time )";
			}elseif($begin_time >0 && $end_time>0){
				$sql_str .= " and (a.true_repay_time between $begin_time and $end_time )";
			}
			
		}
		
		$sql_str .= " limit $limit ";
		$list = array();
		$list = $GLOBALS['db']->getAll($sql_str);
		
//		echo $sql_str;
//		exit;
//		var_dump($list);exit;
		
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv_hasback_info'), $page+1);
			
			$hasback_total_value = array(
									'skr'=>'""',
									'hkh'=>'""',
									'jkbt'=>'""',
									'jklx'=>'""',
									'hkbx'=>'""',
									'tzzfxsl'=>'""',
									'tzzfglf'=>'""',
									'jkzfglf'=>'""',
									'yqts'=>'""',
									'yssj'=>'""',
									'sssj'=>'""',
									'tzzssze'=>'""',
									'ptsl'=>'""',
									'zt'=>'""'
									);
			if($page == 1)
	    	$content_hasback_info = iconv("utf-8","gbk","收款人,贷款号,借款标题,借款类型,还款本息,投资者罚息收入,投资者付管理费,借款者付管理费,逾期天数,应收时间,实收时间,投资者实收总额,平台收入,状态");
	    	  
	    	if($page == 1) 	
	    	$content_hasback_info = $content_hasback_info . "\n";
	    	
	    	foreach($list as $k=>$v)
			{	
				$hasback_info_value = array();
				$hasback_info_value['skr'] = iconv('utf-8','gbk','"' . $v['skr'] . '"');
				$hasback_info_value['hkh'] = iconv('utf-8','gbk','"' . $v['hkh'] . '"');
				$hasback_info_value['jkbt'] = iconv('utf-8','gbk','"' . $v['jkbt'] . '"');
				$hasback_info_value['jklx'] = iconv('utf-8','gbk','"' . $v['jklx'] . '"');
				$hasback_info_value['hkbx'] = iconv('utf-8','gbk','"' . number_format($v['hkbx'],2) . '"');
				$hasback_info_value['tzzfxsl'] = iconv('utf-8','gbk','"' . number_format($v['tzzfxsl'],2) . '"');
				$hasback_info_value['tzzfglf'] = iconv('utf-8','gbk','"' . number_format($v['tzzfglf'],2) . '"');
				$hasback_info_value['jkzfglf'] = iconv('utf-8','gbk','"' . number_format($v['jkzfglf'],2) . '"');
				$hasback_info_value['yqts'] = iconv('utf-8','gbk','"' . $v['yqts'] . '"');
				$hasback_info_value['yssj'] = iconv('utf-8','gbk','"' . $v['yssj'] . '"');
				$hasback_info_value['sssj'] = iconv('utf-8','gbk','"' . $v['sssj'] . '"');
				$hasback_info_value['tzzssze'] = iconv('utf-8','gbk','"' . number_format($v['tzzssze'],2) . '"');
				$hasback_info_value['ptsl'] = iconv('utf-8','gbk','"' . number_format($v['ptsl'],2) . '"');
				$hasback_info_value['zt'] = iconv('utf-8','gbk','"' . $v['zt'] . '"');
				
				
				$content_hasback_info .= implode(",", $hasback_info_value) . "\n";
			}	
			
			
			header("Content-Disposition: attachment; filename=hasback_info_list.csv");
	    	echo $content_hasback_info;  
		}
		else
		{
			if($page==1)
			$this->error(L("NO_RESULT"));
		};	
					
	}
	
	//待收款导出
	public function export_csv_tobe_receivables($page = 1){
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
		
		$map =  $this->com_search();
		
		foreach ( $map as $key => $val ) {
			//dump($key);
			if ((!is_array($val)) && ($val <> '')){
				$parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		}
		
		$sql_str = "select 
		repay_date as time,
		sum(repay_money + impose_money - manage_money) as	dsze,
		sum(self_money) as	dsbj,
		sum(repay_money - self_money) as dslx, 	 	 
		count(*) as dskrc

		from ".DB_PREFIX."deal_load_repay as a where has_repay = 0 ";
		
		//日期期间使用in形式，以确保能正常使用到索引
		if( isset($map['start_time']) && $map['start_time'] <> '' && isset($map['end_time']) && $map['end_time'] <> ''){
			$sql_str .= " and repay_date in (".date_in($map['start_time'],$map['end_time']).")";
		}
		
		$sql_str .= "  group by repay_date limit $limit";
		$list = array();
		$list = $GLOBALS['db']->getAll($sql_str);
		
//		echo $sql_str;
//		exit;
//		var_dump($list);exit;
		
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv_tobe_receivables'), $page+1);
			
			$tobe_receivables_value = array(
									'time'=>'""',
									'dsze'=>'""',
									'dsbj'=>'""',
									'dslx'=>'""',
									'dskrc'=>'""'
									);
			if($page == 1)
	    	$content_tobe_receivables = iconv("utf-8","gbk","时间,待收总额,待收本金,待收利息,待收款人次");
	    	  
	    	if($page == 1) 	
	    	$content_tobe_receivables = $content_tobe_receivables . "\n";
	    	
	    	foreach($list as $k=>$v)
			{	
				$tobe_receivables_value = array();
				$tobe_receivables_value['time'] = iconv('utf-8','gbk','"' . $v['time'] . '"');
				$tobe_receivables_value['dsze'] = iconv('utf-8','gbk','"' . number_format($v['dsze'],2) . '"');
				$tobe_receivables_value['dsbj'] = iconv('utf-8','gbk','"' . number_format($v['dsbj'],2) . '"');
				$tobe_receivables_value['dslx'] = iconv('utf-8','gbk','"' . number_format($v['dslx'],2) . '"');
				$tobe_receivables_value['dskrc'] = iconv('utf-8','gbk','"' . $v['dskrc'] . '"');
				$content_tobe_receivables .= implode(",", $tobe_receivables_value) . "\n";
			}	
			
			
			header("Content-Disposition: attachment; filename=tobe_receivables_list.csv");
	    	echo $content_tobe_receivables;  
		}
		else
		{
			if($page==1)
			$this->error(L("NO_RESULT"));
		};		
	}
	
	
	//待收款明细导出
	public function export_csv_tobe_receivablesinfo($page = 1){
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
		
		$begin_time  = trim($_REQUEST['begin_time'])==''?0:to_timespan($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time'])==''?0:to_timespan($_REQUEST['end_time']);
		//$condtion = "  (a.repay_time between $start_time and $end_time )";
		$time=trim($_REQUEST['time']);
		if(trim($_REQUEST['time'])){
			$condtion = " and  (a.repay_date = '$time')";
		}
		
		if(trim($_REQUEST['user_name'])!='')
		{
			$user_name= trim($_REQUEST['user_name']);
		}
		if(trim($_REQUEST['deal_sn'])!='')
		{
			$deal_sn= trim($_REQUEST['deal_sn']);
		}
		if(trim($_REQUEST['sub_name'])!='')
		{
			$sub_name= trim($_REQUEST['sub_name']);
		}
		if(trim($_REQUEST['cate_id'])!='')
		{
			$cate_id= trim($_REQUEST['cate_id']);
		}
		
		//$model = D();
		//echo $sql_str;
		$this->assign("cate_list",M("DealCate")->where('is_effect = 1 and is_delete = 0 order by sort')->findAll());
		$now_day=to_date(TIME_UTC,"Y-m-d");
		$sql_str = "select 
		u.id as skr,
		d.deal_sn as	dkh,
		d.sub_name as jkbt,
		c.`name` as jklx,
		a.repay_money as hkbx,
		if (datediff($now_day ,FROM_UNIXTIME(a.repay_time + 28800, '%Y-%m-%d')) > 0, 
		datediff($now_day,FROM_UNIXTIME(a.repay_time + 28800, '%Y-%m-%d')),
		0) as yqts, 
		FROM_UNIXTIME(a.repay_time + 28800, '%Y-%m-%d') as  yssj,
		'未收款' as zt,
		a.deal_id as deal_id
		 from ".DB_PREFIX."deal_load_repay as a 
		LEFT JOIN ".DB_PREFIX."user u on u.id = a.user_id
		LEFT JOIN ".DB_PREFIX."deal d on d.id = a.deal_id
		LEFT JOIN ".DB_PREFIX."deal_cate c on c.id = d.cate_id
		where a.has_repay = 0  $condtion ";
		
		if($user_name){
			$sql_str="$sql_str and u.user_name like '%$user_name%'";
		}
		if($deal_sn){
			$sql_str="$sql_str and d.deal_sn like '%$deal_sn%'";
		}
		if($sub_name){
			$sql_str="$sql_str and d.sub_name like '%$sub_name%'";
		}
		
		if($sub_name){
			$sql_str="$sql_str and d.sub_name like '%$sub_name%'";
		}
		
		if($cate_id){
			$sql_str="$sql_str and c.id = '$cate_id'";
		}
		
		if($begin_time > 0 || $end_time > 0){
			if($begin_time>0 && $end_time==0){
				$sql_str = "$sql_str and (a.repay_time > $begin_time)";
			}elseif($begin_time==0 && $end_time>0){
				$sql_str = "$sql_str and (a.repay_time < $end_time )";
			}elseif($begin_time >0 && $end_time>0){
				$sql_str = "$sql_str and (a.repay_time between $begin_time and $end_time )";
			}
			
		}
		
		$sql_str .= " limit $limit ";
		$list = array();
		$list = $GLOBALS['db']->getAll($sql_str);
		
//		echo $sql_str;
//		exit;
//		var_dump($list);exit;
		
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv_tobe_receivablesinfo'), $page+1);
			
			$hasback_total_value = array(
									'skr'=>'""',
									'dkh'=>'""',
									'jkbt'=>'""',
									'jklx'=>'""',
									'hkbx'=>'""',
									'yqts'=>'""',
									'yssj'=>'""',
									'zt'=>'""'
									);
			if($page == 1)
	    	$content_tobe_receivablesinfo = iconv("utf-8","gbk","收款人,贷款号,借款标题,借款类型,还款本息,逾期天数,应收时间,状态");
	    	  
	    	if($page == 1) 	
	    	$content_tobe_receivablesinfo = $content_tobe_receivablesinfo . "\n";
	    	
	    	foreach($list as $k=>$v)
			{	
				$tobe_receivablesinfo_value = array();
				$tobe_receivablesinfo_value['skr'] = iconv('utf-8','gbk','"' . $v['skr'] . '"');
				$tobe_receivablesinfo_value['dkh'] = iconv('utf-8','gbk','"' . $v['dkh'] . '"');
				$tobe_receivablesinfo_value['jkbt'] = iconv('utf-8','gbk','"' . $v['jkbt'] . '"');
				$tobe_receivablesinfo_value['jklx'] = iconv('utf-8','gbk','"' . $v['jklx'] . '"');
				$tobe_receivablesinfo_value['hkbx'] = iconv('utf-8','gbk','"' . number_format($v['hkbx'],2) . '"');
				$tobe_receivablesinfo_value['yqts'] = iconv('utf-8','gbk','"' . $v['yqts'] . '"');
				$tobe_receivablesinfo_value['yssj'] = iconv('utf-8','gbk','"' . $v['yssj'] . '"');
				$tobe_receivablesinfo_value['zt'] = iconv('utf-8','gbk','"' . $v['zt'] . '"');
				
				$content_tobe_receivablesinfo .= implode(",", $tobe_receivablesinfo_value) . "\n";
			}	
			
			header("Content-Disposition: attachment; filename=tobe_receivablesinfo_list.csv");
	    	echo $content_tobe_receivablesinfo;  
		}
		else
		{
			if($page==1)
			$this->error(L("NO_RESULT"));
		};			
	}
	
	
	//投资排名导出
	public function export_csv_rank_list($page = 1){
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
		
		$map =  $this->com_search();
		
		foreach ( $map as $key => $val ) {
			//dump($key);
			if ((!is_array($val)) && ($val <> '')){
				$parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		}
		
		$sql_str = "SELECT
			(@rowNO := @rowNo + 1) AS pm,
			c.tzr,
			c.cgtzze
		FROM
			(
				SELECT
					a.user_name AS tzr,
					sum(money) AS cgtzze
				FROM
					".DB_PREFIX."deal_load AS a,
					(SELECT @rowNO := 0) b
				WHERE
					a.is_has_loans = 1
			";	
		
		//日期期间使用in形式，以确保能正常使用到索引
		if( isset($map['start_time']) && $map['start_time'] <> '' && isset($map['end_time']) && $map['end_time'] <> ''){
			$sql_str .= " and create_date in (".date_in($map['start_time'],$map['end_time']).")";
		}
		
		$sql_str .= " GROUP BY a.user_id order by cgtzze desc) c limit $limit  ";
		
		$list = array();
		$list = $GLOBALS['db']->getAll($sql_str);
		
//		echo $sql_str;
//		exit;
//		var_dump($list);exit;
		
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv_rank_list'), $page+1);
			
			$rank_list_value = array(
									'pm'=>'""',
									'tzr'=>'""',
									'cgtzze'=>'""'
									);
			if($page == 1)
	    	$content_rank_list = iconv("utf-8","gbk","排名,投资人,成功投资总额");
	    	  
	    	if($page == 1) 	
	    	$content_rank_list = $content_rank_list . "\n";
	    	
	    	foreach($list as $k=>$v)
			{	
				$rank_list_value = array();
				$rank_list_value['pm'] = iconv('utf-8','gbk','"' . $v['pm'] . '"');
				$rank_list_value['tzr'] = iconv('utf-8','gbk','"' . $v['tzr'] . '"');
				$rank_list_value['cgtzze'] = iconv('utf-8','gbk','"' . number_format($v['cgtzze'],2) . '"');
				
				$content_rank_list .= implode(",", $rank_list_value) . "\n";
			}	
			
			
			header("Content-Disposition: attachment; filename=rank_list.csv");
	    	echo $content_rank_list;  
		}
		else
		{
			if($page==1)
			$this->error(L("NO_RESULT"));
		};		
				
	}
	
	
	//投资额比例导出
	public function export_csv_account_ratio($page = 1){
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
		
		$map =  $this->com_search();
		
		foreach ( $map as $key => $val ) {
			//dump($key);
			if ((!is_array($val)) && ($val <> '')){
				$parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		}
		
		$sql_str = "select create_date as time,
		sum(if(money < 5000, 1, 0)) as ftu,
		sum(if(money >= 5000 and money < 10000, 1, 0)) as ftom,
		sum(if(money >= 10000 and money < 50000, 1, 0)) as omtm,
		sum(if(money >= 50000 and money < 100000, 1, 0)) as fmtm,
		sum(if(money >= 100000 and money < 200000, 1, 0)) as tmtm,
		sum(if(money >= 200000 and money < 500000, 1, 0)) as tmfm,
		sum(if(money >= 500000, 1, 0)) as fmo,
		count(*) as cgzrc
		from ".DB_PREFIX."deal_load where is_has_loans = 1 ";
		
		//日期期间使用in形式，以确保能正常使用到索引
		if( isset($map['start_time']) && $map['start_time'] <> '' && isset($map['end_time']) && $map['end_time'] <> ''){
			$sql_str .= " and create_date in (".date_in($map['start_time'],$map['end_time']).")";
		}
		
		$sql_str .= " GROUP BY create_date limit $limit ";
		$list = array();
		$list = $GLOBALS['db']->getAll($sql_str);
		
//		echo $sql_str;
//		exit;
//		var_dump($list);exit;
		
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv_account_ratio'), $page+1);
			
			$account_ratio_value = array(
									'time'=>'""',
									'ftu'=>'""',
									'ftom'=>'""',
									'omtm'=>'""',
									'fmtm'=>'""',
									'tmtm'=>'""',
									'tmfm'=>'""',
									'fmo'=>'""',
									'cgzrc'=>'""'
									);
			if($page == 1)
	    	$content_account_ratio = iconv("utf-8","gbk","时间,5千以下,5千至1万,1至5万,5至10万,10至20万,20至50万,50万以上,成功总人次");
	    	  
	    	if($page == 1) 	
	    	$content_account_ratio = $content_account_ratio . "\n";
	    	
	    	foreach($list as $k=>$v)
			{	
				$account_ratio_value = array();
				$account_ratio_value['time'] = iconv('utf-8','gbk','"' . $v['time'] . '"');
				$account_ratio_value['ftu'] = iconv('utf-8','gbk','"' . $v['ftu'] . '"');
				$account_ratio_value['ftom'] = iconv('utf-8','gbk','"' . $v['ftom'] . '"');
				$account_ratio_value['omtm'] = iconv('utf-8','gbk','"' . $v['omtm'] . '"');
				$account_ratio_value['fmtm'] = iconv('utf-8','gbk','"' . $v['fmtm'] . '"');
				$account_ratio_value['tmtm'] = iconv('utf-8','gbk','"' . $v['tmtm'] . '"');
				$account_ratio_value['tmfm'] = iconv('utf-8','gbk','"' . $v['tmfm'] . '"');
				$account_ratio_value['fmo'] = iconv('utf-8','gbk','"' . $v['fmo'] . '"');
				$account_ratio_value['cgzrc'] = iconv('utf-8','gbk','"' . $v['cgzrc'] . '"');
				
				$content_account_ratio .= implode(",", $account_ratio_value) . "\n";
			}	
			
			
			header("Content-Disposition: attachment; filename=account_ratio_list.csv");
	    	echo $content_account_ratio;  
		}
		else
		{
			if($page==1)
			$this->error(L("NO_RESULT"));
		};	
	}	
	
	//借出总统计导出
	public function export_csv_total($page = 1){
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
		
		$begin_time  = trim($_REQUEST['begin_time'])==''?0:to_timespan($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time'])==''?0:to_timespan($_REQUEST['end_time']);
		
		$sql_str = "select 
		count(DISTINCT user_id) as tzrs,
		sum(self_money) as cgtzje, 
		(select ifnull(sum(rebate_money),0) from ".DB_PREFIX."deal_load m where m.is_has_loans = 1 and m.is_rebate = 1 ) as jlze,
		sum(if(has_repay = 0, repay_money,0)) as dsze,
		sum(if(has_repay = 0, self_money,0)) as dsbjze,
		sum(if(has_repay = 0, repay_money - self_money,0)) as dsllze,
		sum(if(has_repay = 1, repay_money,0)) as ysze,
		sum(if(has_repay = 1, self_money,0)) as ysbjze,
		sum(if(has_repay = 1, repay_money - self_money,0)) as ysllze,
		sum(if(has_repay = 1 and status = 0, impose_money,0)) as tqhkfxze,
		sum(if(has_repay = 1 and (status = 2 or status = 3), impose_money,0)) as yqhkfjze
		from ".DB_PREFIX."deal_load_repay as a where 1 = 1   ";
		
		if($begin_time > 0 || $end_time > 0){
			if($begin_time>0 && $end_time==0){
				$sql_str .= " and (a.repay_time > $begin_time)";
			}elseif($begin_time==0 && $end_time>0){
				$sql_str .= " and (a.repay_time < $end_time )";
			}elseif($begin_time >0 && $end_time>0){
				$sql_str .= " and (a.repay_time between $begin_time and $end_time )";
			}
			
		}
		
		$list = array();
		$list = $GLOBALS['db']->getAll($sql_str);
		
//		echo $sql_str;
//		exit;
//		var_dump($list);exit;
		
		if($list)
		{
			//register_shutdown_function(array(&$this, 'export_csv_total'), $page+1);
			
			$total_value = array(
									'tzrs'=>'""',
									'cgtzje'=>'""',
									'jlze'=>'""',
									'dsze'=>'""',
									'dsbjze'=>'""',
									'dsllze'=>'""',
									'ysze'=>'""',
									'ysbjze'=>'""',
									'ysllze'=>'""',
									'tqhkfxze'=>'""',
									'yqhkfjze'=>'""'
									);
			if($page == 1)
	    	$content_total = iconv("utf-8","gbk","投资人数,成功投资金额,奖励总额,待收总额,待收本金总额,待收利润总额,已收总额,已收本金总额,已收利润总额,提前还款罚息总额,逾期还款罚金总额");
	    	  
	    	if($page == 1) 	
	    	$content_total = $content_total . "\n";
	    	
	    	foreach($list as $k=>$v)
			{	
				$total_value = array();
				$total_value['tzrs'] = iconv('utf-8','gbk','"' . $v['tzrs'] . '"');
				$total_value['cgtzje'] = iconv('utf-8','gbk','"' . number_format($v['cgtzje'],2) . '"');
				$total_value['jlze'] = iconv('utf-8','gbk','"' . number_format($v['jlze'],2) . '"');
				$total_value['dsze'] = iconv('utf-8','gbk','"' . number_format($v['dsze'],2) . '"');
				$total_value['dsbjze'] = iconv('utf-8','gbk','"' . number_format($v['dsbjze'],2) . '"');
				$total_value['dsllze'] = iconv('utf-8','gbk','"' . number_format($v['dsllze'],2) . '"');
				$total_value['ysze'] = iconv('utf-8','gbk','"' . number_format($v['ysze'],2) . '"');
				$total_value['ysbjze'] = iconv('utf-8','gbk','"' . number_format($v['ysbjze'],2) . '"');
				$total_value['ysllze'] = iconv('utf-8','gbk','"' . number_format($v['ysllze'],2) . '"');
				$total_value['tqhkfxze'] = iconv('utf-8','gbk','"' . number_format($v['tqhkfxze'],2) . '"');
				$total_value['yqhkfjze'] = iconv('utf-8','gbk','"' . number_format($v['yqhkfjze'],2) . '"');
				
				$content_total .= implode(",", $total_value) . "\n";
			}	
			
			
			header("Content-Disposition: attachment; filename=total_list.csv");
	    	echo $content_total;  
		}
		else
		{
			if($page==1)
			$this->error(L("NO_RESULT"));
		}
			
	}
	
	//所有投资人导出
	public function export_csv_total_info($page = 1)
	{	
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
		
		$begin_time  = trim($_REQUEST['begin_time'])==''?0:to_timespan($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time'])==''?0:to_timespan($_REQUEST['end_time']);
		
		$sql_str = "select 
			(select u.user_name from ".DB_PREFIX."user u where u.id=a.user_id) as tzr,
			sum(self_money) as cgtzje, 
			(select ifnull(sum(rebate_money),0) from ".DB_PREFIX."deal_load m where m.is_has_loans = 1 and m.is_rebate = 1 and m.user_id = 
			a.user_id) as jlze,
			sum(if(has_repay = 0, repay_money,0)) as dsze,
			sum(if(has_repay = 0, self_money,0)) as dsbjze,
			sum(if(has_repay = 0, repay_money - self_money,0)) as dsllze,
			sum(if(has_repay = 1, repay_money,0)) as ysze,
			sum(if(has_repay = 1, self_money,0)) as ysbjze,
			sum(if(has_repay = 1, repay_money - self_money,0)) as ysllze,
			sum(if(has_repay = 1 and status = 0, impose_money,0)) as tqhkfxze,
			sum(if(has_repay = 1 and (status = 2 or status = 3), impose_money,0)) as yqhkfjze
			from ".DB_PREFIX."deal_load_repay as a left join ".DB_PREFIX."user as u on u.id=a.user_id where 1 = 1 ";
		if(trim($_REQUEST['user_name'])!='')
		{
			$sql_str .= " and u.user_name like '%".trim($_REQUEST['user_name'])."%'  ";	
		}
		
		if($begin_time > 0 || $end_time > 0){
			if($begin_time>0 && $end_time==0){
				$sql_str .= " and (a.repay_time > $begin_time)";
			}elseif($begin_time==0 && $end_time>0){
				$sql_str .= " and (a.repay_time < $end_time )";
			}elseif($begin_time >0 && $end_time>0){
				$sql_str .= " and (a.repay_time between $begin_time and $end_time )";
			}
			
		}
		
		$sql_str .= "  group by user_id limit $limit ";
		$list = array();
		$list = $GLOBALS['db']->getAll($sql_str);
		
//		echo $sql_str;
//		exit;
//		var_dump($list);exit;
		
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv_total_info'), $page+1);
			
			$total_info_value = array(
									'tzr'=>'""',
									'cgtzje'=>'""',
									'jlze'=>'""',
									'dsze'=>'""',
									'dsbjze'=>'""',
									'dsllze'=>'""',
									'ysze'=>'""',
									'ysbjze'=>'""',
									'ysllze'=>'""',
									'tqhkfxze'=>'""',
									'yqhkfjze'=>'""'
									);
			if($page == 1)
	    	$content_total_info = iconv("utf-8","gbk","投资人,成功投资金额,奖励总额,待收总额,待收本金总额,待收利润总额,已收总额,已收本金总额,已收利润总额,提前还款罚息总额,逾期还款罚金总额");
	    	  
	    	if($page == 1) 	
	    	$content_total_info = $content_total_info . "\n";
	    	
	    	foreach($list as $k=>$v)
			{	
				$total_info_value = array();
				$total_info_value['tzr'] = iconv('utf-8','gbk','"' . $v['tzr'] . '"');
				$total_info_value['cgtzje'] = iconv('utf-8','gbk','"' . number_format($v['cgtzje'],2) . '"');
				$total_info_value['jlze'] = iconv('utf-8','gbk','"' . number_format($v['jlze'],2) . '"');
				$total_info_value['dsze'] = iconv('utf-8','gbk','"' . number_format($v['dsze'],2) . '"');
				$total_info_value['dsbjze'] = iconv('utf-8','gbk','"' . number_format($v['dsbjze'],2) . '"');
				$total_info_value['dsllze'] = iconv('utf-8','gbk','"' . number_format($v['dsllze'],2) . '"');
				$total_info_value['ysze'] = iconv('utf-8','gbk','"' . number_format($v['ysze'],2) . '"');
				$total_info_value['ysbjze'] = iconv('utf-8','gbk','"' . number_format($v['ysbjze'],2) . '"');
				$total_info_value['ysllze'] = iconv('utf-8','gbk','"' . number_format($v['ysllze'],2) . '"');
				$total_info_value['tqhkfxze'] = iconv('utf-8','gbk','"' . number_format($v['tqhkfxze'],2) . '"');
				$total_info_value['yqhkfjze'] = iconv('utf-8','gbk','"' . number_format($v['yqhkfjze'],2) . '"');
				
				$content_total_info .= implode(",", $total_info_value) . "\n";
			}	
			
			
			header("Content-Disposition: attachment; filename=total_info_list.csv");
	    	echo $content_total_info;  
		}
		else
		{
			if($page==1)
			$this->error(L("NO_RESULT"));
		}
		
	}
}
?>