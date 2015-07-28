<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------

class IpsRelationAction extends CommonAction{
	public function repayment()
	{
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
			$condition = " where ips.pErrCode = 'MG00000F' ";
		
			$sql = "select ips.*,ips.id as mid,d.`name` as deal_name,u.user_name, dr.*,(pOutAmt+pOutFee) as total_money from ".DB_PREFIX."ips_repayment_new_trade as ips
			left join ".DB_PREFIX."deal d on d.id = ips.deal_id
			left join ".DB_PREFIX."deal_repay dr on dr.id = ips.deal_repay_id 
			left join ".DB_PREFIX."user u on u.id = d.user_id ";
			
			$count_sql = "select count(*) from ".DB_PREFIX."ips_repayment_new_trade as ips
			left join ".DB_PREFIX."deal d on d.id = ips.deal_id
			left join ".DB_PREFIX."deal_repay dr on dr.id = ips.deal_repay_id 
			left join ".DB_PREFIX."user u on u.id = d.user_id ";
			
			if(strim($_REQUEST['pBidNo'])!='')
			{		
				$condition .= " and d.id like '%".strim($_REQUEST['pBidNo'])."%'";
			}
			if(strim($_REQUEST['deal_name'])!='')
			{		
				$condition .= " and d.name like '%".strim($_REQUEST['deal_name'])."%'";
			}
			if(strim($_REQUEST['user_name'])!='')
			{		
				$condition .= " and u.user_name like '%".strim($_REQUEST['user_name'])."%'";
			}
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
			$condition = " where yct.bizType = 'REPAYMENT' and yct.is_complete_transaction = 1 and yct.code ='1' and yct.is_callback = 1 ";
			
			$sql = "select yct.tenderOrderNo as pBidNo, yct.*,yct.id as mid,d.`name` as deal_name, 1 as pRepayType,(paymentAmount + fee) as total_money,
			u.user_name, dr.* , FROM_UNIXTIME(yct.create_time ,'%Y-%m-%d')as pIpsDate,FROM_UNIXTIME(yct.create_time ,'%Y-%m-%d')as pRepaymentDate
			from ".DB_PREFIX."yeepay_cp_transaction as yct
			left join ".DB_PREFIX."deal d on d.id = yct.tenderOrderNo
			left join ".DB_PREFIX."deal_repay dr on dr.id = yct.deal_repay_id 
			left join ".DB_PREFIX."user u on u.id = d.user_id ";
			
			$count_sql = "select count(*) from ".DB_PREFIX."yeepay_cp_transaction as yct
			left join ".DB_PREFIX."deal d on d.id = yct.tenderOrderNo
			left join ".DB_PREFIX."deal_repay dr on dr.id = yct.deal_repay_id 
			left join ".DB_PREFIX."user u on u.id = d.user_id ";
			
			if(strim($_REQUEST['pBidNo'])!='')
			{		
				$condition .= " and d.id like '%".strim($_REQUEST['pBidNo'])."%'";
			}
			if(strim($_REQUEST['deal_name'])!='')
			{		
				$condition .= " and d.name like '%".strim($_REQUEST['deal_name'])."%'";
			}
			if(strim($_REQUEST['user_name'])!='')
			{		
				$condition .= " and u.user_name like '%".strim($_REQUEST['user_name'])."%'";
			}
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
			$condition = " where yct.action_type = 4 and yct.code ='CSD000' and yct.is_callback = 1 ";
			
			$sql = "select yct.cus_id as pBidNo, yct.*,yct.id as mid,d.`name` as deal_name, 1 as pRepayType,(amount + fee) as total_money,
			u.user_name, dr.* , FROM_UNIXTIME(yct.req_time/1000 ,'%Y-%m-%d')as pIpsDate,FROM_UNIXTIME(yct.req_time/1000 ,'%Y-%m-%d')as pRepaymentDate
			from ".DB_PREFIX."baofoo_business as yct
			left join ".DB_PREFIX."deal d on d.id = yct.cus_id
			left join ".DB_PREFIX."deal_repay dr on dr.id = yct.deal_repay_id 
			left join ".DB_PREFIX."user u on u.id = d.user_id ";
			
			$count_sql = "select count(*) from ".DB_PREFIX."baofoo_business as yct
			left join ".DB_PREFIX."deal d on d.id = yct.cus_id
			left join ".DB_PREFIX."deal_repay dr on dr.id = yct.deal_repay_id 
			left join ".DB_PREFIX."user u on u.id = d.user_id ";
			
			if(strim($_REQUEST['pBidNo'])!='')
			{		
				$condition .= " and d.id like '%".strim($_REQUEST['pBidNo'])."%'";
			}
			if(strim($_REQUEST['deal_name'])!='')
			{		
				$condition .= " and d.name like '%".strim($_REQUEST['deal_name'])."%'";
			}
			if(strim($_REQUEST['user_name'])!='')
			{		
				$condition .= " and u.user_name like '%".strim($_REQUEST['user_name'])."%'";
			}			
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
		$this->assign('page',$p);
		
		$this->assign('list',$list);
		$this->assign("start_time",$start_time);
		$this->assign("end_time",$end_time);
		
		$this->display ();
		
	}
	public function repayment_export_csv($page = 1)
	{
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
		
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

		if($limit)
		{
			$limit = " limit ".$limit;
		}
		
		$className = getCollName();
		//环迅
		if(strtolower($className) == "ips")
		{
			$condition = " where ips.pErrCode = 'MG00000F' ";
		
			$sql = "select ips.*,ips.id as mid,d.`name` as deal_name,u.user_name, dr.*,(pOutAmt+pOutFee) as total_money from ".DB_PREFIX."ips_repayment_new_trade as ips
			left join ".DB_PREFIX."deal d on d.id = ips.deal_id
			left join ".DB_PREFIX."deal_repay dr on dr.id = ips.deal_repay_id 
			left join ".DB_PREFIX."user u on u.id = d.user_id ";
			
			$count_sql = "select count(*) from ".DB_PREFIX."ips_repayment_new_trade as ips
			left join ".DB_PREFIX."deal d on d.id = ips.deal_id
			left join ".DB_PREFIX."deal_repay dr on dr.id = ips.deal_repay_id 
			left join ".DB_PREFIX."user u on u.id = d.user_id ";
			
			if(strim($_REQUEST['pBidNo'])!='')
			{		
				$condition .= " and d.id like '%".strim($_REQUEST['pBidNo'])."%'";
			}
			if(strim($_REQUEST['deal_name'])!='')
			{		
				$condition .= " and d.name like '%".strim($_REQUEST['deal_name'])."%'";
			}
			if(strim($_REQUEST['user_name'])!='')
			{		
				$condition .= " and u.user_name like '%".strim($_REQUEST['user_name'])."%'";
			}
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
			$condition = " where yct.bizType = 'REPAYMENT' and yct.is_complete_transaction = 1 and yct.code ='1' and yct.is_callback = 1 ";
			
			$sql = "select yct.tenderOrderNo as pBidNo, yct.*,yct.id as mid,d.`name` as deal_name, 1 as pRepayType,(paymentAmount + fee) as total_money,
			u.user_name, dr.* , FROM_UNIXTIME(yct.create_time ,'%Y-%m-%d')as pIpsDate,FROM_UNIXTIME(yct.repay_start_time ,'%Y-%m-%d')as pRepaymentDate
			from ".DB_PREFIX."yeepay_cp_transaction as yct
			left join ".DB_PREFIX."deal d on d.id = yct.tenderOrderNo
			left join ".DB_PREFIX."deal_repay dr on dr.id = yct.deal_repay_id 
			left join ".DB_PREFIX."user u on u.id = d.user_id ";
			
			$count_sql = "select count(*) from ".DB_PREFIX."yeepay_cp_transaction as yct
			left join ".DB_PREFIX."deal d on d.id = yct.tenderOrderNo
			left join ".DB_PREFIX."deal_repay dr on dr.id = yct.deal_repay_id 
			left join ".DB_PREFIX."user u on u.id = d.user_id ";
			
			if(strim($_REQUEST['pBidNo'])!='')
			{		
				$condition .= " and d.id like '%".strim($_REQUEST['pBidNo'])."%'";
			}
			if(strim($_REQUEST['deal_name'])!='')
			{		
				$condition .= " and d.name like '%".strim($_REQUEST['deal_name'])."%'";
			}
			if(strim($_REQUEST['user_name'])!='')
			{		
				$condition .= " and u.user_name like '%".strim($_REQUEST['user_name'])."%'";
			}
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
			$condition = " where yct.action_type = 4 and yct.code ='CSD000' and yct.is_callback = 1 ";
			
			$sql = "select yct.cus_id as pBidNo, yct.*,yct.id as mid,d.`name` as deal_name, 1 as pRepayType,(amount + fee) as total_money,
			u.user_name, dr.* , FROM_UNIXTIME(yct.req_time/1000 ,'%Y-%m-%d')as pIpsDate,FROM_UNIXTIME(yct.repay_start_time ,'%Y-%m-%d')as pRepaymentDate
			from ".DB_PREFIX."baofoo_business as yct
			left join ".DB_PREFIX."deal d on d.id = yct.cus_id
			left join ".DB_PREFIX."deal_repay dr on dr.id = yct.deal_repay_id 
			left join ".DB_PREFIX."user u on u.id = d.user_id ";
			
			$count_sql = "select count(*) from ".DB_PREFIX."baofoo_business as yct
			left join ".DB_PREFIX."deal d on d.id = yct.cus_id
			left join ".DB_PREFIX."deal_repay dr on dr.id = yct.deal_repay_id 
			left join ".DB_PREFIX."user u on u.id = d.user_id ";
			
			if(strim($_REQUEST['pBidNo'])!='')
			{		
				$condition .= " and d.id like '%".strim($_REQUEST['pBidNo'])."%'";
			}
			if(strim($_REQUEST['deal_name'])!='')
			{		
				$condition .= " and d.name like '%".strim($_REQUEST['deal_name'])."%'";
			}
			if(strim($_REQUEST['user_name'])!='')
			{		
				$condition .= " and u.user_name like '%".strim($_REQUEST['user_name'])."%'";
			}			
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

		/*foreach($list as $k => $v)
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
		}*/
		
		if($list)
		{
			register_shutdown_function(array(&$this, 'repayment_export_csv'), $page+1);
			
			$list_value_old = array(
				'mid'=>'""', 
				'pBidNo'=>'""', 
				'deal_name'=>'""', 
				'user_name'=>'""',
				'repay_money'=>'""', 
				'impose_money'=>'""',
				'true_manage_money'=>'""', 
				'mange_impose_money'=>'""',
				'total_money'=>'""',
				'pRepaymentDate' => '""',
				'pIpsDate' => '""'			
			);
	    	if($page == 1)
	    	{
		    	$content = iconv("utf-8","gbk","编号,标号,贷款名称,还款人,还款本金,罚息/违约金,管理费,逾期管理费,还款总额,还款日期,第三方受理时间");	    		    	
		    	$content = $content . "\n";
	    	}

			
			foreach($list as $k=> $v)
			{
				$list_value = $list_value_old;
				$list_value["mid"] = '"' . iconv('utf-8','gbk', $v['mid']) . '"';
				$list_value["pBidNo"] = '"' . iconv('utf-8','gbk', $v['pBidNo']) . '"';
				$list_value["deal_name"] = '"' . iconv('utf-8','gbk',  $v["deal_name"]). '"';
				$list_value["user_name"] =  '"' . iconv('utf-8','gbk',  $v["user_name"]). '"';
				$list_value["repay_money"] =  '"' . iconv('utf-8','gbk', number_format($v["repay_money"],2)). '"';
				$list_value["impose_money"] =  '"' . iconv('utf-8','gbk', number_format($v["impose_money"],2)). '"';
				$list_value["true_manage_money"] =  '"' . iconv('utf-8','gbk', number_format($v["true_manage_money"],2)). '"';
				$list_value["mange_impose_money"] =  '"' . iconv('utf-8','gbk', number_format($v["mange_impose_money"],2)). '"';
				$list_value["total_money"] =  '"' . iconv('utf-8','gbk', number_format($v["total_money"],2)). '"';
				$list_value["pRepaymentDate"] =  '"' . iconv('utf-8','gbk', $v["pRepaymentDate"]). '"';
				$list_value["pIpsDate"] =  '"' . iconv('utf-8','gbk', $v["pIpsDate"]). '"';
				
				$content .= implode(",", $list_value) . "\n";
			}	
			
			
			header("Content-Disposition: attachment; filename=order_list.csv");
	    	echo $content;  
		}
		else
		{
			if($page==1)
			$this->error(L("NO_RESULT"));
		}	
		
	}

	public function deal_list()
	{
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
			
			$sql = "select d.*,r.*,d.id as mid ,u.user_name,dl.name as deal_name,tu.user_name as t_user_name ,r.deal_id as pBidNo
			,pInAmt+pInFee as total_repay
			from ".DB_PREFIX."ips_repayment_new_trade_detail as d
			left JOIN ".DB_PREFIX."deal_load_repay r on r.id = d.deal_load_repay_id
			left join ".DB_PREFIX."user u on u.id = r.user_id
			left join ".DB_PREFIX."user tu on tu.id = r.t_user_id left join ".DB_PREFIX."deal as dl on r.deal_id = dl.id";
			
			$count_sql = "select count(*) 
			from ".DB_PREFIX."ips_repayment_new_trade_detail as d
			left JOIN ".DB_PREFIX."deal_load_repay r on r.id = d.deal_load_repay_id
			left join ".DB_PREFIX."user u on u.id = r.user_id
			left join ".DB_PREFIX."user tu on tu.id = r.t_user_id left join ".DB_PREFIX."deal as dl on r.deal_id = dl.id";
			
			$list = $GLOBALS['db']->getAll( $sql.$condition." order by d.id desc ".$limit);
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
		}
		//易宝
		elseif(strtolower($className) == "yeepay")
		{
			$condition = " where 1=1";
			
			$condition .= " and d.pid = ".intval(strim($_REQUEST['id']));
			
			$GLOBALS['tmpl']->assign('id', intval(strim($_REQUEST['id'])));
			
			$sql = "select d.*,r.*,d.id as mid ,u.user_name,dl.name as deal_name,tu.user_name as t_user_name ,(amount + fee) as total_repay,r.deal_id as pBidNo
			from ".DB_PREFIX."yeepay_cp_transaction_detail as d
			left JOIN ".DB_PREFIX."deal_load_repay r on r.id = d.deal_load_repay_id
			left join ".DB_PREFIX."user u on u.id = r.user_id
			left join ".DB_PREFIX."user tu on tu.id = r.t_user_id left join ".DB_PREFIX."deal as dl on r.deal_id = dl.id";
			
			$count_sql = "select count(*) 
			from ".DB_PREFIX."yeepay_cp_transaction_detail as d
			left JOIN ".DB_PREFIX."deal_load_repay r on r.id = d.deal_load_repay_id
			left join ".DB_PREFIX."user u on u.id = r.user_id
			left join ".DB_PREFIX."user tu on tu.id = r.t_user_id left join ".DB_PREFIX."deal as dl on r.deal_id = dl.id";
	
			$list = $GLOBALS['db']->getAll( $sql.$condition." order by d.id desc ".$limit);

			//取得满足条件的记录数
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
		}
		//宝付
		elseif(strtolower($className) == "baofoo")
		{
			$condition = " where 1=1";
			
			$condition .= " and d.pid = ".intval(strim($_REQUEST['id']));
			
			$GLOBALS['tmpl']->assign('id', intval(strim($_REQUEST['id'])));
			
			$sql = "select d.*,r.*,d.id as mid ,u.user_name,dl.name as deal_name,tu.user_name as t_user_name ,(amount + fee) as total_money,r.deal_id as pBidNo
			from ".DB_PREFIX."baofoo_business_detail as d
			left JOIN ".DB_PREFIX."deal_load_repay r on r.id = d.deal_load_repay_id
			left join ".DB_PREFIX."user u on u.id = r.user_id
			left join ".DB_PREFIX."user tu on tu.id = r.t_user_id left join ".DB_PREFIX."deal as dl on r.deal_id = dl.id";
			
			$count_sql = "select count(*) 
			from ".DB_PREFIX."baofoo_business_detail as d
			left JOIN ".DB_PREFIX."deal_load_repay r on r.id = d.deal_load_repay_id
			left join ".DB_PREFIX."user u on u.id = r.user_id
			left join ".DB_PREFIX."user tu on tu.id = r.t_user_id left join ".DB_PREFIX."deal as dl on r.deal_id = dl.id";
			
			$list = $GLOBALS['db']->getAll( $sql.$condition." order by d.id desc ".$limit);
			
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
		$this->assign('pages',$p);
		
		$this->assign('list',$list);
		
		$this->display ();
	}
	public function deal_export_csv($page = 1)
	{
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
		
		if(isset($_REQUEST['id'])&&intval(strim($_REQUEST['id']))>0)
		{		
			
		}
		else
		{
			return;
			//$this->error (l("INVALID_OPERATION"),$ajax);
		}
		
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
			
			$sql = "select d.*,r.*,d.id as mid ,u.user_name,dl.name as deal_name,tu.user_name as t_user_name ,r.deal_id as pBidNo
			,pInAmt+pInFee as total_repay
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
			
			$sql = "select d.*,r.*,d.id as mid ,u.user_name,dl.name as deal_name,tu.user_name as t_user_name ,(amount + fee) as total_repay,r.deal_id as pBidNo
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
			
			$sql = "select d.*,r.*,d.id as mid ,u.user_name,dl.name as deal_name,tu.user_name as t_user_name ,(amount + fee) as total_money,r.deal_id as pBidNo
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
		
		if($list)
		{
			register_shutdown_function(array(&$this, 'deal_export_csv'), $page+1);

			$list_value = array(
			'mid'=>'""', 
			'pBidNo'=>'""', 
			'deal_name'=>'""',
			'user_name'=>'""', 
			't_user_name'=>'""', 
			'self_money'=>'""',
			'interest_money'=>'""',
			'manage_money'=>'""',
			'impose_money'=>'""', 
			'total_repay'=>'""', 
			'status'=>'""', 
			'repay_time'=>'""',
			'true_repay_time'=>'""',  
			);
	    	if($page == 1)
	    	{
		    	$content = iconv("utf-8","gbk","编号,标号,贷款名称,投标人,承接着,还款本金,利息,管理费,罚息/违约金,换困总额,还款状态,还款日,实际还款时间");	    		    	
		    	$content = $content . "\n";
	    	}
			foreach($list as $k=> $v)
			{
				$list_value["mid"] =  '"' . iconv('utf-8','gbk', $v["mid"]). '"';
				$list_value["pBidNo"] =  '"' . iconv('utf-8','gbk', $v["pBidNo"]). '"';
				$list_value["deal_name"] =  '"' . iconv('utf-8','gbk', $v["deal_name"]). '"';
				$list_value["user_name"] = '"' . iconv('utf-8','gbk', $v["user_name"]) . '"';
				$list_value["t_user_name"] = '"' . iconv('utf-8','gbk', $v["t_user_name"]) . '"';
				$list_value["self_money"] = '"' . iconv('utf-8','gbk', $v["self_money"]) . '"';
				$list_value["interest_money"] = '"' . iconv('utf-8','gbk', $v["interest_money"]) . '"';
				$list_value["manage_money"] = '"' . iconv('utf-8','gbk', $v["manage_money"]) . '"';
				$list_value["impose_money"] =  '"' . iconv('utf-8','gbk', $v["impose_money"]).'"';
				$list_value["total_repay"] =  '"' . iconv('utf-8','gbk', $v["total_repay"]). '"';
				$list_value["status"] =  '"' . iconv('utf-8','gbk',  $v["status"]). '"';
				$list_value["repay_time"] =  '"' . iconv('utf-8','gbk',  $v["repay_time"]). '"';
				$list_value["true_repay_time"] =  '"' . iconv('utf-8','gbk',  $v["true_repay_time"]). '"';
				$content .= implode(",", $list_value) . "\n";
			}	
			
			
			header("Content-Disposition: attachment; filename=order_list.csv");
	    	echo $content;  
		}
		else
		{
			if($page==1)
			$this->error(L("NO_RESULT"));
		}	
		
	}
	public function back_repayment()
	{
		$this->assign("page_title","回款单");

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
			$condition = " where pStatus = 'Y'";
			
			if(strim($_REQUEST['pBidNo'])!='')
			{		
				$condition .= " and dl.id like '%".strim($_REQUEST['pBidNo'])."%'";
			}
			if(strim($_REQUEST['deal_name'])!='')
			{		
				$condition .= " and dl.name like '%".strim($_REQUEST['deal_name'])."%'";
			}
			if(strim($_REQUEST['user_name'])!='')
			{		
				$condition .= " and u.user_name like '%".strim($_REQUEST['user_name'])."%'";
			}		
			if(strim($start_time)!="")
			{
				$condition .= " and r.repay_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$condition .= " and r.repay_time <=".  to_timespan(strim($end_time));
			}

			
			$sql = "select d.*,r.*,deal_t.pBidNo,d.id as mid ,u.user_name,dl.name as deal_name from ".DB_PREFIX."ips_repayment_new_trade_detail as d
			left JOIN ".DB_PREFIX."deal_load_repay r on r.id = d.deal_load_repay_id
			left JOIN ".DB_PREFIX."deal dl on dl.id = r.deal_id
			left join ".DB_PREFIX."user u on u.id = r.user_id
			left JOIN ".DB_PREFIX."ips_repayment_new_trade deal_t on deal_t.id = d.pid";
			
			$count_sql = "select count(*) from ".DB_PREFIX."ips_repayment_new_trade_detail as d
			left JOIN ".DB_PREFIX."deal_load_repay r on r.id = d.deal_load_repay_id
			left JOIN ".DB_PREFIX."deal dl on dl.id = r.deal_id
			left join ".DB_PREFIX."user u on u.id = r.user_id";
	

			$list = $GLOBALS['db']->getAll( $sql.$condition." order by d.id desc".$limit);

			//取得满足条件的记录数
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
			
		}
		//易宝
		elseif(strtolower($className) == "yeepay")
		{
			$condition = " where deal_t.bizType = 'REPAYMENT' and deal_t.is_complete_transaction = 1 and deal_t.code ='1' and deal_t.is_callback = 1 ";
			
			if(strim($_REQUEST['pBidNo'])!='')
			{		
				$condition .= " and dl.id like '%".strim($_REQUEST['pBidNo'])."%'";
			}
			if(strim($_REQUEST['deal_name'])!='')
			{		
				$condition .= " and dl.name like '%".strim($_REQUEST['deal_name'])."%'";
			}
			if(strim($_REQUEST['user_name'])!='')
			{		
				$condition .= " and u.user_name like '%".strim($_REQUEST['user_name'])."%'";
			}		
			if(strim($start_time)!="")
			{
				$condition .= " and r.repay_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$condition .= " and r.repay_time <=".  to_timespan(strim($end_time));
			}
			
			$sql = "select d.*,r.*,deal_t.tenderOrderNo as pBidNo,d.id as mid ,u.user_name,dl.name as deal_name 
			from ".DB_PREFIX."yeepay_cp_transaction_detail as d
			left JOIN ".DB_PREFIX."deal_load_repay r on r.id = d.deal_load_repay_id
			left JOIN ".DB_PREFIX."deal dl on dl.id = r.deal_id
			left join ".DB_PREFIX."user u on u.id = r.user_id
			left JOIN ".DB_PREFIX."yeepay_cp_transaction deal_t on deal_t.id = d.pid";
			
			$count_sql = "select count(*) 
			from ".DB_PREFIX."yeepay_cp_transaction_detail as d
			left JOIN ".DB_PREFIX."deal_load_repay r on r.id = d.deal_load_repay_id
			left JOIN ".DB_PREFIX."deal dl on dl.id = r.deal_id
			left join ".DB_PREFIX."user u on u.id = r.user_id
			left JOIN ".DB_PREFIX."yeepay_cp_transaction deal_t on deal_t.id = d.pid";
	
			$list = $GLOBALS['db']->getAll( $sql.$condition." order by d.id desc".$limit);
			
			//取得满足条件的记录数
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
		}
		//宝付
		elseif(strtolower($className) == "baofoo")
		{
			$condition = " where 1=1 ";
		
			if(strim($_REQUEST['pBidNo'])!='')
			{		
				$condition .= " and dl.id like '%".strim($_REQUEST['pBidNo'])."%'";
			}
			if(strim($_REQUEST['deal_name'])!='')
			{		
				$condition .= " and dl.name like '%".strim($_REQUEST['deal_name'])."%'";
			}
			if(strim($_REQUEST['user_name'])!='')
			{		
				$condition .= " and u.user_name like '%".strim($_REQUEST['user_name'])."%'";
			}		
			if(strim($start_time)!="")
			{
				$condition .= " and r.repay_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$condition .= " and r.repay_time <=".  to_timespan(strim($end_time));
			}
		
			$sql = "select d.*,r.*,deal_t.cus_id as pBidNo,d.id as mid ,u.user_name,dl.name as deal_name 
			from ".DB_PREFIX."baofoo_business_detail as d
			left JOIN ".DB_PREFIX."deal_load_repay r on r.id = d.deal_load_repay_id
			left JOIN ".DB_PREFIX."deal dl on dl.id = r.deal_id
			left join ".DB_PREFIX."user u on u.id = r.user_id
			left JOIN ".DB_PREFIX."baofoo_business deal_t on deal_t.id = d.pid";
			
			$count_sql = "select count(*) 
			from ".DB_PREFIX."baofoo_business_detail as d
			left JOIN ".DB_PREFIX."deal_load_repay r on r.id = d.deal_load_repay_id
			left JOIN ".DB_PREFIX."deal dl on dl.id = r.deal_id
			left join ".DB_PREFIX."user u on u.id = r.user_id";
			
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
		$this->assign('page',$p);
		
		$this->assign('list',$list);
		$this->assign("start_time",$start_time);
		$this->assign("end_time",$end_time);
		
		$this->display ();
		
	}
	public function back_export_csv($page = 1)
	{
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
		
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
			$condition = " where pStatus = 'Y'";
			
			if(strim($_REQUEST['pBidNo'])!='')
			{		
				$condition .= " and dl.id like '%".strim($_REQUEST['pBidNo'])."%'";
			}
			if(strim($_REQUEST['deal_name'])!='')
			{		
				$condition .= " and dl.name like '%".strim($_REQUEST['deal_name'])."%'";
			}
			if(strim($_REQUEST['user_name'])!='')
			{		
				$condition .= " and u.user_name like '%".strim($_REQUEST['user_name'])."%'";
			}		
			if(strim($start_time)!="")
			{
				$condition .= " and r.repay_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$condition .= " and r.repay_time <=".  to_timespan(strim($end_time));
			}

			
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
			$condition = " where deal_t.bizType = 'REPAYMENT' and deal_t.is_complete_transaction = 1 and deal_t.code ='1' and deal_t.is_callback = 1 ";
			
			if(strim($_REQUEST['pBidNo'])!='')
			{		
				$condition .= " and dl.id like '%".strim($_REQUEST['pBidNo'])."%'";
			}
			if(strim($_REQUEST['deal_name'])!='')
			{		
				$condition .= " and dl.name like '%".strim($_REQUEST['deal_name'])."%'";
			}
			if(strim($_REQUEST['user_name'])!='')
			{		
				$condition .= " and u.user_name like '%".strim($_REQUEST['user_name'])."%'";
			}		
			if(strim($start_time)!="")
			{
				$condition .= " and r.repay_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$condition .= " and r.repay_time <=".  to_timespan(strim($end_time));
			}
			
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
			$condition = " where 1=1 ";
		
			if(strim($_REQUEST['pBidNo'])!='')
			{		
				$condition .= " and dl.id like '%".strim($_REQUEST['pBidNo'])."%'";
			}
			if(strim($_REQUEST['deal_name'])!='')
			{		
				$condition .= " and dl.name like '%".strim($_REQUEST['deal_name'])."%'";
			}
			if(strim($_REQUEST['user_name'])!='')
			{		
				$condition .= " and u.user_name like '%".strim($_REQUEST['user_name'])."%'";
			}		
			if(strim($start_time)!="")
			{
				$condition .= " and r.repay_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$condition .= " and r.repay_time <=".  to_timespan(strim($end_time));
			}
		
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
		
		if($list)
		{
			register_shutdown_function(array(&$this, 'deal_export_csv'), $page+1);
			
			$list_value = array(
			'mid'=>'""', 
			'pBidNo'=>'""', 
			'deal_name'=>'""',
			'user_name'=>'""', 
			'repay_time'=>'""', 
			'true_repay_time'=>'""',
			'repay_money'=>'""',
			'true_manage_money'=>'""',
			'true_manage_interest_money'=>'""', 
			'impose_money'=>'""', 
			'status'=>'""'
			);
	    	if($page == 1)
	    	{
		    	$content = iconv("utf-8","gbk","编号,标号,贷款名称,投标人,还款日,实际还款日,已收本息,管理费,利息管理费,罚息/违约金,状态");	    		    	
		    	$content = $content . "\n";
	    	}
			foreach($list as $k=> $v)
			{
				$list_value["mid"] =  '"' . iconv('utf-8','gbk', $v["mid"]). '"';
				$list_value["pBidNo"] =  '"' . iconv('utf-8','gbk', $v["pBidNo"]). '"';
				$list_value["deal_name"] =  '"' . iconv('utf-8','gbk', $v["deal_name"]). '"';
				$list_value["user_name"] = '"' . iconv('utf-8','gbk', $v["user_name"]) . '"';
				$list_value["repay_time"] = '"' . iconv('utf-8','gbk', to_date($v["repay_time"],"Y-m-d")). '"';
				$list_value["true_repay_time"] = '"' . iconv('utf-8','gbk', to_date($v["true_repay_time"],"Y-m-d")) . '"';
				$list_value["repay_money"] = '"' . iconv('utf-8','gbk', $v["repay_money"]) . '"';
				$list_value["true_manage_money"] = '"' . iconv('utf-8','gbk', $v["true_manage_money"]) . '"';
				$list_value["impose_money"] =  '"' . iconv('utf-8','gbk', $v["impose_money"]).'"';
				$list_value["true_manage_interest_money"] =  '"' . iconv('utf-8','gbk', $v["true_manage_interest_money"]). '"';
				$list_value["impose_money"] =  '"' . iconv('utf-8','gbk',  $v["impose_money"]). '"';
				$list_value["status"] =  '"' . iconv('utf-8','gbk',  $v["status"]). '"';
				$content .= implode(",", $list_value) . "\n";
			}	
			
			
			header("Content-Disposition: attachment; filename=order_list.csv");
	    	echo $content;  
		}
		else
		{
			if($page==1)
			$this->error(L("NO_RESULT"));
		}	
		
	}
}
?>