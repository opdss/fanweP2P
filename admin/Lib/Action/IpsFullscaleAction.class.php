<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------

class IpsFullscaleAction extends CommonAction{
	public function index()
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
			$condition = " and t.pErrCode = 'MG00000F' ";
			
			if(strim($_REQUEST['pBidNo'])!='')
			{		
				$condition .= " and pBidNo like '%".strim($_REQUEST['pBidNo'])."%'";
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
			$condition = " where  bizType = 'TENDER' and is_complete_transaction = 1 and t.code ='1' and is_callback = 1 ";
			
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
			//print_r($count);die;
			//$name=$this->getActionName();
		}
		//宝付
		elseif(strtolower($className) == "baofoo")
		{
			$condition = " where  action_type = '2' and t.code ='CSD000' and is_callback = 1 ";
			
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
		$this->assign('page',$p);
		
		$this->assign('list',$list);
		$this->assign("start_time",$start_time);
		$this->assign("end_time",$end_time);
		
		$this->display ();
		
	}
	public function export_csv($page = 1)
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
			$condition = " and t.pErrCode = 'MG00000F' ";
			
			if(strim($_REQUEST['pBidNo'])!='')
			{		
				$condition .= " and pBidNo like '%".strim($_REQUEST['pBidNo'])."%'";
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
			$condition = " where  bizType = 'TENDER' and is_complete_transaction = 1 and t.code ='1' and is_callback = 1 ";
			
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
			//print_r($count);die;
			//$name=$this->getActionName();
		}
		//宝付
		elseif(strtolower($className) == "baofoo")
		{
			$condition = " where  action_type = '2' and t.code ='CSD000' and is_callback = 1 ";
			
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
		
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv'), $page+1);
						
			$list_value_old = array(
				'id'=>'""', 
				'pBidNo'=>'""', 
				'name' => '""',
				'user_name'=>'""',
				'borrow_amount'=>'""', 
				'deal_fee'=>'""', 
				'loan_amount'=>'""',
				'pIpsTime'=>'""'
			);
			
	    	if($page == 1)
	    	{
		    	$content = iconv("utf-8","gbk","编号,标号,贷款名称,借款人,借款金额,成交服务费,放款金额,第三方处理时间");	    		    	
		    	$content = $content . "\n";
	    	}

			foreach($list as $k=> $v)
			{
				$list_value = $list_value_old;
				
				$list_value["id"] = '"' . iconv('utf-8','gbk', $v['id']) . '"';
				$list_value["name"] = '"' . iconv('utf-8','gbk', $v['name']) . '"';
				$list_value["pBidNo"] =  '"' . iconv('utf-8','gbk',  $v["pBidNo"]). '"';
				$list_value["user_name"] =  '"' . iconv('utf-8','gbk', $v["user_name"]). '"';
				$list_value["borrow_amount"] =  '"' . iconv('utf-8','gbk', $v["borrow_amount"]). '"';
				$list_value["deal_fee"] =  '"' . iconv('utf-8','gbk', $v["deal_fee"]). '"';
				$list_value["loan_amount"] =  '"' . iconv('utf-8','gbk', $v["loan_amount"]). '"';
				$list_value["pIpsTime"] =  '"' . iconv('utf-8','gbk', $v["pIpsTime"]). '"';
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

	public function relation_list()
	{
		if(isset($_REQUEST['id'])&&intval(strim($_REQUEST['id']))>0)
		{		
			
		}
		else
		{
			return;
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
			$condition = " where  1=1 ";
			$condition .= " and t.pid = ".intval(strim($_REQUEST['id']));
			$GLOBALS['tmpl']->assign('id',intval(strim($_REQUEST['id'])));
			$sql = "select t.*,t.id as mid,l.* from ".DB_PREFIX."ips_transfer_detail as t LEFT JOIN ".DB_PREFIX."ips_transfer it on t.pid = it.id
	LEFT JOIN ".DB_PREFIX."deal_load l on l.deal_id = it.deal_id and l.pMerBillNo = t.pOriMerBillNo ";
			
			
			$count_sql = "select count(*) from ".DB_PREFIX."ips_transfer_detail as t 
			LEFT JOIN ".DB_PREFIX."ips_transfer it on t.pid = it.id
			LEFT JOIN ".DB_PREFIX."deal_load l on l.deal_id = it.deal_id and l.pMerBillNo = t.pOriMerBillNo ";
	
			$list = $GLOBALS['db']->getAll( $sql.$condition." order by t.id desc ".$limit);
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
			
			$sql = "select t.*,t.id as mid,l.*,u.user_name, 'Y' as pStatus,FROM_UNIXTIME(t.update_time,'%Y-%m-%d') as pIpsDetailTime,
			requestNo as pIpsDetailBillNo,l.money as pTrdAmt,fee as pIpsFee
			 from ".DB_PREFIX."yeepay_cp_transaction as t
			LEFT JOIN ".DB_PREFIX."deal_load l on l.deal_id = t.tenderOrderNo and l.pMerBillNo = t.requestNo 
			left join ".DB_PREFIX."user u on u.id = t.platformUserNo ";
			
			
			$count_sql = "select count(*) from ".DB_PREFIX."yeepay_cp_transaction as t 
			LEFT JOIN ".DB_PREFIX."deal_load l on l.deal_id = t.tenderOrderNo and l.pMerBillNo = t.requestNo 
			left join ".DB_PREFIX."user u on u.id = t.platformUserNo ";

			$list = $GLOBALS['db']->getAll( $sql.$condition." order by t.id desc ".$limit);
			//取得满足条件的记录数
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);

		}
		//宝付
		elseif(strtolower($className) == "baofoo")
		{
			$condition = " where  action_type = '2' and t.code ='CSD000' and is_callback = 1 ";
			
			$condition .= " and t.cus_id = ".intval(strim($_REQUEST['id']));
			
			$GLOBALS['tmpl']->assign('id',intval(strim($_REQUEST['id'])));
			
			$sql = "select t.*,t.id as mid,l.*,u.user_name,FROM_UNIXTIME(req_time/1000,'%Y-%m-%d') as pIpsDetailTime,
			order_id as pIpsDetailBillNo,l.money as pTrdAmt,fee as pIpsFee,'Y' as pStatus
			 from ".DB_PREFIX."baofoo_business as t
			LEFT JOIN ".DB_PREFIX."deal_load l on l.deal_id = t.cus_id 
			left join ".DB_PREFIX."user u on u.id = l.user_id";
			
			$count_sql = "select count(*) from ".DB_PREFIX."baofoo_business as t 
			LEFT JOIN ".DB_PREFIX."deal_load l on l.deal_id = t.cus_id
			left join ".DB_PREFIX."user u on u.id = l.user_id ";


			$list = $GLOBALS['db']->getAll( $sql.$condition." order by t.id desc ".$limit);
			//取得满足条件的记录数
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
			//print_r($count);die;
			//$name=$this->getActionName();
		}
		
		$page = new Page($list_count,app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$this->assign('page',$p);
		
		$this->assign('list',$list);
		$this->assign("start_time",$start_time);
		$this->assign("end_time",$end_time);
		
		$this->display ();
	}
	public function relation_export_csv($page = 1)
	{
		
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
		
		if(isset($_REQUEST['id'])&&intval(strim($_REQUEST['id']))>0)
		{		
			
		}
		else
		{
			return;
		}
		
		if($limit)
		{
			$limit = " limit ".$limit;
		}
		
		$className = getCollName();
		
		//环迅
		if(strtolower($className) == "ips")
		{
			$condition = " where  1=1 ";
			$condition .= " and t.pid = ".intval(strim($_REQUEST['id']));
			$GLOBALS['tmpl']->assign('id',intval(strim($_REQUEST['id'])));
			$sql = "select t.*,t.id as mid,l.* from ".DB_PREFIX."ips_transfer_detail as t LEFT JOIN ".DB_PREFIX."ips_transfer it on t.pid = it.id
	LEFT JOIN ".DB_PREFIX."deal_load l on l.deal_id = it.deal_id ";
			
			
			$count_sql = "select count(*) from ".DB_PREFIX."ips_transfer_detail as t 
			LEFT JOIN ".DB_PREFIX."ips_transfer it on t.pid = it.id
			LEFT JOIN ".DB_PREFIX."deal_load l on l.deal_id = it.deal_id ";
	
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
			$condition .= " and t.id = ".intval(strim($_REQUEST['id']));
			
			$GLOBALS['tmpl']->assign('id',intval(strim($_REQUEST['id'])));
			
			$sql = "select t.*,t.id as mid,l.*,u.user_name, 'Y' as pStatus,FROM_UNIXTIME(t.update_time,'%Y-%m-%d') as pIpsDetailTime,
			requestNo as pIpsDetailBillNo,l.money as pTrdAmt,fee as pIpsFee
			 from ".DB_PREFIX."yeepay_cp_transaction as t
			LEFT JOIN ".DB_PREFIX."deal_load l on l.deal_id = t.tenderOrderNo and l.pMerBillNo = t.requestNo 
			left join ".DB_PREFIX."user u on u.id = t.platformUserNo ";
			
			
			$count_sql = "select count(*) from ".DB_PREFIX."yeepay_cp_transaction as t 
			LEFT JOIN ".DB_PREFIX."deal_load l on l.deal_id = t.tenderOrderNo and l.pMerBillNo = t.requestNo 
			left join ".DB_PREFIX."user u on u.id = t.platformUserNo ";

			$list = $GLOBALS['db']->getAll( $sql.$condition.$limit);
			//取得满足条件的记录数
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);

		}
		//宝付
		elseif(strtolower($className) == "baofoo")
		{
			$condition = " where  action_type = '2' and t.code ='CSD000' and is_callback = 1 ";
			
			$condition .= " and t.id = ".intval(strim($_REQUEST['id']));
			
			$GLOBALS['tmpl']->assign('id',intval(strim($_REQUEST['id'])));
			
			$sql = "select t.*,t.id as mid,l.*,u.user_name,FROM_UNIXTIME(req_time/1000,'%Y-%m-%d') as pIpsDetailTime,
			order_id as pIpsDetailBillNo,l.money as pTrdAmt,fee as pIpsFee,'Y' as pStatus
			 from ".DB_PREFIX."baofoo_business as t
			LEFT JOIN ".DB_PREFIX."deal_load l on l.deal_id = t.cus_id and l.pMerBillNo = t.order_id 
			left join ".DB_PREFIX."user u on u.id = t.load_user_id ";
			
			$count_sql = "select count(*) from ".DB_PREFIX."baofoo_business as t 
			LEFT JOIN ".DB_PREFIX."deal_load l on l.deal_id = t.cus_id and l.pMerBillNo = t.order_id 
			left join ".DB_PREFIX."user u on u.id = t.load_user_id ";
			

			$list = $GLOBALS['db']->getAll( $sql.$condition.$limit);
			//取得满足条件的记录数
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
			//print_r($count);die;
			//$name=$this->getActionName();
		}
			
		if($list)
		{
			register_shutdown_function(array(&$this, 'relation_export_csv'), $page+1);
			
			$list_value = array('id'=>'""', 'user_name'=>'""', 'pTrdAmt'=>'""','pStatus'=>'""','pIpsDetailTime'=>'""');
	    	if($page == 1)
	    	{
		    	$content = iconv("utf-8","gbk","编号,投资人,交易金额,转账状态,明细处理时间");	    		    	
		    	$content = $content . "\n";
	    	}

			foreach($list as $k=> $v)
			{
				$list_value["id"] =  '"' . iconv('utf-8','gbk', $v["mid"]). '"';
				$list_value["user_name"] =  '"' . iconv('utf-8','gbk', $v["user_name"]). '"';
				$list_value["pTrdAmt"] =  '"' . iconv('utf-8','gbk', $v["pTrdAmt"]). '"';
				if(strim($v["pStatus"]))
					$list_value["pStatus"] = '"' . iconv('utf-8','gbk',l("P_TRANSFER_STATUS_". $v["pStatus"])). '"';
				else
				{
					$list_value["pStatus"] ="";
				}
				$list_value["pIpsDetailTime"] = '"' . iconv('utf-8','gbk', $v["pIpsDetailTime"]) . '"';
				
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