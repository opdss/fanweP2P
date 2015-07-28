<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/uc.php';

class uc_ipsModule extends SiteBaseModule
{
	public function create()
	{
		$GLOBALS['tmpl']->assign("page_title","标的登记");

		$start_time = strim($_REQUEST['start_time']);
		$end_time = strim($_REQUEST['end_time']);
			
		$d = explode('-',$start_time);
		if (isset($_REQUEST['start_time']) && $start_time !="" && checkdate($d[1], $d[2], $d[0]) == false){
			$this->error("开始时间不是有效的时间格式:{$start_time}(yyyy-mm-dd)");
			exit;
		}
		
		$d = explode('-',$end_time);
		if ( isset($_REQUEST['end_time']) && strim($end_time) !="" &&  checkdate($d[1], $d[2], $d[0]) == false){
			$this->error("结束时间不是有效的时间格式:{$end_time}(yyyy-mm-dd)");
			exit;
		}
		
		if ($start_time!="" && strim($end_time) !="" && to_timespan($start_time) > to_timespan($end_time)){
			$this->error('开始时间不能大于结束时间:'.$start_time.'至'.$end_time);
			exit;
		}

		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");

		if($limit)
		{
			$limit = " limit ".$limit;
		}
		
		$className = getCollName();
		//环迅
		if(strtolower($className) == "ips")
		{
			$where = " where pErrCode in ('MG00000F','MG02500F','MG02501F','MG02503F','MG02505F') and pErrCode<>'MG02503F' and  d.user_id = ".$GLOBALS["user_info"]["id"]." "; 
			if(strim($start_time)!="")
			{
				$where .= " and UNIX_TIMESTAMP(pRegDate) >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and UNIX_TIMESTAMP(pRegDate) <=".  to_timespan(strim($end_time));
			}
			
			$list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."ips_register_subject irs left join ".DB_PREFIX."deal d on irs.deal_id = d.id ".$where ." order by irs.id desc ".$limit);	
			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."ips_register_subject irs left join ".DB_PREFIX."deal d on irs.deal_id = d.id ".$where);
			foreach($list as $k => $v)
			{
				$list[$k]["pTrdCycleType"] = $v["pTrdCycleType"] == 1?"天":"月";
			}
		}
		//易宝  //宝付
		elseif(strtolower($className) == "yeepay" || strtolower($className) == "baofoo")
		{
			$where = " where user_id = ".$GLOBALS["user_info"]["id"]." and mer_bill_no <>'' "; 
			if(strim($start_time)!="")
			{
				$where .= " and create_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and create_time <=".  to_timespan(strim($end_time));
			}
			$list = $GLOBALS['db']->getAll("select id as pBidNo,name,borrow_amount as pLendAmt,guarantees_money as pGuaranteesAmt,(services_fee / 100 * borrow_amount) as pLendFee,
			repay_time as pTrdCycleValue,
			repay_time_type as pTrdCycleType,
			FROM_UNIXTIME( create_time, '%Y-%m-%d') as pRegDate
			 from ".DB_PREFIX."deal ".$where ." order by id desc ".$limit);
			
			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal ".$where);
			 
			foreach($list as $k => $v)
			{
				$list[$k]["pTrdCycleType"] = $v["pTrdCycleType"] == "0"?"天":"月";
			}
		}
		
		$page = new Page($list_count,app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		$GLOBALS['tmpl']->assign("list",$list);
		$GLOBALS['tmpl']->assign("start_time",$start_time);
		$GLOBALS['tmpl']->assign("end_time",$end_time);
		
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_ips_create.html");
		$GLOBALS['tmpl']->display("page/uc.html");
		
	}
	public function recharge()
	{
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_MONEY_INCHARGE_LOG']);
		
		//定义条件
		$start_time = strim($_REQUEST['start_time']);
		$end_time = strim($_REQUEST['end_time']);
			
		$d = explode('-',$start_time);
		if (isset($_REQUEST['start_time']) && $start_time !="" && checkdate($d[1], $d[2], $d[0]) == false){
			$this->error("开始时间不是有效的时间格式:{$start_time}(yyyy-mm-dd)");
			exit;
		}
		
		$d = explode('-',$end_time);
		if ( isset($_REQUEST['end_time']) && strim($end_time) !="" &&  checkdate($d[1], $d[2], $d[0]) == false){
			$this->error("结束时间不是有效的时间格式:{$end_time}(yyyy-mm-dd)");
			exit;
		}
		
		if ($start_time!="" && strim($end_time) !="" && to_timespan($start_time) > to_timespan($end_time)){
			$this->error('开始时间不能大于结束时间:'.$start_time.'至'.$end_time);
			exit;
		}
		
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		if($limit)
		{
			$limit = " limit ".$limit;
		}
		
		$className = getCollName();
		//环迅
		if(strtolower($className) == "ips")
		{
			$where = " where pErrCode ='MG00000F' and user_id = ".$GLOBALS["user_info"]["id"];
			
			if(strim($start_time)!="")
			{
				$where .= " and UNIX_TIMESTAMP(pTrdDate) >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and UNIX_TIMESTAMP(pTrdDate) <=".  to_timespan(strim($end_time));
			}
			$list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."ips_do_dp_trade ".$where." order by id desc ".$limit);
			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."ips_do_dp_trade ".$where);
		}
		//易宝
		elseif(strtolower($className) == "yeepay")
		{
			$where = " where is_callback = 1 and yr.code = 1 and yr.platformUserNo = ".$GLOBALS["user_info"]["id"];
			
			if(strim($start_time)!="")
			{
				$where .= " and yr.create_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and yr.create_time <=".  to_timespan(strim($end_time));
			}
			
			$list = $GLOBALS['db']->getAll("select requestNo as pMerBillNo,idno as pIdentNo,
			real_name as pRealName, FROM_UNIXTIME( yr.create_time, '%Y-%m-%d' ) as pTrdDate,
			amount as pTrdAmt ,'2' as pChannelType ,case when feeMode ='PLATFORM' then 1 else 2 end as pIpsFeeType ,fee as pMerFee from ".DB_PREFIX."yeepay_recharge yr left join ".DB_PREFIX."user u on yr.platformUserNo = u.id ".$where." order by yr.id desc ".$limit);
			
			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."yeepay_recharge yr left join ".DB_PREFIX.".user u on yr.platformUserNo = u.id ".$where);
		}
		//宝付
		elseif(strtolower($className) == "baofoo")
		{
			$where = " where is_callback = 1 and yr.code = 'CSD000' and yr.user_id = ".$GLOBALS["user_info"]["id"];
			
			if(strim($start_time)!="")
			{
				$where .= " and yr.create_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and yr.create_time <=".  to_timespan(strim($end_time));
			}
			
			$list = $GLOBALS['db']->getAll("select order_id as pMerBillNo,idno as pIdentNo,
			real_name as pRealName, FROM_UNIXTIME( yr.create_time, '%Y-%m-%d' ) as pTrdDate,
			amount as pTrdAmt,'2' as pChannelType,fee_taken_on as pIpsFeeType ,mer_fee as pMerFee from ".DB_PREFIX."baofoo_recharge yr left join ".DB_PREFIX."user u on yr.user_id = u.id ".$where." order by yr.id desc ".$limit);
			
			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."baofoo_recharge yr left join ".DB_PREFIX."user u on yr.user_id = u.id ".$where);
		}

		$page = new Page($list_count,app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		$GLOBALS['tmpl']->assign("list",$list);
		$GLOBALS['tmpl']->assign("start_time",$start_time);
		$GLOBALS['tmpl']->assign("end_time",$end_time);
		
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_ips_recharge.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	public function transfer()
	{
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_IPS_TRANSFER']);
		
		$start_time = strim($_REQUEST['start_time']);
		$end_time = strim($_REQUEST['end_time']);
			
		$d = explode('-',$start_time);
		if (isset($_REQUEST['start_time']) && $start_time !="" && checkdate($d[1], $d[2], $d[0]) == false){
			$this->error("开始时间不是有效的时间格式:{$start_time}(yyyy-mm-dd)");
			exit;
		}
		
		$d = explode('-',$end_time);
		if ( isset($_REQUEST['end_time']) && strim($end_time) !="" &&  checkdate($d[1], $d[2], $d[0]) == false){
			$this->error("结束时间不是有效的时间格式:{$end_time}(yyyy-mm-dd)");
			exit;
		}
		
		if ($start_time!="" && strim($end_time) !="" && to_timespan($start_time) > to_timespan($end_time)){
			$this->error('开始时间不能大于结束时间:'.$start_time.'至'.$end_time);
			exit;
		}
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		if($limit)
		{
			$limit = " limit ".$limit;
		}
		
		$className = getCollName();
		//环迅
		if(strtolower($className) == "ips")
		{
			$where = " where pErrCode ='MG00000F' and user_id = ".$GLOBALS["user_info"]["id"];
			
			if(strim($start_time)!="")
			{
				$where .= " and UNIX_TIMESTAMP(pDwDate) >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and UNIX_TIMESTAMP(pDwDate) <=".  to_timespan(strim($end_time));
			}
			$list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."ips_do_dw_trade ".$where.$limit);
			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."ips_do_dw_trade ".$where);
		
		}
		//易宝
		elseif(strtolower($className) == "yeepay")
		{
			$where = " where yw.code ='1' and yw.is_callback = 1 and yw.platformUserNo = ".$GLOBALS["user_info"]["id"];
			
			if(strim($start_time)!="")
			{
				$where .= " and yw.create_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and yw.create_time <=".  to_timespan(strim($end_time));
			}
			$y_sql = "select requestNo  as pIpsBillNo,u.real_name as pRealName,
			FROM_UNIXTIME(yw.create_time ,'%Y-%m-%d')as pDwDate,amount as pTrdAmt,fee as pMerFee , 
			u.idno as pIdentNo,
			case when feeMode ='PLATFORM' then 1 else 2 end as pIpsFeeType
			 from ".DB_PREFIX."yeepay_withdraw yw left join ".DB_PREFIX."user u on yw.platformUserNo = u.id ".$where.$limit;
			$list = $GLOBALS['db']->getAll($y_sql);
			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."yeepay_withdraw yw left join ".DB_PREFIX."user u on yw.platformUserNo = u.id ".$where);
		}
		//宝付
		elseif(strtolower($className) == "baofoo")
		{
			$where = " where yw.code ='CSD000' and yw.is_callback = 1 and yw.user_id = ".$GLOBALS["user_info"]["id"];
			
			if(strim($start_time)!="")
			{
				$where .= " and yw.create_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and yw.create_time <=".  to_timespan(strim($end_time));
			}
			$y_sql = "select order_id as pIpsBillNo,u.real_name as pRealName,
			FROM_UNIXTIME(yw.create_time ,'%Y-%m-%d')as pDwDate,amount as pTrdAmt,fee as pMerFee , 
			u.idno as pIdentNo,
			fee_taken_on as pIpsFeeType
			 from ".DB_PREFIX."baofoo_fo_charge yw left join ".DB_PREFIX."user u on yw.user_id = u.id ".$where.$limit;
			$list = $GLOBALS['db']->getAll($y_sql);
			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."baofoo_fo_charge yw left join ".DB_PREFIX."user u on yw.user_id = u.id ".$where);
		}
		
		$page = new Page($list_count,app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		$GLOBALS['tmpl']->assign("list",$list);
		$GLOBALS['tmpl']->assign("start_time",$start_time);
		$GLOBALS['tmpl']->assign("end_time",$end_time);
		
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_ips_transfer.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	public function creditor()
	{
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['SPACE_LEND']);
		
		$start_time = strim($_REQUEST['start_time']);
		$end_time = strim($_REQUEST['end_time']);
			
		$d = explode('-',$start_time);
		if (isset($_REQUEST['start_time']) && $start_time !="" && checkdate($d[1], $d[2], $d[0]) == false){
			$this->error("开始时间不是有效的时间格式:{$start_time}(yyyy-mm-dd)");
			exit;
		}
		
		$d = explode('-',$end_time);
		if ( isset($_REQUEST['end_time']) && strim($end_time) !="" &&  checkdate($d[1], $d[2], $d[0]) == false){
			$this->error("结束时间不是有效的时间格式:{$end_time}(yyyy-mm-dd)");
			exit;
		}
		
		if ($start_time!="" && strim($end_time) !="" && to_timespan($start_time) > to_timespan($end_time)){
			$this->error('开始时间不能大于结束时间:'.$start_time.'至'.$end_time);
			exit;
		}
		
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		if($limit)
		{
			$limit = " limit ".$limit;
		}
		
		$className = getCollName();
		//环迅
		if(strtolower($className) == "ips")
		{
			$where = " where  pErrCode ='MG00000F'  and a.user_id = ".$GLOBALS["user_info"]["id"];
			if(strim($start_time)!="")
			{
				$where .= " and UNIX_TIMESTAMP(pMerDate) >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and UNIX_TIMESTAMP(pMerDate) <=".  to_timespan(strim($end_time));
			}
			$list = $GLOBALS['db']->getAll("select a.*,b.name as deal_name from ".DB_PREFIX."ips_register_creditor as a left join ".DB_PREFIX."deal as b on a.deal_id = b.id ".$where." order by id desc ".$limit);
		
			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."ips_register_creditor as a left join ".DB_PREFIX."deal as b on a.deal_id = b.id ".$where);
		}
		//易宝
		elseif(strtolower($className) == "yeepay")
		{
			$where = " where bizType = 'TENDER' and code ='1' and is_callback = 1 and a.platformUserNo = ".$GLOBALS["user_info"]["id"];
			if(strim($start_time)!="")
			{
				$where .= " and a.create_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and a.create_time <=".  to_timespan(strim($end_time));
			}
			$list = $GLOBALS['db']->getAll("select b.name as deal_name,
			a.tenderOrderNo as pBidNo,paymentAmount as pTrdAmt, 
			fee as pFee, paymentAmount as pTransferAmt,
			FROM_UNIXTIME(a.create_time ,'%Y-%m-%d')as pIpsTime,
			1 as pRegType
			 from ".DB_PREFIX."yeepay_cp_transaction as a left join ".DB_PREFIX."deal as b on a.tenderOrderNo = b.id ".$where." order by a.id desc ".$limit);
			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."yeepay_cp_transaction as a left join ".DB_PREFIX."deal as b on a.tenderOrderNo = b.id ".$where);
		}
		//宝付
		elseif(strtolower($className) == "baofoo")
		{
			$where = " where action_type = '1' and  code ='CSD000' and is_callback = 1 and a.load_user_id = ".$GLOBALS["user_info"]["id"];
			if(strim($start_time)!="")
			{
				$where .= " and (a.req_time/1000) >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and (a.req_time/1000) <=".  to_timespan(strim($end_time));
			}
			$list = $GLOBALS['db']->getAll("select b.name as deal_name,
			a.cus_id as pBidNo,load_amount as pTrdAmt, 
			fee as pFee, load_amount as pTransferAmt,
			FROM_UNIXTIME(a.req_time/1000 ,'%Y-%m-%d')as pIpsTime,
			1 as pRegType
			 from ".DB_PREFIX."baofoo_business as a left join ".DB_PREFIX."deal as b on a.cus_id = b.id ".$where." order by a.id desc ".$limit);
			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."baofoo_business as a left join ".DB_PREFIX."deal as b on a.cus_id = b.id ".$where);
		}
		

		$page = new Page($list_count,app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		$GLOBALS['tmpl']->assign("list",$list);
		$GLOBALS['tmpl']->assign("start_time",$start_time);
		$GLOBALS['tmpl']->assign("end_time",$end_time);
		
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_ips_creditor.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	public function repayment()
	{
		$GLOBALS['tmpl']->assign("page_title","还款单");
		
		
		$start_time = strim($_REQUEST['start_time']);
		$end_time = strim($_REQUEST['end_time']);
			
		$d = explode('-',$start_time);
		if (isset($_REQUEST['start_time']) && $start_time !="" && checkdate($d[1], $d[2], $d[0]) == false){
			$this->error("开始时间不是有效的时间格式:{$start_time}(yyyy-mm-dd)");
			exit;
		}
		
		$d = explode('-',$end_time);
		if ( isset($_REQUEST['end_time']) && strim($end_time) !="" &&  checkdate($d[1], $d[2], $d[0]) == false){
			$this->error("结束时间不是有效的时间格式:{$end_time}(yyyy-mm-dd)");
			exit;
		}
		
		if ($start_time!="" && strim($end_time) !="" && to_timespan($start_time) > to_timespan($end_time)){
			$this->error('开始时间不能大于结束时间:'.$start_time.'至'.$end_time);
			exit;
		}
		
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");   
		
		if($limit)
		{
			$limit = " limit ".$limit;
		}
		
		$className = getCollName();
		//环迅
		if(strtolower($className) == "ips")
		{
			$condition = " where ips.pErrCode = 'MG00000F' and d.user_id = ".$GLOBALS["user_info"]["id"];
		
			$sql = "select ips.*,ips.id as mid,d.`name` as deal_name,u.user_name, dr.* from ".DB_PREFIX."ips_repayment_new_trade as ips
			left join ".DB_PREFIX."deal d on d.id = ips.deal_id
			left join ".DB_PREFIX."deal_repay dr on dr.id = ips.deal_repay_id 
			left join ".DB_PREFIX."user u on u.id = d.user_id ";
			
			$count_sql = "select count(*) from ".DB_PREFIX."ips_repayment_new_trade as ips
			left join ".DB_PREFIX."deal d on d.id = ips.deal_id
			left join ".DB_PREFIX."deal_repay dr on dr.id = ips.deal_repay_id 
			left join ".DB_PREFIX."user u on u.id = d.user_id ";
			
			if(strim($start_time)!="")
			{
				$condition .= " and UNIX_TIMESTAMP(pRepaymentDate) >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$condition .= " and UNIX_TIMESTAMP(pRepaymentDate) <=".  to_timespan(strim($end_time));
			}
			
			$list = $GLOBALS['db']->getAll($sql.$condition." order by mid desc ".$limit);
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
		}
		//易宝
		elseif(strtolower($className) == "yeepay")
		{
			$condition = " where yct.bizType = 'REPAYMENT' and yct.is_complete_transaction = 1 and yct.code ='1' and yct.is_callback = 1 and yct.platformUserNo = ".$GLOBALS["user_info"]["id"];
			
			$sql = "select yct.tenderOrderNo as pBidNo, yct.*,yct.id as mid,d.`name` as deal_name, 1 as pRepayType,true_repay_money as pOutAmt,
			u.user_name, dr.* , FROM_UNIXTIME(yct.create_time ,'%Y-%m-%d')as pRepaymentDate
			from ".DB_PREFIX."yeepay_cp_transaction as yct
			left join ".DB_PREFIX."deal d on d.id = yct.tenderOrderNo
			left join ".DB_PREFIX."deal_repay dr on dr.id = yct.deal_repay_id 
			left join ".DB_PREFIX."user u on u.id = d.user_id ";
			
			$count_sql = "select count(*) from ".DB_PREFIX."yeepay_cp_transaction as yct
			left join ".DB_PREFIX."deal d on d.id = yct.tenderOrderNo
			left join ".DB_PREFIX."deal_repay dr on dr.id = yct.deal_repay_id 
			left join ".DB_PREFIX."user u on u.id = d.user_id ";
			if(strim($start_time)!="")
			{
				$condition .= " and yct.repay_start_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$condition .= " and yct.repay_start_time <=".  to_timespan(strim($end_time));
			}
			$list = $GLOBALS['db']->getAll($sql.$condition." order by mid desc ".$limit);
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
		}
		//宝付
		elseif(strtolower($className) == "baofoo")
		{
			$condition = " where yct.action_type = 4 and yct.code ='CSD000' and yct.is_callback = 1 and yct.brw_id = ".$GLOBALS["user_info"]["id"];
			
			$sql = "select yct.cus_id as pBidNo, yct.*,yct.id as mid,d.`name` as deal_name, 1 as pRepayType,true_repay_money as pOutAmt,
			u.user_name, dr.* , FROM_UNIXTIME(yct.req_time/1000 ,'%Y-%m-%d')as pRepaymentDate
			from ".DB_PREFIX."baofoo_business as yct
			left join ".DB_PREFIX."deal d on d.id = yct.cus_id
			left join ".DB_PREFIX."deal_repay dr on dr.id = yct.deal_repay_id 
			left join ".DB_PREFIX."user u on u.id = d.user_id ";
			
			$count_sql = "select count(*) from ".DB_PREFIX."baofoo_business as yct
			left join ".DB_PREFIX."deal d on d.id = yct.cus_id
			left join ".DB_PREFIX."deal_repay dr on dr.id = yct.deal_repay_id 
			left join ".DB_PREFIX."user u on u.id = d.user_id ";
			
			if(strim($start_time)!="")
			{
				$condition .= " and yct.repay_start_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$condition .= " and yct.repay_start_time <=".  to_timespan(strim($end_time));
			}

			$list = $GLOBALS['db']->getAll($sql.$condition." order by mid desc ".$limit);
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
		}

		foreach($list as $k => $v)
		{
			if($v["status"] == 0)
			{
				$v["status"] = "提前";
			}
			if($v["status"] == 1)
			{
				$v["status"] = "准时";
			}
			if($v["status"] == 2)
			{
				$v["status"] = "逾期";
			}
			if($v["status"] == 3)
			{
				$v["status"] = "严重逾期";
			}
			$list[$k]["status"] = $v["status"];
		}
		
		$page = new Page($list_count,app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		$GLOBALS['tmpl']->assign('list',$list);
		$GLOBALS['tmpl']->assign("start_time",$start_time);
		$GLOBALS['tmpl']->assign("end_time",$end_time);
		
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_ips_repayment.html");
		$GLOBALS['tmpl']->display("page/uc.html");
		
	}
	public function repayment_view()
	{
		$GLOBALS['tmpl']->assign("page_title","还款单明细");
		
		if(isset($_REQUEST['id'])&&intval(strim($_REQUEST['id']))>0)
		{		
			
		}
		else
		{
			return;
			//$this->error (l("INVALID_OPERATION"),$ajax);
		}
		
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		
		if($limit)
		{
			$limit = " limit ".$limit;
		}
		
		$className = getCollName();
		//环迅
		if(strtolower($className) == "ips")
		{
			$condition = " where 1 = 1 ";
			$condition .= " and d.pid = ".intval(strim($_REQUEST['id']));
			
			$GLOBALS['tmpl']->assign('id', intval(strim($_REQUEST['id'])));
			
			$sql = "select d.*,r.*,d.id as mid ,u.user_name,dl.name as deal_name,tu.user_name as t_user_name 
			from ".DB_PREFIX."ips_repayment_new_trade_detail as d
			left JOIN ".DB_PREFIX."deal_load_repay r on r.id = d.deal_load_repay_id
			left join ".DB_PREFIX."user u on u.id = r.user_id
			left join ".DB_PREFIX."user tu on tu.id = r.t_user_id left join ".DB_PREFIX."deal as dl on r.deal_id = dl.id";
			
			$count_sql = "select count(*) 
			from ".DB_PREFIX."ips_repayment_new_trade_detail as d
			left JOIN ".DB_PREFIX."deal_load_repay r on r.id = d.deal_load_repay_id
			left join ".DB_PREFIX."user u on u.id = r.user_id
			left join ".DB_PREFIX."user tu on tu.id = r.t_user_id left join ".DB_PREFIX."deal as dl on r.deal_id = dl.id";
	
			$list = $GLOBALS['db']->getAll( $sql.$condition.$limit);
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
		}
		//易宝
		elseif(strtolower($className) == "yeepay")
		{
			$condition = " where 1=1";
			
			$condition .= " and d.pid = ".intval(strim($_REQUEST['id']));
			
			$GLOBALS['tmpl']->assign('id', intval(strim($_REQUEST['id'])));
			
			$sql = "select d.*,r.*,d.id as mid ,u.user_name,dl.name as deal_name,tu.user_name as t_user_name 
			from ".DB_PREFIX."yeepay_cp_transaction_detail as d
			left JOIN ".DB_PREFIX."deal_load_repay r on r.id = d.deal_load_repay_id
			left join ".DB_PREFIX."user u on u.id = r.user_id
			left join ".DB_PREFIX."user tu on tu.id = r.t_user_id left join ".DB_PREFIX."deal as dl on r.deal_id = dl.id";
			
			$count_sql = "select count(*) 
			from ".DB_PREFIX."yeepay_cp_transaction_detail as d
			left JOIN ".DB_PREFIX."deal_load_repay r on r.id = d.deal_load_repay_id
			left join ".DB_PREFIX."user u on u.id = r.user_id
			left join ".DB_PREFIX."user tu on tu.id = r.t_user_id left join ".DB_PREFIX."deal as dl on r.deal_id = dl.id";
	
			$list = $GLOBALS['db']->getAll( $sql.$condition.$limit);
			
			//取得满足条件的记录数
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
		}
		//宝付
		elseif(strtolower($className) == "baofoo")
		{
			$condition = " where 1=1";
			
			$condition .= " and d.pid = ".intval(strim($_REQUEST['id']));
			
			$GLOBALS['tmpl']->assign('id', intval(strim($_REQUEST['id'])));
			
			$sql = "select d.*,r.*,d.id as mid ,u.user_name,dl.name as deal_name,tu.user_name as t_user_name 
			from ".DB_PREFIX."baofoo_business_detail as d
			left JOIN ".DB_PREFIX."deal_load_repay r on r.id = d.deal_load_repay_id
			left join ".DB_PREFIX."user u on u.id = r.user_id
			left join ".DB_PREFIX."user tu on tu.id = r.t_user_id left join ".DB_PREFIX."deal as dl on r.deal_id = dl.id";
			
			$count_sql = "select count(*) 
			from ".DB_PREFIX."baofoo_business_detail as d
			left JOIN ".DB_PREFIX."deal_load_repay r on r.id = d.deal_load_repay_id
			left join ".DB_PREFIX."user u on u.id = r.user_id
			left join ".DB_PREFIX."user tu on tu.id = r.t_user_id left join ".DB_PREFIX."deal as dl on r.deal_id = dl.id";
			
			$list = $GLOBALS['db']->getAll( $sql.$condition.$limit);
			
			//取得满足条件的记录数
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
		}
		
		

		foreach($list as $k => $v)
		{
			$list[$k]["ll_key"] = $v["l_key"] + 1;
			if($v["status"] == 0)
			{
				$v["status"] = "提前";
			}
			if($v["status"] == 1)
			{
				$v["status"] = "准时";
			}
			if($v["status"] == 2)
			{
				$v["status"] = "逾期";
			}
			if($v["status"] == 3)
			{
				$v["status"] = "严重逾期";
			}
			$list[$k]["status"] = $v["status"];
		}
		
		$page = new Page($list_count,app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		$GLOBALS['tmpl']->assign('list',$list);
		
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_ips_repayment_view.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	//回款单
	public function receivedpayment()
	{
		$GLOBALS['tmpl']->assign("page_title","回款单");
		
		$user_id = intval($GLOBALS["user_info"]["id"]);
		
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		
		if($limit)
		{
			$limit = " limit ".$limit;
		}
		
		$className = getCollName();
		//环迅
		if(strtolower($className) == "ips")
		{
			$condition = " where (r.t_user_id = ".$user_id ." or (r.user_id = ".$user_id ." and r.t_user_id = 0)) and pStatus = 'Y'";
		
			$sql = "select d.*,r.*,deal_t.pBidNo,d.id as mid ,u.user_name,dl.name as deal_name from ".DB_PREFIX."ips_repayment_new_trade_detail as d
			left JOIN ".DB_PREFIX."deal_load_repay r on r.id = d.deal_load_repay_id
			left JOIN ".DB_PREFIX."deal dl on dl.id = r.deal_id
			left join ".DB_PREFIX."user u on u.id = dl.user_id
			left JOIN ".DB_PREFIX."ips_repayment_new_trade deal_t on deal_t.id = d.pid";
			
			$count_sql = "select count(*) from ".DB_PREFIX."ips_repayment_new_trade_detail as d
			left JOIN ".DB_PREFIX."deal_load_repay r on r.id = d.deal_load_repay_id
			left JOIN ".DB_PREFIX."deal dl on dl.id = r.deal_id
			left join ".DB_PREFIX."user u on u.id = dl.user_id";
	
			$list = $GLOBALS['db']->getAll( $sql.$condition." order by d.id desc".$limit);
			
			//取得满足条件的记录数
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
			
		}
		//易宝
		elseif(strtolower($className) == "yeepay")
		{
			$condition = " where (r.t_user_id = ".$user_id ." or (r.user_id = ".$user_id ." and r.t_user_id = 0)) ";
			$condition .= " and deal_t.bizType = 'REPAYMENT' and deal_t.is_complete_transaction = 1 and deal_t.code ='1' and deal_t.is_callback = 1 ";
			$sql = "select d.*,r.*,deal_t.tenderOrderNo as pBidNo,d.id as mid ,u.user_name,dl.name as deal_name 
			from ".DB_PREFIX."yeepay_cp_transaction_detail as d
			left JOIN ".DB_PREFIX."deal_load_repay r on r.id = d.deal_load_repay_id
			left JOIN ".DB_PREFIX."deal dl on dl.id = r.deal_id
			left join ".DB_PREFIX."user u on u.id = dl.user_id
			left JOIN ".DB_PREFIX."yeepay_cp_transaction deal_t on deal_t.id = d.pid";
			
			$count_sql = "select count(*) 
			from ".DB_PREFIX."yeepay_cp_transaction_detail as d
			left JOIN ".DB_PREFIX."deal_load_repay r on r.id = d.deal_load_repay_id
			left JOIN ".DB_PREFIX."deal dl on dl.id = r.deal_id
			left join ".DB_PREFIX."user u on u.id = dl.user_id
			left JOIN ".DB_PREFIX."yeepay_cp_transaction deal_t on deal_t.id = d.pid";
			
			$list = $GLOBALS['db']->getAll( $sql.$condition." order by d.id desc".$limit);
			
			//取得满足条件的记录数
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
		}
		//宝付
		elseif(strtolower($className) == "baofoo")
		{
			$condition = " where (r.t_user_id = ".$user_id ." or (r.user_id = ".$user_id ." and r.t_user_id = 0)) ";
		
			$sql = "select d.*,r.*,deal_t.cus_id as pBidNo,d.id as mid ,u.user_name,dl.name as deal_name 
			from ".DB_PREFIX."baofoo_business_detail as d
			left JOIN ".DB_PREFIX."deal_load_repay r on r.id = d.deal_load_repay_id
			left JOIN ".DB_PREFIX."deal dl on dl.id = r.deal_id
			left join ".DB_PREFIX."user u on u.id = dl.user_id
			left JOIN ".DB_PREFIX."baofoo_business deal_t on deal_t.id = d.pid";
			
			$count_sql = "select count(*) 
			from ".DB_PREFIX."baofoo_business_detail as d
			left JOIN ".DB_PREFIX."deal_load_repay r on r.id = d.deal_load_repay_id
			left JOIN ".DB_PREFIX."deal dl on dl.id = r.deal_id
			left join ".DB_PREFIX."user u on u.id = dl.user_id";

			$list = $GLOBALS['db']->getAll( $sql.$condition." order by d.id desc".$limit);
			
			//取得满足条件的记录数
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
		}
		
		foreach($list as $k => $v)
		{
			if($v["status"] == 0)
			{
				$v["status"] = "提前";
			}
			if($v["status"] == 1)
			{
				$v["status"] = "准时";
			}
			if($v["status"] == 2)
			{
				$v["status"] = "逾期";
			}
			if($v["status"] == 3)
			{
				$v["status"] = "严重逾期";
			}
			$list[$k]["status"] = $v["status"];
		}
		
		$page = new Page($list_count,app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		$GLOBALS['tmpl']->assign('list',$list);
		$GLOBALS['tmpl']->assign("start_time",$start_time);
		$GLOBALS['tmpl']->assign("end_time",$end_time);
		
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_ips_receivedpayment.html");
		$GLOBALS['tmpl']->display("page/uc.html");
		
	}
	public function fullscale()
	{
		$GLOBALS['tmpl']->assign("page_title","满标放款");
		
		$start_time = strim($_REQUEST['start_time']);
		$end_time = strim($_REQUEST['end_time']);
			
		$d = explode('-',$start_time);
		if (isset($_REQUEST['start_time']) && $start_time !="" && checkdate($d[1], $d[2], $d[0]) == false){
			$this->error("开始时间不是有效的时间格式:{$start_time}(yyyy-mm-dd)");
			exit;
		}
		
		$d = explode('-',$end_time);
		if ( isset($_REQUEST['end_time']) && strim($end_time) !="" &&  checkdate($d[1], $d[2], $d[0]) == false){
			$this->error("结束时间不是有效的时间格式:{$end_time}(yyyy-mm-dd)");
			exit;
		}
		
		if ($start_time!="" && strim($end_time) !="" && to_timespan($start_time) > to_timespan($end_time)){
			$this->error('开始时间不能大于结束时间:'.$start_time.'至'.$end_time);
			exit;
		}

		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		if($limit)
		{
			$limit = " limit ".$limit;
		}
		
		$className = getCollName();
		//环迅
		if(strtolower($className) == "ips")
		{
			$condition = " and t.pErrCode = 'MG00000F'  and d.user_id = ".$GLOBALS["user_info"]["id"];
			
			if(strim($start_time)!="")
			{
				$condition .= " and UNIX_TIMESTAMP(pDate) >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$condition .= " and UNIX_TIMESTAMP(pDate) <=".  to_timespan(strim($end_time));
			}
			
			$sql = "select t.*,d.`name`,u.user_name,d.borrow_amount,
			d.borrow_amount*CONVERT(d.services_fee,DECIMAL)*0.01 as deal_fee,
			(d.borrow_amount - (d.borrow_amount*CONVERT(d.services_fee,DECIMAL)*0.01)) as loan_amount
			 from ".DB_PREFIX."ips_transfer as t
			LEFT JOIN ".DB_PREFIX."deal as d on d.id = t.deal_id
			LEFT JOIN ".DB_PREFIX."user as u on u.id = d.user_id where t.pTransferType = 1 ";
			
			$count_sql = "select count(*) from ".DB_PREFIX."ips_transfer as t
			LEFT JOIN ".DB_PREFIX."deal as d on d.id = t.deal_id
			LEFT JOIN ".DB_PREFIX."user as u on u.id = d.user_id where t.pTransferType = 1 ";
			
			//取得满足条件的记录数
			$list = $GLOBALS['db']->getAll( $sql.$condition." order by id desc ".$limit);
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
			//print_r($count);die;
			//$name=$this->getActionName();
		}
		//易宝
		elseif(strtolower($className) == "yeepay")
		{
			$condition = " where  bizType = 'TENDER' and is_complete_transaction = 1 and t.code ='1' and is_callback = 1 and d.user_id = ".$GLOBALS["user_info"]["id"];
			
			if(strim($start_time)!="")
			{
				$condition .= " and t.create_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$condition .= " and t.create_time <=".  to_timespan(strim($end_time));
			}
			
			$sql = "select d.id,d.`name`,u.user_name,d.borrow_amount as borrow_amount,d.id as pBidNo,
			d.borrow_amount*CONVERT(d.services_fee,DECIMAL)*0.01 as deal_fee,
			(d.borrow_amount - (d.borrow_amount*CONVERT(d.services_fee,DECIMAL)*0.01)) as loan_amount,
			1 as pTransferMode, requestNo as pIpsBillNo, FROM_UNIXTIME(t.update_time ,'%Y-%m-%d') as pIpsTime
			 from ".DB_PREFIX."yeepay_cp_transaction as t
			LEFT JOIN ".DB_PREFIX."deal as d on d.id = t.tenderOrderNo
			LEFT JOIN ".DB_PREFIX."user as u on u.id = d.user_id ";
			
			$count_sql = "select count(*) from ".DB_PREFIX."yeepay_cp_transaction as t
			LEFT JOIN ".DB_PREFIX."deal as d on d.id = t.tenderOrderNo
			LEFT JOIN ".DB_PREFIX."user as u on u.id = d.user_id ";
			
			//取得满足条件的记录数
			$list = $GLOBALS['db']->getAll( $sql.$condition." group by t.tenderOrderNo order by t.id desc ".$limit);
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);

			//$name=$this->getActionName();
		}
		//宝付
		elseif(strtolower($className) == "baofoo")
		{
			$condition = " where  action_type = '2' and t.code ='CSD000' and is_callback = 1 and d.load_user_id = ".$GLOBALS["user_info"]["id"];
			
			if(strim($start_time)!="")
			{
				$condition .= " and (t.req_time/1000) >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$condition .= " and (t.req_time/1000) <=".  to_timespan(strim($end_time));
			}
			
			$sql = "select d.id,d.`name`,u.user_name,d.borrow_amount as borrow_amount,d.id as pBidNo,
			d.borrow_amount*CONVERT(d.services_fee,DECIMAL)*0.01 as deal_fee,
			(d.borrow_amount - (d.borrow_amount*CONVERT(d.services_fee,DECIMAL)*0.01)) as loan_amount,
			1 as pTransferMode, order_id as pIpsBillNo, FROM_UNIXTIME(t.req_time/1000 ,'%Y-%m-%d') as pIpsTime
			 from ".DB_PREFIX."baofoo_business as t
			LEFT JOIN ".DB_PREFIX."deal as d on d.id = t.cus_id
			LEFT JOIN ".DB_PREFIX."user as u on u.id = d.user_id ";
			
			$count_sql = "select count(*) from ".DB_PREFIX."baofoo_business as t
			LEFT JOIN ".DB_PREFIX."deal as d on d.id = t.cus_id
			LEFT JOIN ".DB_PREFIX."user as u on u.id = d.user_id ";
			
			//print_r($sql.$condition);die;
			//取得满足条件的记录数
			$list = $GLOBALS['db']->getAll( $sql.$condition."  group by t.cus_id order by id desc ".$limit);

			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
		}

		$page = new Page($list_count,app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		$GLOBALS['tmpl']->assign('list',$list);
		$GLOBALS['tmpl']->assign("start_time",$start_time);
		$GLOBALS['tmpl']->assign("end_time",$end_time);
		
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_ips_fullscale.html");
		$GLOBALS['tmpl']->display("page/uc.html");
		
	}
	public function fullscale_view()
	{
		if(isset($_REQUEST['id'])&&intval(strim($_REQUEST['id']))>0)
		{		
			
		}
		else
		{
			return;
			//$this->error (l("INVALID_OPERATION"),$ajax);
		}
		
		$GLOBALS['tmpl']->assign("page_title","满标放款明细");
		
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		if($limit)
		{
			$limit = " limit ".$limit;
		}
		
		$className = getCollName();
		
		//环迅
		if(strtolower($className) == "ips")
		{
			$condition = " where 1=1 ";
			$condition .= " and t.pid = ".intval(strim($_REQUEST['id']));
			$GLOBALS['tmpl']->assign('id',intval(strim($_REQUEST['id'])));
			$sql = "select t.*,t.id as mid,l.* from ".DB_PREFIX."ips_transfer_detail as t LEFT JOIN ".DB_PREFIX."ips_transfer it on t.pid = it.id
	LEFT JOIN ".DB_PREFIX."deal_load l on l.deal_id = it.deal_id and l.pMerBillNo = t.pOriMerBillNo ";
			
			
			$count_sql = "select count(*) from ".DB_PREFIX."ips_transfer_detail as t LEFT JOIN ".DB_PREFIX."ips_transfer it on t.pid = it.id
	LEFT JOIN ".DB_PREFIX."deal_load l on l.deal_id = it.deal_id and l.pMerBillNo = t.pOriMerBillNo ";
	
			$list = $GLOBALS['db']->getAll( $sql.$condition.$limit);
			//取得满足条件的记录数
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
			//print_r($count);die;
			//$name=$this->getActionName();

		}
		//易宝
		elseif(strtolower($className) == "yeepay")
		{
			$condition = " where  bizType = 'TENDER' and is_complete_transaction = 1 and t.code ='1' and is_callback = 1 ";
			$condition .= " and t.tenderOrderNo = ".intval(strim($_REQUEST['id']));
			
			$GLOBALS['tmpl']->assign('id',intval(strim($_REQUEST['id'])));
			
			$sql = "select t.id as mid,l.*,
			l.pMerBillNo as pIpsDetailBillNo,l.money as pTrdAmt,t.fee as pIpsFee
			 from ".DB_PREFIX."yeepay_cp_transaction as t
	LEFT JOIN ".DB_PREFIX."deal_load l on  l.pMerBillNo = t.requestNo ";
			

			$count_sql = "select count(*) from ".DB_PREFIX."yeepay_cp_transaction as t 
	LEFT JOIN ".DB_PREFIX."deal_load l on  l.pMerBillNo = t.requestNo ";

			$list = $GLOBALS['db']->getAll( $sql.$condition.$limit);
			
			//取得满足条件的记录数
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
			//print_r($count);die;
			//$name=$this->getActionName();
		}
		//宝付
		elseif(strtolower($className) == "baofoo")
		{
			$condition = " where  action_type = '2' and t.code ='CSD000' and is_callback = 1 ";
			
			$condition .= " and t.cus_id = ".intval(strim($_REQUEST['id']));
			
			$GLOBALS['tmpl']->assign('id',intval(strim($_REQUEST['id'])));
			
			$sql = "select t.id as mid,l.*,
			l.pMerBillNo as pIpsDetailBillNo,l.money as pTrdAmt,t.fee as pIpsFee
			 from ".DB_PREFIX."baofoo_business as t
	LEFT JOIN ".DB_PREFIX."deal_load l on  l.pMerBillNo = t.order_id ";
			
			$count_sql = "select count(*) from ".DB_PREFIX."baofoo_business as t 
	LEFT JOIN ".DB_PREFIX."deal_load l on  l.pMerBillNo = t.order_id ";
			
			$list = $GLOBALS['db']->getAll( $sql.$condition.$limit);
			//取得满足条件的记录数
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
			//print_r($count);die;
			//$name=$this->getActionName();
		}
		
		$page = new Page($list_count,app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		$GLOBALS['tmpl']->assign('list',$list);
		$GLOBALS['tmpl']->assign("start_time",$start_time);
		$GLOBALS['tmpl']->assign("end_time",$end_time);
		
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_ips_fullscale_view.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	public function ips_transfer()
	{
		$GLOBALS['tmpl']->assign("page_title","债权转让");
		
		$start_time = strim($_REQUEST['start_time']);
		$end_time = strim($_REQUEST['end_time']);
			
		$d = explode('-',$start_time);
		if (isset($_REQUEST['start_time']) && $start_time !="" && checkdate($d[1], $d[2], $d[0]) == false){
			$this->error("开始时间不是有效的时间格式:{$start_time}(yyyy-mm-dd)");
			exit;
		}
		
		$d = explode('-',$end_time);
		if ( isset($_REQUEST['end_time']) && strim($end_time) !="" &&  checkdate($d[1], $d[2], $d[0]) == false){
			$this->error("结束时间不是有效的时间格式:{$end_time}(yyyy-mm-dd)");
			exit;
		}
		
		if ($start_time!="" && strim($end_time) !="" && to_timespan($start_time) > to_timespan($end_time)){
			$this->error('开始时间不能大于结束时间:'.$start_time.'至'.$end_time);
			exit;
		}
		
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		if($limit)
		{
			$limit = " limit ".$limit;
		}
		
		$className = getCollName();
		
		//环迅
		if(strtolower($className) == "ips")
		{
			$condition = " and t.pErrCode = 'MG00000F' and (dlt.user_id = ".$GLOBALS["user_info"]["id"]." or dlt.t_user_id = ".$GLOBALS["user_info"]["id"].")";
			
			if(strim($_REQUEST['pBidNo'])!='')
			{		
				$condition .= " and d.id like '%".strim($_REQUEST['pBidNo'])."%'";
			}
			if(strim($_REQUEST['deal_name'])!='')
			{		
				$condition .= " and d.name like '%".strim($_REQUEST['deal_name'])."%'";
			}
			if(strim($start_time)!="")
			{
				$condition .= " and UNIX_TIMESTAMP(pDate) >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$condition .= " and UNIX_TIMESTAMP(pDate) <=".  to_timespan(strim($end_time));
			}
		
			$sql = "select dlt.id as mid,t.*,d.`name`,u.user_name,tu.user_name as t_user_name,bu.user_name as b_user_name,(dlt.load_money - dlt.transfer_amount) as leave_money from ".DB_PREFIX."ips_transfer as t
			LEFT JOIN ".DB_PREFIX."deal as d on d.id = t.deal_id
			LEFT JOIN ".DB_PREFIX."deal_load_transfer as dlt on dlt.id = t.ref_data
			LEFT JOIN ".DB_PREFIX."user as u on u.id = dlt.user_id
			left join ".DB_PREFIX."user tu on tu.id = dlt.t_user_id
			left join ".DB_PREFIX."user bu on bu.id = d.user_id
			where t.pTransferType = 4 ";
					
					$count_sql = "select count(*) from ".DB_PREFIX."ips_transfer as t
			LEFT JOIN ".DB_PREFIX."deal as d on d.id = t.deal_id
			LEFT JOIN ".DB_PREFIX."deal_load_transfer as dlt on dlt.id = t.ref_data
			LEFT JOIN ".DB_PREFIX."user as u on u.id = dlt.user_id
			left join ".DB_PREFIX."user tu on tu.id = dlt.t_user_id
			left join ".DB_PREFIX."user bu on bu.id = d.user_id
			where t.pTransferType = 4 ";
	
			//print_r($sql.$condition);die;
			$list = $GLOBALS['db']->getAll( $sql.$condition." order by t.id desc ".$limit);
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
			//print_r($count);die;
			//$name=$this->getActionName();
		}
		//易宝
		elseif(strtolower($className) == "yeepay")
		{
			
			$condition = " where bizType = 'CREDIT_ASSIGNMENT' and is_complete_transaction = 1 and t.code ='1' and is_callback = 1  and (dlt.user_id = ".$GLOBALS["user_info"]["id"]." or dlt.t_user_id = ".$GLOBALS["user_info"]["id"].")";
			
			if(strim($_REQUEST['pBidNo'])!='')
			{		
				$condition .= " and d.id like '%".strim($_REQUEST['pBidNo'])."%'";
			}
			if(strim($_REQUEST['deal_name'])!='')
			{		
				$condition .= " and d.name like '%".strim($_REQUEST['deal_name'])."%'";
			}
			if(strim($start_time)!="")
			{
				$condition .= " and t.update_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$condition .= " and t.update_time <=".  to_timespan(strim($end_time));
			}
		
			$sql = "select dlt.id as mid,t.*,d.`name`,u.user_name,tu.user_name as t_user_name,bu.user_name as b_user_name,dlt.ips_status,dlt.t_user_id ,
			requestNo as pIpsBillNo,tenderOrderNo as pBidNo,FROM_UNIXTIME(t.create_time ,'%Y-%m-%d') as pDate,
			FROM_UNIXTIME(t.update_time ,'%Y-%m-%d') as pIpsTime
			from ".DB_PREFIX."yeepay_cp_transaction as t
			LEFT JOIN ".DB_PREFIX."deal as d on d.id = t.tenderOrderNo
			LEFT JOIN ".DB_PREFIX."deal_load_transfer as dlt on dlt.id = t.transfer_id
			LEFT JOIN ".DB_PREFIX."user as u on u.id = dlt.user_id
			left join ".DB_PREFIX."user tu on tu.id = dlt.t_user_id
			left join ".DB_PREFIX."user bu on bu.id = d.user_id";
			
			$count_sql = "select count(*) 
			from ".DB_PREFIX."yeepay_cp_transaction as t
			LEFT JOIN ".DB_PREFIX."deal as d on d.id = t.tenderOrderNo
			LEFT JOIN ".DB_PREFIX."deal_load_transfer as dlt on dlt.id = t.transfer_id
			LEFT JOIN ".DB_PREFIX."user as u on u.id = dlt.user_id
			left join ".DB_PREFIX."user tu on tu.id = dlt.t_user_id
			left join ".DB_PREFIX."user bu on bu.id = d.user_id";

			//print_r($sql.$condition);die;
			$list = $GLOBALS['db']->getAll( $sql.$condition." order by t.id desc ".$limit);
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
			//print_r($count);die;
			//$name=$this->getActionName();
		}
		//宝付
		elseif(strtolower($className) == "baofoo")
		{
			$condition = " where ref_type = '1' and t.code ='CSD000'  and (dlt.user_id = ".$GLOBALS["user_info"]["id"]." or dlt.t_user_id = ".$GLOBALS["user_info"]["id"].")";
			
			if(strim($_REQUEST['pBidNo'])!='')
			{		
				$condition .= " and d.id like '%".strim($_REQUEST['pBidNo'])."%'";
			}
			if(strim($_REQUEST['deal_name'])!='')
			{		
				$condition .= " and d.name like '%".strim($_REQUEST['deal_name'])."%'";
			}
			if(strim($start_time)!="")
			{
				$condition .= " and (t.req_time/1000) >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$condition .= " and (t.req_time/1000) <=".  to_timespan(strim($end_time));
			}
		 
			$sql = "select dlt.id as mid, t.*,d.`name`,u.user_name,tu.user_name as t_user_name,bu.user_name as b_user_name,dlt.ips_status,dlt.t_user_id ,
			order_id as pIpsBillNo,dlt.deal_id as pBidNo,FROM_UNIXTIME(t.req_time/1000 ,'%Y-%m-%d') as pDate,
			FROM_UNIXTIME(t.req_time/1000 ,'%Y-%m-%d') as pIpsTime
			from ".DB_PREFIX."baofoo_acct_trans as t
			LEFT JOIN ".DB_PREFIX."deal_load_transfer as dlt on dlt.id = t.ref_id
			LEFT JOIN ".DB_PREFIX."deal as d on d.id = dlt.deal_id
			LEFT JOIN ".DB_PREFIX."user as u on u.id = dlt.user_id
			left join ".DB_PREFIX."user tu on tu.id = dlt.t_user_id
			left join ".DB_PREFIX."user bu on bu.id = d.user_id";
			
			$count_sql = "select count(*) 
			from ".DB_PREFIX."baofoo_acct_trans as t
			LEFT JOIN ".DB_PREFIX."deal_load_transfer as dlt on dlt.id = t.ref_id
			LEFT JOIN ".DB_PREFIX."deal as d on d.id = dlt.deal_id
			LEFT JOIN ".DB_PREFIX."user as u on u.id = dlt.user_id
			left join ".DB_PREFIX."user tu on tu.id = dlt.t_user_id
			left join ".DB_PREFIX."user bu on bu.id = d.user_id";
			//print_r($sql.$condition);die;
			//print_r($sql.$condition);die;
			$list = $GLOBALS['db']->getAll( $sql.$condition." order by t.id desc ".$limit);
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
		}
		require_once APP_ROOT_PATH.'app/Lib/deal.php';
		require_once APP_ROOT_PATH.'app/Lib/common.php';
		
		foreach($list as $k=>$v)
		{
			$condition = " AND dlt.id='".$v["mid"]."'";
			//$condition = ' AND dlt.id='.$v["mid"].' AND d.deal_status >= 4 and d.is_effect=1 and d.is_delete=0  and d.repay_time_type =1 and  d.publish_wait=0 ';
			$union_sql = " LEFT JOIN ".DB_PREFIX."deal_load_transfer dlt ON dlt.load_id = dl.id ";
			$info = get_transfer($union_sql,$condition);
			
			$list[$k]["left_benjin_format"] = $info["left_benjin_format"];
			$list[$k]["left_lixi_format"] = $info["left_lixi_format"];
			$list[$k]["transfer_amount_format"] = $info["transfer_amount_format"];
			$list[$k]["transfer_income_format"] = $info["transfer_income_format"];
		}
		
		$page = new Page($list_count,app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('user_id',$GLOBALS["user_info"]["id"]);
		$GLOBALS['tmpl']->assign('pages',$p);
		
		$GLOBALS['tmpl']->assign('list',$list);
		$GLOBALS['tmpl']->assign("start_time",$start_time);
		$GLOBALS['tmpl']->assign("end_time",$end_time);
		
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_ips_ips_transfer.html");
		$GLOBALS['tmpl']->display("page/uc.html");
		
	}
	public function ips_transfer_view()
	{
		$GLOBALS['tmpl']->assign("page_title","债权转让明细");	
		
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		if($limit)
		{
			$limit = " limit ".$limit;
		}
		
		if(isset($_REQUEST['id'])&&intval(strim($_REQUEST['id']))>0)
		{		
			
			//$condition .= " and t.pid = ".intval(strim($_REQUEST['id']));
			$GLOBALS['tmpl']->assign('id',intval(strim($_REQUEST['id'])));
		}
		else
		{
			return ;
			//$this->error (l("INVALID_OPERATION"),$ajax);
		}
			
		$className = getCollName();
		
		//环迅
		if(strtolower($className) == "ips")
		{
			$p_sql = "select dlt.* from ".DB_PREFIX."ips_transfer as t
			LEFT JOIN ".DB_PREFIX."deal as d on d.id = t.deal_id
			LEFT JOIN ".DB_PREFIX."deal_load_transfer as dlt on dlt.id = t.ref_data
			LEFT JOIN ".DB_PREFIX."user as u on u.id = d.user_id
			left join ".DB_PREFIX."user tu on tu.id = dlt.t_user_id
			where t.pTransferType = 4 and t.id = ".intval($_REQUEST['id']);
					
			$load_info = $GLOBALS['db']->getRow($p_sql);
			
			if(!$load_info)
			{
				$this->error (l("INVALID_OPERATION"),$ajax);
			}
			
			$condition = " and ((dlr.user_id = ".$GLOBALS["user_info"]["id"]." and dlr.t_user_id = 0 ) or dlr.t_user_id =".$GLOBALS["user_info"]["id"].")";

			$sql = "select dlr.*,d.name as deal_name,u.user_name,tu.user_name as t_user_name from ".DB_PREFIX."deal_load_repay as dlr LEFT JOIN ".DB_PREFIX."user as u on u.id = dlr.user_id left join ".DB_PREFIX."user as tu on tu.id = dlr.t_user_id left join ".DB_PREFIX."deal as d on dlr.deal_id = d.id where dlr.load_id =".intval($load_info['load_id']) . " and dlr.user_id =".intval($load_info['user_id']) . " and dlr.deal_id = ".$load_info["deal_id"];
			
		    
			$count_sql = "select count(*) from ".DB_PREFIX."deal_load_repay as dlr LEFT JOIN ".DB_PREFIX."user as u on u.id = dlr.user_id 
			left join ".DB_PREFIX."user as tu on tu.id = dlr.t_user_id 
			left join ".DB_PREFIX."deal as d on dlr.deal_id = d.id where dlr.load_id =".intval($load_info['load_id']) . " and dlr.user_id =".intval($load_info['user_id']) . " and dlr.deal_id = ".$load_info["deal_id"];
	
			
			$list = $GLOBALS['db']->getAll( $sql.$condition.$limit);
			//取得满足条件的记录数
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
			
		}
		//易宝
		elseif(strtolower($className) == "yeepay")
		{
			$p_sql = "select dlt.* from ".DB_PREFIX."yeepay_cp_transaction as t
			LEFT JOIN ".DB_PREFIX."deal as d on d.id = t.tenderOrderNo
			LEFT JOIN ".DB_PREFIX."deal_load_transfer as dlt on dlt.id = t.transfer_id
			LEFT JOIN ".DB_PREFIX."user as u on u.id = d.user_id
			left join ".DB_PREFIX."user tu on tu.id = dlt.t_user_id
			where t.bizType = 'CREDIT_ASSIGNMENT'  and t.id = ".intval($_REQUEST['id']);

			$load_info = $GLOBALS['db']->getRow($p_sql);
			
			if(!$load_info)
			{
				$this->error (l("INVALID_OPERATION"),$ajax);
			}
			
			$condition = " and ((dlr.user_id = ".$GLOBALS["user_info"]["id"]." and dlr.t_user_id = 0 ) or dlr.t_user_id =".$GLOBALS["user_info"]["id"].")";
			
			$sql = "select dlr.*,d.name as deal_name,u.user_name,tu.user_name as t_user_name 
			from ".DB_PREFIX."deal_load_repay as dlr 
			LEFT JOIN ".DB_PREFIX."user as u on u.id = dlr.user_id 
			left join ".DB_PREFIX."user as tu on tu.id = dlr.t_user_id 
			left join ".DB_PREFIX."deal as d on dlr.deal_id = d.id 
			where dlr.load_id =".intval($load_info['load_id']) . " and dlr.user_id =".intval($load_info['user_id']) . " 
			and dlr.deal_id = ".$load_info["deal_id"];
			//print_r($sql);die;
			$count_sql = "select count(*) from ".DB_PREFIX."deal_load_repay as dlr 
			LEFT JOIN ".DB_PREFIX."user as u on u.id = dlr.user_id 
			left join ".DB_PREFIX."user as tu on tu.id = dlr.t_user_id 
			left join ".DB_PREFIX."deal as d on dlr.deal_id = d.id 
			where dlr.load_id =".intval($load_info['load_id']) . " and dlr.user_id =".intval($load_info['user_id']) . " 
			and dlr.deal_id = ".$load_info["deal_id"];
			

			$list = $GLOBALS['db']->getAll( $sql.$condition.$limit);
			//取得满足条件的记录数
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
		}
		//宝付
		elseif(strtolower($className) == "baofoo")
		{
			$p_sql = "select dlt.* from ".DB_PREFIX."baofoo_acct_trans as t
			LEFT JOIN ".DB_PREFIX."deal_load_transfer as dlt on dlt.id = t.ref_id
			LEFT JOIN ".DB_PREFIX."deal as d on d.id = dlt.deal_id
			LEFT JOIN ".DB_PREFIX."user as u on u.id = d.user_id
			left join ".DB_PREFIX."user tu on tu.id = dlt.t_user_id
			where t.code = 'CSD000'  and t.ref_type = 1 and t.id = ".intval($_REQUEST['id']);

			$load_info = $GLOBALS['db']->getRow($p_sql);
			
			if(!$load_info)
			{
				$this->error (l("INVALID_OPERATION"),$ajax);
			}
			
			$condition = " and ((dlr.user_id = ".$GLOBALS["user_info"]["id"]." and dlr.t_user_id = 0 ) or dlr.t_user_id =".$GLOBALS["user_info"]["id"].")";
			
			$sql = "select dlr.*,d.name as deal_name,u.user_name,tu.user_name as t_user_name 
			from ".DB_PREFIX."deal_load_repay as dlr 
			LEFT JOIN ".DB_PREFIX."user as u on u.id = dlr.user_id 
			left join ".DB_PREFIX."user as tu on tu.id = dlr.t_user_id 
			left join ".DB_PREFIX."deal as d on dlr.deal_id = d.id 
			where dlr.load_id =".intval($load_info['load_id']) . " and dlr.user_id =".intval($load_info['user_id']) . " 
			and dlr.deal_id = ".$load_info["deal_id"];
			//print_r($sql);die;
			$count_sql = "select count(*) from ".DB_PREFIX."deal_load_repay as dlr 
			LEFT JOIN ".DB_PREFIX."user as u on u.id = dlr.user_id 
			left join ".DB_PREFIX."user as tu on tu.id = dlr.t_user_id 
			left join ".DB_PREFIX."deal as d on dlr.deal_id = d.id 
			where dlr.load_id =".intval($load_info['load_id']) . " and dlr.user_id =".intval($load_info['user_id']) . " 
			and dlr.deal_id = ".$load_info["deal_id"];

			$list = $GLOBALS['db']->getAll( $sql.$condition.$limit);
			//取得满足条件的记录数
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
			
		}
		foreach($list as $k => $v)
		{
			$list[$k]["ll_key"] = $v["l_key"]+1;
		}
		$page = new Page($list_count,app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		$GLOBALS['tmpl']->assign('list',$list);
		$GLOBALS['tmpl']->assign("start_time",$start_time);
		$GLOBALS['tmpl']->assign("end_time",$end_time);
		
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_ips_ips_transfer_view.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
}
?>