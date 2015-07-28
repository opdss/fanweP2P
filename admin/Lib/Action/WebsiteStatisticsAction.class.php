<?php

class WebsiteStatisticsAction extends CommonAction {
	
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
		//echo abs(to_timespan($map['end_time']) - to_timespan($map['start_time'])) / 86400 + 1;
		if ($q_date_diff > 0 && (abs(to_timespan($map['end_time']) - to_timespan($map['start_time'])) / 86400  + 1 > $q_date_diff)){
			$this->error("查询时间间隔不能大于  {$q_date_diff} 天");
			exit;
		}
	
		return $map;
	}	
	
	//充值统计
	public function website_recharge_total(){
		
		$map =  $this->com_search();
		
		foreach ( $map as $key => $val ) {
			//dump($key);
			if ((!is_array($val)) && ($val <> '')){
				$parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		}
		
		$sql_str = "select 
		n.pay_date as 时间,
		sum(if(is_paid=1,money,0)) as 成功充值总额
		from ".DB_PREFIX."payment_notice as n ";
		
		//日期期间使用in形式，以确保能正常使用到索引
		if( isset($map['start_time']) && $map['start_time'] <> '' && isset($map['end_time']) && $map['end_time'] <> ''){
			$sql_str .= " where n.pay_date in (".date_in($map['start_time'],$map['end_time']).")";
		}
		
		$sql_str .= " group by n.pay_date ";
		$model = D();
		$voList = $this->_Sql_list($model, $sql_str, "&".$parameter, '时间', false);
		
		require('./admin/Tpl/default/Common/js/flash/php-ofc-library/open-flash-chart.php');
		
		$total_array=array(
				array(
					array('成功充值总额','时间','成功充值总额'),
					
				),
		);
		
		//echo $sql_str;
		krsort($voList);
		$chart_list=$this->get_jx_json_all($voList,$total_array);
		$this->assign("chart_list",$chart_list);
		$this->display();		
		
	}
	
	//提现统计
	public function website_extraction_cash(){
		
		$map =  $this->com_search();
		
		foreach ( $map as $key => $val ) {
			//dump($key);
			if ((!is_array($val)) && ($val <> '')){
				$parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		}
		
		$sql_str = "select 
		c.create_date as 时间,
		sum(if(status=1,money,0)) as 成功提现总额,
		count(*) as 人次
		from ".DB_PREFIX."user_carry as c ";
		
		//日期期间使用in形式，以确保能正常使用到索引
		if( isset($map['start_time']) && $map['start_time'] <> '' && isset($map['end_time']) && $map['end_time'] <> ''){
			$sql_str .= " where c.create_date in (".date_in($map['start_time'],$map['end_time']).")";
		}
		
		$sql_str .= " group by c.create_date ";
		$model = D();
		$voList = $this->_Sql_list($model, $sql_str, "&".$parameter, '时间', false);
		
		require('./admin/Tpl/default/Common/js/flash/php-ofc-library/open-flash-chart.php');
		
		$total_array=array(
				array(
					array('成功提现总额','时间','成功提现总额'),
					array('人次','时间','人次'),
				),
		);
		krsort($voList);
		$chart_list=$this->get_jx_json_all($voList,$total_array);
		$this->assign("chart_list",$chart_list);
		$this->display();		
		
	}
	
	
	//用户统计
	public function website_users_total(){
		
		$map =  $this->com_search();
		
		foreach ( $map as $key => $val ) {
			//dump($key);
			if ((!is_array($val)) && ($val <> '')){
				$parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		}
		
		$sql_str = "select 
		u.create_date as 时间,
		count(*) as 用户注册人数
		from ".DB_PREFIX."user as u ";
		
		//日期期间使用in形式，以确保能正常使用到索引
		if( isset($map['start_time']) && $map['start_time'] <> '' && isset($map['end_time']) && $map['end_time'] <> ''){
			$sql_str .= " where u.create_date in (".date_in($map['start_time'],$map['end_time']).")";
		}
		
		$sql_str .= " group by u.create_date ";
		$model = D();
		$voList = $this->_Sql_list($model, $sql_str, "&".$parameter, '时间', false);
		
		require('./admin/Tpl/default/Common/js/flash/php-ofc-library/open-flash-chart.php');
		
		$total_array=array(
				array(
					array('用户注册人数','时间','用户注册人数'),
					
				),
		);
		
		//echo $sql_str;
		krsort($voList);
		$chart_list=$this->get_jx_json_all($voList,$total_array);
		$this->assign("chart_list",$chart_list);
		$this->display();		
		
	}
	
	//网站垫付
	public function website_advance_total(){
		
		$map =  $this->com_search();
		
		foreach ( $map as $key => $val ) {
			//dump($key);
			if ((!is_array($val)) && ($val <> '')){
				$parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		}
		
		$sql_str = "select 
		g.create_date as 时间,
		sum(repay_money) as 代还本息总额,
		sum(manage_money) as 代还管理费总额,
		sum(impose_money) as 代还罚息总额,
		sum(manage_impose_money) as 代还逾期管理费总额
		from ".DB_PREFIX."generation_repay as g ";
		
		//日期期间使用in形式，以确保能正常使用到索引
		if( isset($map['start_time']) && $map['start_time'] <> '' && isset($map['end_time']) && $map['end_time'] <> ''){
			$sql_str .= " where g.create_date in (".date_in($map['start_time'],$map['end_time']).")";
		}
		
		$sql_str .= " group by g.create_date ";
		$model = D();
		$voList = $this->_Sql_list($model, $sql_str, "&".$parameter, '时间', false);
		
		require('./admin/Tpl/default/Common/js/flash/php-ofc-library/open-flash-chart.php');
		
		$total_array=array(
				array(
					array('代还本息总额','时间','代还本息总额'),
					array('代还管理费总额','时间','代还管理费总额'),
					array('代还罚息总额','时间','代还罚息总额'),
					array('代还逾期管理费总额','时间','代还逾期管理费总额'),
					
				),
		);
		
		//echo $sql_str;
		krsort($voList);
		$chart_list=$this->get_jx_json_all($voList,$total_array);
		$this->assign("chart_list",$chart_list);
		$this->display();		
		
	}
	
	//网站费用统计
	public function website_cost_total(){
		
		$begin_time  = trim($_REQUEST['begin_time'])==''?0:to_timespan($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time'])==''?0:to_timespan($_REQUEST['end_time']);
		
		$sql_str = "select 
		count(DISTINCT user_id) as 关联用户数,
		sum(if(type = 1,money,0)) as 充值手续费,
		sum(if(type = 9,money,0)) as 提现手续费,
		sum(if(type = 10,money,0)) as 借款管理费,
		sum(if(type = 12,money,0)) as 逾期管理费,
		sum(if(type = 13,money,0)) as 人工操作,
		sum(if(type = 14,money,0)) as 借款服务费,
		sum(if(type = 17,money,0)) as 债权转让管理费,
		sum(if(type = 18,money,0)) as 开户奖励,
		sum(if(type = 20,money,0)) as 投标管理费,
		sum(if(type = 22,money,0)) as 兑换,
		sum(if(type = 23,money,0)) as 邀请返利,
		sum(if(type = 24,money,0)) as 投标返利,
		sum(if(type = 25,money,0)) as 签到成功,
		sum(if(type = 26,money,0)) as 逾期罚金（垫付后）,
		sum(if(type = 27,money,0)) as 其他费用,
		sum(if(type = 28,money,0)) as 投资奖励,
		sum(if(type = 29,money,0)) as 红包奖励
		from ".DB_PREFIX."site_money_log where 1 = 1  ";
		
		if($begin_time > 0 || $end_time > 0){
			if($begin_time>0 && $end_time==0){
				$sql_str .= " and (create_time > $begin_time)";
			}elseif($begin_time==0 && $end_time>0){
				$sql_str .= " and (create_time < $end_time )";
			}elseif($begin_time >0 && $end_time>0){
				$sql_str .= " and (create_time between $begin_time and $end_time )";
			}
			
		}
		
		$model = D();
		$voList = $this->_Sql_list($model, $sql_str, '时间', false);		

		$this->display();		
	}
	
	//充值明细
	public function website_recharge_info(){
		
		$begin_time  = trim($_REQUEST['begin_time'])==''?0:to_timespan($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time'])==''?0:to_timespan($_REQUEST['end_time']);
		$time=trim($_REQUEST['time']);
		if(trim($_REQUEST['time'])){
			$condtion = " where  (n.create_date = '$time')";
		}else{
			$condtion = " where 1=1 ";
		}
		
		if(trim($_REQUEST['notice_sn'])!='')
		{
			$notice_sn=trim($_REQUEST['notice_sn']);
		}
		if(trim($_REQUEST['user_name'])!='')
		{
			$user_name= trim($_REQUEST['user_name']);
		}
		if(trim($_REQUEST['is_paid'])!='')
		{
			$is_paid= trim($_REQUEST['is_paid']);
		}
		if(trim($_REQUEST['memo'])!='')
		{
			$memo= trim($_REQUEST['memo']);
		}
		
		
		
		$sql_str = "select 
		n.create_date as 时间,
		n.notice_sn as 支付单号,
		n.user_id,
		u.user_name as 会员名称,
		n.money as 应付金额,
		p.name as 支付方式,
		if(n.is_paid = 1,'已支付','未支付') as 支付状态,
		n.memo as 支付备注
		from ".DB_PREFIX."payment_notice as n LEFT JOIN ".DB_PREFIX."user as u on u.id=n.user_id LEFT JOIN ".DB_PREFIX."payment as p on  p.id=n.payment_id $condtion ";
		
		if($notice_sn){
			$sql_str="$sql_str  and n.notice_sn = '$notice_sn'";
		}
		if($user_name){
			$sql_str="$sql_str and u.user_name like '%$user_name%'";
		}
		if($memo){
			$sql_str="$sql_str and n.memo like '%$memo%'";
		}
		
		
		if(isset($_REQUEST['is_paid'])){
			if($is_paid==4){
				$sql_str="$sql_str";
			}elseif($is_paid==1){
				$sql_str="$sql_str and n.is_paid = 1 ";
			}elseif($is_paid==2){
				$sql_str="$sql_str and n.is_paid = 0 ";
			}
			
		}
		
		if($begin_time > 0 || $end_time > 0){
			if($begin_time>0 && $end_time==0){
				$sql_str = "$sql_str and (n.create_time > $begin_time)";
			}elseif($begin_time==0 && $end_time>0){
				$sql_str = "$sql_str and (n.create_time < $end_time )";
			}elseif($begin_time >0 && $end_time>0){
				$sql_str = "$sql_str and (n.create_time between $begin_time and $end_time )";
			}
			
		}
		
		$model = D();
		//echo $sql_str;
		$voList = $this->_Sql_list($model, $sql_str, '时间', false);
		
		$this->display();		
	}
	
	//提现明细
	public function website_extraction_cash_info(){
		
		$begin_time  = trim($_REQUEST['begin_time'])==''?0:to_timespan($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time'])==''?0:to_timespan($_REQUEST['end_time']);
		$time=trim($_REQUEST['time']);
		if(trim($_REQUEST['time'])){
			$condtion = " where  (c.create_date = '$time')";
		}else{
			$condtion = " where 1=1 ";
		}
		
		
		if(trim($_REQUEST['user_name'])!='')
		{
			$user_name= trim($_REQUEST['user_name']);
		}
		if(trim($_REQUEST['status'])!='')
		{
			$status= trim($_REQUEST['status']);
		}
		
		$sql_str = "select 
		c.create_date as 时间,
		u.id as 会员名称,
		c.money as 提现金额,
		c.fee as 手续费,
		case c.status 
		when 0 then '待审核'
		when 1 then '已付款'
		when 2 then '未通过'
		when 3 then '待付款'
		else 
		 '撤销'
		end as 提现状态,
		FROM_UNIXTIME(c.update_time + 28800, '%Y-%m-%d') as 处理时间
		from ".DB_PREFIX."user_carry as c left join ".DB_PREFIX."user as u on u.id=c.user_id  $condtion ";
		
		if($user_name){
			$sql_str="$sql_str and u.user_name like '%$user_name%'";
		}
		
		if(isset($_REQUEST['status'])){
			if($status==5){
				$sql_str="$sql_str";
			}elseif($status==1){
				$sql_str="$sql_str and c.status = 0 ";
			}elseif($status==2){
				$sql_str="$sql_str and c.status = 1 ";
			}elseif($status==3){
				$sql_str="$sql_str and c.status = 2 ";
			}elseif($status==4){
				$sql_str="$sql_str and c.status = 4 ";
			}
			
		}
		
		if($begin_time > 0 || $end_time > 0){
			if($begin_time>0 && $end_time==0){
				$sql_str = "$sql_str and (c.create_time > $begin_time)";
			}elseif($begin_time==0 && $end_time>0){
				$sql_str = "$sql_str and (c.create_time < $end_time )";
			}elseif($begin_time >0 && $end_time>0){
				$sql_str = "$sql_str and (c.create_time between $begin_time and $end_time )";
			}
			
		}
		
		$model = D();
		//echo $sql_str;
		$voList = $this->_Sql_list($model, $sql_str, '时间', false);
		
		$this->display();		
	}
	
	//用户明细
	public function website_users_info(){
		
		$begin_time  = trim($_REQUEST['begin_time'])==''?0:to_timespan($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time'])==''?0:to_timespan($_REQUEST['end_time']);
		$time=trim($_REQUEST['time']);
		if(trim($_REQUEST['time'])){
			$condtion = " where  (u.create_date = '$time')";
		}else{
			$condtion = " where 1=1 ";
		}
		
		if(trim($_REQUEST['user_name'])!='')
		{
			$user_name= trim($_REQUEST['user_name']);
		}
		if(trim($_REQUEST['email'])!='')
		{
			$email= trim($_REQUEST['email']);
		}
		if(trim($_REQUEST['mobile'])!='')
		{
			$mobile= trim($_REQUEST['mobile']);
		}
		
		if(trim($_REQUEST['level_id'])!='')
		{
			$level_id= trim($_REQUEST['level_id']);
		}
		
		$this->assign("level_list",M("UserLevel")->findAll());
		
		
		$sql_str = "select
		u.create_date as 注册时间,
		u.id as 会员名称,
		u.email as 会员邮件,
		u.mobile as 手机号,
		u.money as 会员余额,
		u.lock_money as 冻结资金,
		l.name as 会员等级
		from ".DB_PREFIX."user as u left join ".DB_PREFIX."user_level as l on l.id=u.level_id  $condtion ";
		
		if($user_name){
			$sql_str="$sql_str and u.user_name like '%$user_name%'";
		}
		if($email){
			$sql_str="$sql_str and u.email like '%$email%'";
		}
		if($mobile){
			$sql_str="$sql_str and u.mobile like '%$mobile%'";
		}
		
		if($level_id){
			$sql_str="$sql_str and l.id = '$level_id'";
		}
		
		if($begin_time > 0 || $end_time > 0){
			if($begin_time>0 && $end_time==0){
				$sql_str = "$sql_str and (u.create_time > $begin_time)";
			}elseif($begin_time==0 && $end_time>0){
				$sql_str = "$sql_str and (u.create_time < $end_time )";
			}elseif($begin_time >0 && $end_time>0){
				$sql_str = "$sql_str and (u.create_time between $begin_time and $end_time )";
			}
			
		}
		
		$model = D();
		//echo $sql_str;
		$voList = $this->_Sql_list($model, $sql_str, '时间', false);
		
		$this->display();		
	}
	
	//垫付明细
	public function website_advance_info(){
		
		$begin_time  = trim($_REQUEST['begin_time'])==''?0:to_timespan($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time'])==''?0:to_timespan($_REQUEST['end_time']);
		$time=trim($_REQUEST['time']);
		if(trim($_REQUEST['time'])){
			$condtion = " where  (r.create_date = '$time')";
		}else{
			$condtion = " where 1=1 ";
		}
		
		if(trim($_REQUEST['name'])!='')
		{
			$name= trim($_REQUEST['name']);
		}
		if(trim($_REQUEST['adm_name'])!='')
		{
			$adm_name= trim($_REQUEST['adm_name']);
		}
		if(trim($_REQUEST['agency_id'])!='')
		{
			$agency_id= trim($_REQUEST['agency_id']);
		}
		
		$this->assign("agency_list",M("User")->where('user_type = 2')->findAll());
		
		$sql_str = "select 
		r.create_date as 代还时间,
		d.sub_name as 贷款名称,
		CONCAT('第',lr.l_key + 1,'期') as 第几期,
		a.adm_name as 管理员,
		da.name as 担保机构,
		r.repay_money as 代还本息,
		r.manage_money as 代还管理费,
		r.impose_money as 代还罚息,
		r.manage_impose_money 代还多少逾期管理费,
		r.deal_id
		from ".DB_PREFIX."generation_repay as r
		left join ".DB_PREFIX."deal as d on d.id=r.deal_id
		left join ".DB_PREFIX."deal_load_repay as lr on lr.repay_id=r.repay_id
		left join ".DB_PREFIX."admin as a on a.id=r.admin_id
		left join ".DB_PREFIX."deal_agency as da on da.id=r.agency_id
		$condtion ";
		
		if($name){
			$sql_str="$sql_str and d.name like '%$name%'";
		}
		if($adm_name){
			$sql_str="$sql_str and a.adm_name like '%$adm_name%'";
		}
		
		if($agency_id){
			$sql_str="$sql_str and da.id = '$agency_id'";
		}
		
		
		if($begin_time > 0 || $end_time > 0){
			if($begin_time>0 && $end_time==0){
				$sql_str = "$sql_str and (r.create_time > $begin_time)";
			}elseif($begin_time==0 && $end_time>0){
				$sql_str = "$sql_str and (r.create_time < $end_time )";
			}elseif($begin_time >0 && $end_time>0){
				$sql_str = "$sql_str and (r.create_time between $begin_time and $end_time )";
			}
			
		}
		
		$model = D();
		//echo $sql_str;
		$voList = $this->_Sql_list($model, $sql_str, '时间', false);
		
		$this->display();		
	}
	
	//充值统计导出
	public function export_csv_recharge_total($page = 1){
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
		n.pay_date as time,
		sum(if(is_paid=1,money,0)) as cgczze
		from ".DB_PREFIX."payment_notice as n ";
		
		//日期期间使用in形式，以确保能正常使用到索引
		if( isset($map['start_time']) && $map['start_time'] <> '' && isset($map['end_time']) && $map['end_time'] <> ''){
			$sql_str .= " where n.pay_date in (".date_in($map['start_time'],$map['end_time']).")";
		}
		
		$sql_str .= " group by n.pay_date limit $limit ";
		$list = array();
		$list = $GLOBALS['db']->getAll($sql_str);
		
//		echo $sql_str;
//		exit;
//		var_dump($list);exit;
		
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv_recharge_total'), $page+1);
			
			$recharge_total_value = array(
									'time'=>'""',
									'cgczze'=>'""'
									);
			if($page == 1)
	    	$content_recharge_total = iconv("utf-8","gbk","时间,成功充值总额");
	    	  
	    	if($page == 1) 	
	    	$content_recharge_total = $content_recharge_total . "\n";
	    	
	    	foreach($list as $k=>$v)
			{	
				$recharge_total_value = array();
				$recharge_total_value['time'] = iconv('utf-8','gbk','"' . $v['time'] . '"');
				$recharge_total_value['cgczze'] = iconv('utf-8','gbk','"' . number_format($v['cgczze'],2) . '"');
				
				$content_recharge_total .= implode(",", $recharge_total_value) . "\n";
			}	
			
			
			header("Content-Disposition: attachment; filename=recharge_total_list.csv");
	    	echo $content_recharge_total;  
		}
		else
		{
			if($page==1)
			$this->error(L("NO_RESULT"));
		}		
		
	}
	
	//提现统计导出
	public function export_csv_extraction_cash($page = 1){
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
		c.create_date as time,
		sum(if(status=1,money,0)) as cgtxze,
		count(*) as rc
		from ".DB_PREFIX."user_carry as c ";
		
		//日期期间使用in形式，以确保能正常使用到索引
		if( isset($map['start_time']) && $map['start_time'] <> '' && isset($map['end_time']) && $map['end_time'] <> ''){
			$sql_str .= " where c.create_date in (".date_in($map['start_time'],$map['end_time']).")";
		}
		
		$sql_str .= " group by c.create_date limit $limit ";
		$list = array();
		$list = $GLOBALS['db']->getAll($sql_str);
		
//		echo $sql_str;
//		exit;
//		var_dump($list);exit;
		
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv_extraction_cash'), $page+1);
			
			$extraction_cash_value = array(
									'time'=>'""',
									'cgtxze'=>'""',
									'rc'=>'""'
									);
			if($page == 1)
	    	$content_extraction_cash = iconv("utf-8","gbk","时间,成功提现总额,人次");
	    	  
	    	if($page == 1) 	
	    	$content_extraction_cash = $content_extraction_cash . "\n";
	    	
	    	foreach($list as $k=>$v)
			{	
				$extraction_cash_value = array();
				$extraction_cash_value['time'] = iconv('utf-8','gbk','"' . $v['time'] . '"');
				$extraction_cash_value['cgtxze'] = iconv('utf-8','gbk','"' . number_format($v['cgtxze'],2) . '"');
				$extraction_cash_value['rc'] = iconv('utf-8','gbk','"' . $v['rc'] . '"');
				
				$content_extraction_cash .= implode(",", $extraction_cash_value) . "\n";
			}	
			
			header("Content-Disposition: attachment; filename=extraction_cash_list.csv");
	    	echo $content_extraction_cash;  
		}
		else
		{
			if($page==1)
			$this->error(L("NO_RESULT"));
		}		
		
	}
	
	//用户统计导出
	public function export_csv_users_total($page = 1){
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
		u.create_date as time,
		count(*) as yhzcrs
		from ".DB_PREFIX."user as u ";
		
		//日期期间使用in形式，以确保能正常使用到索引
		if( isset($map['start_time']) && $map['start_time'] <> '' && isset($map['end_time']) && $map['end_time'] <> ''){
			$sql_str .= " where u.create_date in (".date_in($map['start_time'],$map['end_time']).")";
		}
		
		$sql_str .= " group by u.create_date limit $limit ";
		$list = array();
		$list = $GLOBALS['db']->getAll($sql_str);
		
//		echo $sql_str;
//		exit;
//		var_dump($list);exit;
		
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv_users_total'), $page+1);
			
			$users_total_value = array(
									'time'=>'""',
									'yhzcrs'=>'""'
									);
			if($page == 1)
	    	$content_users_total = iconv("utf-8","gbk","时间,用户注册人数");
	    	  
	    	if($page == 1) 	
	    	$content_users_total = $content_users_total . "\n";
	    	
	    	foreach($list as $k=>$v)
			{	
				$users_total_value = array();
				$users_total_value['time'] = iconv('utf-8','gbk','"' . $v['time'] . '"');
				$users_total_value['yhzcrs'] = iconv('utf-8','gbk','"' . $v['yhzcrs'] . '"');
				
				$content_users_total .= implode(",", $users_total_value) . "\n";
			}	
			
			header("Content-Disposition: attachment; filename=users_total_list.csv");
	    	echo $content_users_total;  
		}
		else
		{
			if($page==1)
			$this->error(L("NO_RESULT"));
		}
		
	}
	
	//网站垫付导出
	public function export_csv_advance_total($page = 1){
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
		g.create_date as time,
		sum(repay_money) as dhbxze,
		sum(manage_money) as dhglfze,
		sum(impose_money) as dhfxze,
		sum(manage_impose_money) as dhyqglfze
		from ".DB_PREFIX."generation_repay as g ";
		
		//日期期间使用in形式，以确保能正常使用到索引
		if( isset($map['start_time']) && $map['start_time'] <> '' && isset($map['end_time']) && $map['end_time'] <> ''){
			$sql_str .= " where g.create_date in (".date_in($map['start_time'],$map['end_time']).")";
		}
		
		$sql_str .= " group by g.create_date limit $limit ";
		$list = array();
		$list = $GLOBALS['db']->getAll($sql_str);
		
//		echo $sql_str;
//		exit;
//		var_dump($list);exit;
		
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv_advance_total'), $page+1);
			
			$advance_total_value = array(
									'time'=>'""',
									'dhbxze'=>'""',
									'dhglfze'=>'""',
									'dhfxze'=>'""',
									'dhyqglfze'=>'""'
									);
			if($page == 1)
	    	$content_advance_total = iconv("utf-8","gbk","时间,代还本息总额,代还管理费总额,代还罚息总额,代还逾期管理费总额");
	    	  
	    	if($page == 1) 	
	    	$content_advance_total = $content_advance_total . "\n";
	    	
	    	foreach($list as $k=>$v)
			{	
				$advance_total_value = array();
				$advance_total_value['time'] = iconv('utf-8','gbk','"' . $v['time'] . '"');
				$advance_total_value['dhbxze'] = iconv('utf-8','gbk','"' . number_format($v['dhbxze'],2) . '"');
				$advance_total_value['dhglfze'] = iconv('utf-8','gbk','"' . number_format($v['dhglfze'],2) . '"');
				$advance_total_value['dhfxze'] = iconv('utf-8','gbk','"' . number_format($v['dhfxze'],2) . '"');
				$advance_total_value['dhyqglfze'] = iconv('utf-8','gbk','"' . number_format($v['dhyqglfze'],2) . '"');
				
				$content_advance_total .= implode(",", $advance_total_value) . "\n";
			}	
			
			header("Content-Disposition: attachment; filename=advance_total_list.csv");
	    	echo $content_advance_total;  
		}
		else
		{
			if($page==1)
			$this->error(L("NO_RESULT"));
		}		
		
	}
	
	//网站费用统计导出
	public function export_csv_cost_total($page = 1){
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
		
		$begin_time  = trim($_REQUEST['begin_time'])==''?0:to_timespan($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time'])==''?0:to_timespan($_REQUEST['end_time']);
		
		$sql_str = "select 
		count(DISTINCT user_id) as glyhs,
		sum(if(type = 1,money,0)) as czsxf,
		sum(if(type = 9,money,0)) as txsxf,
		sum(if(type = 10,money,0)) as jkglf,
		sum(if(type = 12,money,0)) as yqglf,
		sum(if(type = 13,money,0)) as rgcz,
		sum(if(type = 14,money,0)) as jkfwf,
		sum(if(type = 17,money,0)) as zqzrglf,
		sum(if(type = 18,money,0)) as kfjl,
		sum(if(type = 20,money,0)) as tbglf,
		sum(if(type = 22,money,0)) as dh,
		sum(if(type = 23,money,0)) as yqfl,
		sum(if(type = 24,money,0)) as tbfl,
		sum(if(type = 25,money,0)) as qdcg,
		sum(if(type = 26,money,0)) as yqfj,
		sum(if(type = 27,money,0)) as qtfy,
		sum(if(type = 28,money,0)) as tzjl,
		sum(if(type = 29,money,0)) as hbjl
		from ".DB_PREFIX."site_money_log where 1 = 1  ";
		
		if($begin_time > 0 || $end_time > 0){
			if($begin_time>0 && $end_time==0){
				$sql_str .= " and (create_time > $begin_time)";
			}elseif($begin_time==0 && $end_time>0){
				$sql_str .= " and (create_time < $end_time )";
			}elseif($begin_time >0 && $end_time>0){
				$sql_str .= " and (create_time between $begin_time and $end_time )";
			}
			
		}
		
		$list = array();
		$list = $GLOBALS['db']->getAll($sql_str);
		
//		echo $sql_str;
//		exit;
//		var_dump($list);exit;
		
		if($list)
		{
			//register_shutdown_function(array(&$this, 'export_csv_cost_total'), $page+1);
			
			$cost_total_value = array(
									'glyhs'=>'""',
									'czsxf'=>'""',
									'txsxf'=>'""',
									'jkglf'=>'""',
									'yqglf'=>'""',
									'rgcz'=>'""',
									'jkfwf'=>'""',
									'zqzrglf'=>'""',
									'kfjl'=>'""',
									'tbglf'=>'""',
									'dh'=>'""',
									'yqfl'=>'""',
									'tbfl'=>'""',
									'qdcg'=>'""',
									'yqfj'=>'""',
									'qtfy'=>'""',
									'tzjl'=>'""',
									'hbjl'=>'""'
									);
			if($page == 1)
	    	$content_cost_total = iconv("utf-8","gbk","关联用户数,充值手续费,提现手续费,借款管理费,逾期管理费,人工操作,借款服务费,债权转让管理费,开户奖励,投标管理费,兑换,邀请返利,投标返利,签到成功,逾期罚金（垫付后）,其他费用,投资奖励,红包奖励");
	    	  
	    	if($page == 1) 	
	    	$content_cost_total = $content_cost_total . "\n";
	    	
	    	foreach($list as $k=>$v)
			{	
				$cost_total_value = array();
				$cost_total_value['glyhs'] = iconv('utf-8','gbk','"' . $v['glyhs'] . '"');
				$cost_total_value['czsxf'] = iconv('utf-8','gbk','"' . number_format($v['czsxf'],2) . '"');
				$cost_total_value['txsxf'] = iconv('utf-8','gbk','"' . number_format($v['txsxf'],2) . '"');
				$cost_total_value['jkglf'] = iconv('utf-8','gbk','"' . number_format($v['jkglf'],2) . '"');
				$cost_total_value['yqglf'] = iconv('utf-8','gbk','"' . number_format($v['yqglf'],2) . '"');
				$cost_total_value['rgcz'] = iconv('utf-8','gbk','"' . number_format($v['rgcz'],2) . '"');
				$cost_total_value['jkfwf'] = iconv('utf-8','gbk','"' . number_format($v['jkfwf'],2) . '"');
				$cost_total_value['zqzrglf'] = iconv('utf-8','gbk','"' . number_format($v['zqzrglf'],2) . '"');
				$cost_total_value['kfjl'] = iconv('utf-8','gbk','"' . number_format($v['kfjl'],2) . '"');
				$cost_total_value['tbglf'] = iconv('utf-8','gbk','"' . number_format($v['tbglf'],2) . '"');
				$cost_total_value['dh'] = iconv('utf-8','gbk','"' . number_format($v['dh'],2) . '"');
				$cost_total_value['yqfl'] = iconv('utf-8','gbk','"' . number_format($v['yqfl'],2) . '"');
				$cost_total_value['tbfl'] = iconv('utf-8','gbk','"' . number_format($v['tbfl'],2) . '"');
				$cost_total_value['qdcg'] = iconv('utf-8','gbk','"' . number_format($v['qdcg'],2) . '"');
				$cost_total_value['yqfj'] = iconv('utf-8','gbk','"' . number_format($v['yqfj'],2) . '"');
				$cost_total_value['qtfy'] = iconv('utf-8','gbk','"' . number_format($v['qtfy'],2) . '"');
				$cost_total_value['tzjl'] = iconv('utf-8','gbk','"' . number_format($v['tzjl'],2) . '"');
				$cost_total_value['hbjl'] = iconv('utf-8','gbk','"' . number_format($v['hbjl'],2) . '"');
				
				$content_cost_total .= implode(",", $cost_total_value) . "\n";
			}	
			
			header("Content-Disposition: attachment; filename=cost_total_list.csv");
	    	echo $content_cost_total;  
		}
		else
		{
			if($page==1)
			$this->error(L("NO_RESULT"));
		}
				
	}
	
	//充值明细导出
	public function export_csv_recharge_info($page = 1){
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
		
		$begin_time  = trim($_REQUEST['begin_time'])==''?0:to_timespan($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time'])==''?0:to_timespan($_REQUEST['end_time']);
		$time=trim($_REQUEST['time']);
		if(trim($_REQUEST['time'])){
			$condtion = " where  (n.create_date = '$time')";
		}else{
			$condtion = " where 1=1 ";
		}
		
		if(trim($_REQUEST['notice_sn'])!='')
		{
			$notice_sn=trim($_REQUEST['notice_sn']);
		}
		if(trim($_REQUEST['user_name'])!='')
		{
			$user_name= trim($_REQUEST['user_name']);
		}
		if(trim($_REQUEST['is_paid'])!='')
		{
			$is_paid= trim($_REQUEST['is_paid']);
		}
		if(trim($_REQUEST['memo'])!='')
		{
			$memo= trim($_REQUEST['memo']);
		}
		
		
		
		$sql_str = "select 
		n.create_date as time,
		n.notice_sn as zfdh,
		n.user_id,
		u.user_name as hymc,
		n.money as yfje,
		p.name as zffs,
		if(n.is_paid = 1,'已支付','未支付') as zfzt,
		n.memo as zfbz
		from ".DB_PREFIX."payment_notice as n LEFT JOIN ".DB_PREFIX."user as u on u.id=n.user_id LEFT JOIN ".DB_PREFIX."payment as p on  p.id=n.payment_id $condtion ";
		
		if($notice_sn){
			$sql_str .=" and n.notice_sn = '$notice_sn'";
		}
		if($user_name){
			$sql_str .=" and u.user_name like '%$user_name%'";
		}
		if($memo){
			$sql_str .=" and n.memo like '%$memo%'";
		}
		
		
		if(isset($_REQUEST['is_paid'])){
			if($is_paid==4){
				//$sql_str .="";
			}elseif($is_paid==1){
				$sql_str .=" and n.is_paid = 1 ";
			}elseif($is_paid==2){
				$sql_str .=" and n.is_paid = 0 ";
			}
			
		}
		
		if($begin_time > 0 || $end_time > 0){
			if($begin_time>0 && $end_time==0){
				$sql_str .= " and (n.create_time > $begin_time)";
			}elseif($begin_time==0 && $end_time>0){
				$sql_str .= " and (n.create_time < $end_time )";
			}elseif($begin_time >0 && $end_time>0){
				$sql_str .= " and (n.create_time between $begin_time and $end_time )";
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
			register_shutdown_function(array(&$this, 'export_csv_recharge_info'), $page+1);
			
			$recharge_info_value = array(
									'time'=>'""',
									'zfdh'=>'""',
									'hymc'=>'""',
									'yfje'=>'""',
									'zffs'=>'""',
									'zfzt'=>'""',
									'zfbz'=>'""'
									);
			if($page == 1)
	    	$content_recharge_info = iconv("utf-8","gbk","时间,支付单号,会员名称,应付金额,支付方式,支付状态,支付备注");
	    	  
	    	if($page == 1) 	
	    	$content_recharge_info = $content_recharge_info . "\n";
	    	
	    	foreach($list as $k=>$v)
			{	
				$recharge_info_value = array();
				$recharge_info_value['time'] = iconv('utf-8','gbk','"' . $v['time'] . '"');
				$recharge_info_value['zfdh'] = iconv('utf-8','gbk','"' . $v['zfdh'] . '"');
				$recharge_info_value['hymc'] = iconv('utf-8','gbk','"' . $v['hymc'] . '"');
				$recharge_info_value['yfje'] = iconv('utf-8','gbk','"' . number_format($v['yfje'],2) . '"');
				$recharge_info_value['zffs'] = iconv('utf-8','gbk','"' . $v['zffs'] . '"');
				$recharge_info_value['zfzt'] = iconv('utf-8','gbk','"' . $v['zfzt'] . '"');
				$recharge_info_value['zfbz'] = iconv('utf-8','gbk','"' . $v['zfbz'] . '"');
				
				$content_recharge_info .= implode(",", $recharge_info_value) . "\n";
			}	
			
			header("Content-Disposition: attachment; filename=recharge_info_list.csv");
	    	echo $content_recharge_info;  
		}
		else
		{
			if($page==1)
			$this->error(L("NO_RESULT"));
		}
				
	}
	
	//提现明细导出
	public function export_csv_extraction_cash_info($page = 1){
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
		
		$begin_time  = trim($_REQUEST['begin_time'])==''?0:to_timespan($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time'])==''?0:to_timespan($_REQUEST['end_time']);
		$time=trim($_REQUEST['time']);
		if(trim($_REQUEST['time'])){
			$condtion = " where  (c.create_date = '$time')";
		}else{
			$condtion = " where 1=1 ";
		}
		
		
		if(trim($_REQUEST['user_name'])!='')
		{
			$user_name= trim($_REQUEST['user_name']);
		}
		if(trim($_REQUEST['status'])!='')
		{
			$status= trim($_REQUEST['status']);
		}
		
		$sql_str = "select 
		c.create_date as time,
		u.id as hymc,
		c.money as txje,
		c.fee as sxf,
		case c.status 
		when 0 then '待审核'
		when 1 then '已付款'
		when 2 then '未通过'
		when 3 then '待付款'
		else 
		 '撤销'
		end as txzt,
		FROM_UNIXTIME(c.update_time + 28800, '%Y-%m-%d') as clsj
		from ".DB_PREFIX."user_carry as c left join ".DB_PREFIX."user as u on u.id=c.user_id  $condtion ";
		
		if($user_name){
			$sql_str .=" and u.user_name like '%$user_name%'";
		}
		
		if(isset($_REQUEST['status'])){
			if($status==5){
				//$sql_str .="";
			}elseif($status==1){
				$sql_str .=" and c.status = 0 ";
			}elseif($status==2){
				$sql_str .=" and c.status = 1 ";
			}elseif($status==3){
				$sql_str .=" and c.status = 2 ";
			}elseif($status==4){
				$sql_str .=" and c.status = 4 ";
			}
			
		}
		
		if($begin_time > 0 || $end_time > 0){
			if($begin_time>0 && $end_time==0){
				$sql_str .= " and (c.create_time > $begin_time)";
			}elseif($begin_time==0 && $end_time>0){
				$sql_str .= " and (c.create_time < $end_time )";
			}elseif($begin_time >0 && $end_time>0){
				$sql_str .= " and (c.create_time between $begin_time and $end_time )";
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
			register_shutdown_function(array(&$this, 'export_csv_extraction_cash_info'), $page+1);
			
			$extraction_cash_info_value = array(
									'time'=>'""',
									'hymc'=>'""',
									'txje'=>'""',
									'sxf'=>'""',
									'txzt'=>'""',
									'clsj'=>'""'
									);
			if($page == 1)
	    	$content_extraction_cash_info = iconv("utf-8","gbk","时间,会员名称,提现金额,手续费,提现状态,处理时间");
	    	  
	    	if($page == 1) 	
	    	$content_extraction_cash_info = $content_extraction_cash_info . "\n";
	    	
	    	foreach($list as $k=>$v)
			{	
				$extraction_cash_info_value = array();
				$extraction_cash_info_value['time'] = iconv('utf-8','gbk','"' . $v['time'] . '"');
				$extraction_cash_info_value['hymc'] = iconv('utf-8','gbk','"' . $v['hymc'] . '"');
				$extraction_cash_info_value['txje'] = iconv('utf-8','gbk','"' . number_format($v['txje'],2) . '"');
				$extraction_cash_info_value['sxf'] = iconv('utf-8','gbk','"' . number_format($v['sxf'],2) . '"');
				$extraction_cash_info_value['txzt'] = iconv('utf-8','gbk','"' . $v['txzt'] . '"');
				$extraction_cash_info_value['clsj'] = iconv('utf-8','gbk','"' . $v['clsj'] . '"');
				
				$content_extraction_cash_info .= implode(",", $extraction_cash_info_value) . "\n";
			}	
			
			header("Content-Disposition: attachment; filename=extraction_cash_info_list.csv");
	    	echo $content_extraction_cash_info;  
		}
		else
		{
			if($page==1)
			$this->error(L("NO_RESULT"));
		}
				
	}
	
	//用户明细导出
	public function export_csv_users_info($page = 1){
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
		
		$begin_time  = trim($_REQUEST['begin_time'])==''?0:to_timespan($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time'])==''?0:to_timespan($_REQUEST['end_time']);
		$time=trim($_REQUEST['time']);
		if(trim($_REQUEST['time'])){
			$condtion = " where  (u.create_date = '$time')";
		}else{
			$condtion = " where 1=1 ";
		}
		
		if(trim($_REQUEST['user_name'])!='')
		{
			$user_name= trim($_REQUEST['user_name']);
		}
		if(trim($_REQUEST['email'])!='')
		{
			$email= trim($_REQUEST['email']);
		}
		if(trim($_REQUEST['mobile'])!='')
		{
			$mobile= trim($_REQUEST['mobile']);
		}
		
		if(trim($_REQUEST['level_id'])!='')
		{
			$level_id= trim($_REQUEST['level_id']);
		}
		
		$this->assign("level_list",M("UserLevel")->findAll());
		
		
		$sql_str = "select
		u.create_date as zcsj,
		u.id as yhmc,
		u.email as hyyj,
		u.mobile as sjh,
		u.money as yhye,
		u.lock_money as djzj,
		l.name as yhdj
		from ".DB_PREFIX."user as u left join ".DB_PREFIX."user_level as l on l.id=u.level_id  $condtion ";
		
		if($user_name){
			$sql_str .=" and u.user_name like '%$user_name%'";
		}
		if($email){
			$sql_str .=" and u.email like '%$email%'";
		}
		if($mobile){
			$sql_str .=" and u.mobile like '%$mobile%'";
		}
		
		if($level_id){
			$sql_str .=" and l.id = '$level_id'";
		}
		
		if($begin_time > 0 || $end_time > 0){
			if($begin_time>0 && $end_time==0){
				$sql_str .= " and (u.create_time > $begin_time)";
			}elseif($begin_time==0 && $end_time>0){
				$sql_str .= " and (u.create_time < $end_time )";
			}elseif($begin_time >0 && $end_time>0){
				$sql_str .= " and (u.create_time between $begin_time and $end_time )";
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
			register_shutdown_function(array(&$this, 'export_csv_users_info'), $page+1);
			
			$users_info_value = array(
									'zcsj'=>'""',
									'yhmc'=>'""',
									'hyyj'=>'""',
									'sjh'=>'""',
									'yhye'=>'""',
									'djzj'=>'""',
									'yhdj'=>'""'
									);
			if($page == 1)
	    	$content_users_info = iconv("utf-8","gbk","注册时间,会员名称,会员邮件,手机号,会员余额,冻结资金,会员等级");
	    	  
	    	if($page == 1) 	
	    	$content_users_info = $content_users_info . "\n";
	    	
	    	foreach($list as $k=>$v)
			{	
				$users_info_value = array();
				$users_info_value['zcsj'] = iconv('utf-8','gbk','"' . $v['zcsj'] . '"');
				$users_info_value['yhmc'] = iconv('utf-8','gbk','"' . $v['yhmc'] . '"');
				$users_info_value['hyyj'] = iconv('utf-8','gbk','"' . $v['hyyj'] . '"');
				$users_info_value['sjh'] = iconv('utf-8','gbk','"' . $v['sjh'] . '"');
				$users_info_value['yhye'] = iconv('utf-8','gbk','"' . number_format($v['yhye'],2) . '"');
				$users_info_value['djzj'] = iconv('utf-8','gbk','"' . number_format($v['djzj'],2) . '"');
				$users_info_value['yhdj'] = iconv('utf-8','gbk','"' . $v['yhdj'] . '"');
				
				
				$content_users_info .= implode(",", $users_info_value) . "\n";
			}	
			
			header("Content-Disposition: attachment; filename=users_info_list.csv");
	    	echo $content_users_info;  
		}
		else
		{
			if($page==1)
			$this->error(L("NO_RESULT"));
		}
				
	}
	
	//垫付明细导出
	public function export_csv_advance_info($page = 1){
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
		
		$begin_time  = trim($_REQUEST['begin_time'])==''?0:to_timespan($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time'])==''?0:to_timespan($_REQUEST['end_time']);
		$time=trim($_REQUEST['time']);
		if(trim($_REQUEST['time'])){
			$condtion = " where  (r.create_date = '$time')";
		}else{
			$condtion = " where 1=1 ";
		}
		
		if(trim($_REQUEST['name'])!='')
		{
			$name= trim($_REQUEST['name']);
		}
		if(trim($_REQUEST['adm_name'])!='')
		{
			$adm_name= trim($_REQUEST['adm_name']);
		}
		if(trim($_REQUEST['agency_id'])!='')
		{
			$agency_id= trim($_REQUEST['agency_id']);
		}
		
		$this->assign("agency_list",M("User")->where('user_type = 2')->findAll());
		
		$sql_str = "select 
		r.create_date as dhsj,
		d.sub_name as dkmc,
		CONCAT('第',lr.l_key + 1,'期') as djq,
		a.adm_name as gly,
		da.name as dbjg,
		r.repay_money as dhbx,
		r.manage_money as dhglf,
		r.impose_money as dhfx,
		r.manage_impose_money dhdsyqglf,
		r.deal_id
		from ".DB_PREFIX."generation_repay as r
		left join ".DB_PREFIX."deal as d on d.id=r.deal_id
		left join ".DB_PREFIX."deal_load_repay as lr on lr.repay_id=r.repay_id
		left join ".DB_PREFIX."admin as a on a.id=r.admin_id
		left join ".DB_PREFIX."deal_agency as da on da.id=r.agency_id
		$condtion ";
		
		if($name){
			$sql_str .=" and d.name like '%$name%'";
		}
		if($adm_name){
			$sql_str .=" and a.adm_name like '%$adm_name%'";
		}
		
		if($agency_id){
			$sql_str .=" and da.id = '$agency_id'";
		}
		
		
		if($begin_time > 0 || $end_time > 0){
			if($begin_time>0 && $end_time==0){
				$sql_str .= " and (r.create_time > $begin_time)";
			}elseif($begin_time==0 && $end_time>0){
				$sql_str .= " and (r.create_time < $end_time )";
			}elseif($begin_time >0 && $end_time>0){
				$sql_str .= " and (r.create_time between $begin_time and $end_time )";
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
			register_shutdown_function(array(&$this, 'export_csv_advance_info'), $page+1);
			
			$advance_info_value = array(
									'dhsj'=>'""',
									'dkmc'=>'""',
									'djq'=>'""',
									'gly'=>'""',
									'dbjg'=>'""',
									'dhbx'=>'""',
									'dhglf'=>'""',
									'dhfx'=>'""',
									'dhdsyqglf'=>'""'
									);
			if($page == 1)
	    	$content_advance_info = iconv("utf-8","gbk","代还时间,贷款名称,第几期,管理员,担保机构,代还本息,代还管理费,代还罚息,代还多少逾期管理费");
	    	  
	    	if($page == 1) 	
	    	$content_advance_info = $content_advance_info . "\n";
	    	
	    	foreach($list as $k=>$v)
			{	
				$advance_info_value = array();
				$advance_info_value['dhsj'] = iconv('utf-8','gbk','"' . $v['dhsj'] . '"');
				$advance_info_value['dkmc'] = iconv('utf-8','gbk','"' . $v['dkmc'] . '"');
				$advance_info_value['djq'] = iconv('utf-8','gbk','"' . $v['djq'] . '"');
				$advance_info_value['gly'] = iconv('utf-8','gbk','"' . $v['gly'] . '"');
				$advance_info_value['dbjg'] = iconv('utf-8','gbk','"' . $v['dbjg'] . '"');
				$advance_info_value['dhbx'] = iconv('utf-8','gbk','"' . number_format($v['dhbx'],2) . '"');
				$advance_info_value['dhglf'] = iconv('utf-8','gbk','"' . number_format($v['dhglf'],2) . '"');
				$advance_info_value['dhfx'] = iconv('utf-8','gbk','"' . number_format($v['dhfx'],2) . '"');
				$advance_info_value['dhdsyqglf'] = iconv('utf-8','gbk','"' . number_format($v['dhdsyqglf'],2) . '"');
				
				
				$content_advance_info .= implode(",", $advance_info_value) . "\n";
			}	
			
			header("Content-Disposition: attachment; filename=advance_info_list.csv");
	    	echo $content_advance_info;  
		}
		else
		{
			if($page==1)
			$this->error(L("NO_RESULT"));
		}
				
	}
	
}
?>