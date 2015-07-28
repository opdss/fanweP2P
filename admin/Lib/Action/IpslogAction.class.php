<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------

class IpslogAction extends CommonAction{
	public function create()
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
			$where = " where pErrCode ='MG00000F' ";
			//姓名
			if(strim($_REQUEST['user_name'])!='')
			{		
				$where .= " and user_name like '%".strim($_REQUEST['user_name'])."%'";
			}
			//证件号
			if(strim($_REQUEST['ident_no'])!='')
			{		
				$where .= " and pIdentNo like '%".strim($_REQUEST['ident_no'])."%'";
			}
			//手机号
			if(strim($_REQUEST['mobile'])!='')
			{		
				$where .= " and pMobileNo like '%".strim($_REQUEST['mobile'])."%'";
			}
			
			if(strim($start_time)!="")
			{
				$where .= " and UNIX_TIMESTAMP(pSmDate) >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and UNIX_TIMESTAMP(pSmDate) <=".  to_timespan(strim($end_time));
			}

			$list = $GLOBALS['db']->getAll("select irs.*,u.user_name from ".DB_PREFIX."ips_create_new_acct irs left join ".DB_PREFIX."user u on irs.user_id = u.id ".$where ." order by irs.id desc ".$limit);	
			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."ips_create_new_acct irs left join ".DB_PREFIX."user u on irs.user_id = u.id ".$where);
			
			foreach($list as $k => $v)
			{
				$list[$k]["pStatus"] = l("IPS_STATUS_".$v["pStatus"]);
				$list[$k]["pIdentType"] = l("IPS_IDENT_TYPE_".$v["pIdentType"]);
				$list[$k]["user_type"] = l("IPS_TYPE_".$v["user_type"]);
			}
		}
		//易宝
		elseif(strtolower($className) == "yeepay")
		{
			$where = " where is_callback = 1 and  t.code ='1' ";
			//姓名
			if(strim($_REQUEST['user_name'])!='')
			{		
				$where .= " and u.user_name like '%".strim($_REQUEST['user_name'])."%'";
			}
			//证件号
			if(strim($_REQUEST['ident_no'])!='')
			{		
				$where .= " and t.idCardNo like '%".strim($_REQUEST['ident_no'])."%'";
			}
			//手机号
			if(strim($_REQUEST['mobile'])!='')
			{		
				$where .= " and t.mobile like '%".strim($_REQUEST['mobile'])."%'";
			}
			if(strim($start_time)!="")
			{
				$where .= " and t.create_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and t.create_time <=".  to_timespan(strim($end_time));
			}
			$list = $GLOBALS['db']->getAll("select t.*,u.user_name,u.user_type,t.idCardType as pIdentType,
			t.idCardNo as pIdentNo,t.realName as pRealName,t.mobile as pMobileNo,t.email as pEmail,t.create_time as pSmDate
			 from ".DB_PREFIX."yeepay_register t left join ".DB_PREFIX."user u on t.platformUserNo = u.id ".$where ." order by t.id desc ".$limit);

			foreach($list as $k => $v)
			{
				$list[$k]["pStatus"] = l("IPS_STATUS_10");
				
				if(strim($v["pIdentType"]) == "G1_IDCARD")
				{
					$list[$k]["pIdentType"] = "一代身份证";
				}
				else
				{
					$list[$k]["pIdentType"] = "一代身份证";
				}
				$list[$k]["pSmDate"] = to_date($v["pSmDate"],"Y-m-d");
				$list[$k]["user_type"] = l("IPS_TYPE_".$v["user_type"]);
			}
			
			$list_count = $GLOBALS['db']->getOne("select count(*)
			 from ".DB_PREFIX."yeepay_register t left join ".DB_PREFIX."user u on t.platformUserNo = u.id ".$where);
		}
		//宝付
		elseif(strtolower($className) == "baofoo")
		{
			$where = " where is_callback = 1 and  t.code ='CSD000' ";
			//姓名
			if(strim($_REQUEST['user_name'])!='')
			{		
				$where .= " and u.user_name like '%".strim($_REQUEST['user_name'])."%'";
			}
			//证件号
			if(strim($_REQUEST['ident_no'])!='')
			{		
				$where .= " and t.id_card like '%".strim($_REQUEST['ident_no'])."%'";
			}
			//手机号
			if(strim($_REQUEST['mobile'])!='')
			{		
				$where .= " and t.bf_account like '%".strim($_REQUEST['mobile'])."%'";
			}
			if(strim($start_time)!="")
			{
				$where .= " and t.create_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and t.create_time <=".  to_timespan(strim($end_time));
			}
			$list = $GLOBALS['db']->getAll("select t.*,u.user_name,u.user_type,
			t.id_card as pIdentNo,t.name as pRealName,t.bf_account as pMobileNo,u.email as pEmail,t.create_time as pSmDate
			 from ".DB_PREFIX."baofoo_bind_state t left join ".DB_PREFIX."user u on t.user_id = u.id ".$where ." order by t.id desc ".$limit);
			foreach($list as $k => $v)
			{
				$list[$k]["pStatus"] = l("IPS_STATUS_10");
				$list[$k]["pIdentType"] = "身份证";
				$list[$k]["pSmDate"] = to_date($v["pSmDate"],"Y-m-d");
				$list[$k]["user_type"] = l("IPS_TYPE_".$v["user_type"]);
			}
			
			$list_count = $GLOBALS['db']->getOne("select count(*)
			 from ".DB_PREFIX."baofoo_bind_state t left join ".DB_PREFIX."user u on t.user_id = u.id ".$where);
		}
		
		
		$page = new Page($list_count,app_conf("PAGE_SIZE"));   //初始化分页对象 

		$p  =  $page->show();
		$this->assign('page',$p);

		$this->assign("list",$list);
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
			$where = " where pErrCode ='MG00000F' ";
			//姓名
			if(strim($_REQUEST['user_name'])!='')
			{		
				$where .= " and user_name like '%".strim($_REQUEST['user_name'])."%'";
			}
			//证件号
			if(strim($_REQUEST['ident_no'])!='')
			{		
				$where .= " and pIdentNo like '%".strim($_REQUEST['ident_no'])."%'";
			}
			//手机号
			if(strim($_REQUEST['mobile'])!='')
			{		
				$where .= " and pMobileNo like '%".strim($_REQUEST['mobile'])."%'";
			}
			
			if(strim($start_time)!="")
			{
				$where .= " and UNIX_TIMESTAMP(pSmDate) >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and UNIX_TIMESTAMP(pSmDate) <=".  to_timespan(strim($end_time));
			}

			$list = $GLOBALS['db']->getAll("select irs.*,u.user_name from ".DB_PREFIX."ips_create_new_acct irs left join ".DB_PREFIX."user u on irs.user_id = u.id ".$where ." order by irs.id desc ".$limit);	
			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."ips_register_subject irs left join ".DB_PREFIX."user u on irs.user_id = u.id ".$where);
			foreach($list as $k => $v)
			{
				$list[$k]["pStatus"] = l("IPS_STATUS_".$v["pStatus"]);
				$list[$k]["pIdentType"] = l("IPS_IDENT_TYPE_".$v["pIdentType"]);
				$list[$k]["user_type"] = l("IPS_TYPE_".$v["user_type"]);
			}
		}
		//易宝
		elseif(strtolower($className) == "yeepay")
		{
			$where = " where is_callback = 1 and  t.code ='1' ";
			//姓名
			if(strim($_REQUEST['user_name'])!='')
			{		
				$where .= " and u.user_name like '%".strim($_REQUEST['user_name'])."%'";
			}
			//证件号
			if(strim($_REQUEST['ident_no'])!='')
			{		
				$where .= " and t.idCardNo like '%".strim($_REQUEST['ident_no'])."%'";
			}
			//手机号
			if(strim($_REQUEST['mobile'])!='')
			{		
				$where .= " and t.mobile like '%".strim($_REQUEST['mobile'])."%'";
			}
			if(strim($start_time)!="")
			{
				$where .= " and t.create_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and t.create_time <=".  to_timespan(strim($end_time));
			}
			$list = $GLOBALS['db']->getAll("select t.*,u.user_name,u.user_type,t.idCardType as pIdentType,
			t.idCardNo as pIdentNo,t.realName as pRealName,t.mobile as pMobileNo,t.email as pEmail,t.create_time as pSmDate
			 from ".DB_PREFIX."yeepay_register t left join ".DB_PREFIX."user u on t.platformUserNo = u.id ".$where ." order by t.id desc ".$limit);

			foreach($list as $k => $v)
			{
				$list[$k]["pStatus"] = l("IPS_STATUS_10");
				
				if(strim($v["pIdentType"]) == "G1_IDCARD")
				{
					$list[$k]["pIdentType"] = "一代身份证";
				}
				else
				{
					$list[$k]["pIdentType"] = "一代身份证";
				}
				$list[$k]["pSmDate"] = to_date($v["pSmDate"],"Y-m-d");
				$list[$k]["user_type"] = l("IPS_TYPE_".$v["user_type"]);
			}
			
			$list_count = $GLOBALS['db']->getOne("select count(*)
			 from ".DB_PREFIX."yeepay_register t left join ".DB_PREFIX."user u on t.platformUserNo = u.id ".$where);
		}
		//宝付
		elseif(strtolower($className) == "baofoo")
		{
			$where = " where is_callback = 1 and  t.code ='CSD000' ";
			//姓名
			if(strim($_REQUEST['user_name'])!='')
			{		
				$where .= " and u.user_name like '%".strim($_REQUEST['user_name'])."%'";
			}
			//证件号
			if(strim($_REQUEST['ident_no'])!='')
			{		
				$where .= " and t.id_card like '%".strim($_REQUEST['ident_no'])."%'";
			}
			//手机号
			if(strim($_REQUEST['mobile'])!='')
			{		
				$where .= " and t.bf_account like '%".strim($_REQUEST['mobile'])."%'";
			}
			if(strim($start_time)!="")
			{
				$where .= " and t.create_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and t.create_time <=".  to_timespan(strim($end_time));
			}
			$list = $GLOBALS['db']->getAll("select t.*,u.user_name,u.user_type,
			t.id_card as pIdentNo,t.name as pRealName,t.bf_account as pMobileNo,u.email as pEmail,t.create_time as pSmDate
			 from ".DB_PREFIX."baofoo_bind_state t left join ".DB_PREFIX."user u on t.user_id = u.id ".$where ." order by t.id desc ".$limit);
			foreach($list as $k => $v)
			{
				$list[$k]["pStatus"] = l("IPS_STATUS_10");
				$list[$k]["pIdentType"] = "身份证";
				$list[$k]["pSmDate"] = to_date($v["pSmDate"],"Y-m-d");
				$list[$k]["user_type"] = l("IPS_TYPE_".$v["user_type"]);
			}
			
			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal ".$where);
		}
			
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv'), $page+1);
			
			$list_value_old = array('id'=>'""', 'user_name'=>'""', 'user_type'=>'""','pIdentType'=>'""', 'pIdentNo'=>'""', 'pRealName'=>'""','pMobileNo'=>'""','pEmail'=>'""', 'pSmDate'=>'""', 'pStatus'=>'""');
	    	
			if($page == 1)
	    	{	
		    	$content = iconv("utf-8","gbk","编号,用户名,用户类型,证件类型,证件号码,姓名,手机号,注册邮箱,提交日期,开户状态");	    		    	
		    	$content = $content . "\n";
	    	}

			
			foreach($list as $k=> $v)
			{
				$list_value = $list_value_old;

				$list_value["user_name"] = '"' . iconv('utf-8','gbk', $v["user_name"]). '"';
				$list_value["user_type"] = '"' . iconv('utf-8','gbk',$v["user_type"]) . '"';
				$list_value["pIdentType"] =  '"' . iconv('utf-8','gbk', $v["pIdentType"]) . '"';
				
				$list_value["id"] =  '"' . iconv('utf-8','gbk', $v["id"]). '"';
				
				$list_value["pIdentNo"] =  '"' . iconv('utf-8','gbk',  $v["pIdentNo"]). '"';
				
				$list_value["pRealName"] =  '"' . iconv('utf-8','gbk',  $v["pRealName"]). '"';
				
				$list_value["pMobileNo"] =  '"' . iconv('utf-8','gbk',  $v["pMobileNo"]). '"';
				
				$list_value["pEmail"] =  '"' . iconv('utf-8','gbk',  $v["pEmail"]). '"';
				
				$list_value["pSmDate"] =  '"' . iconv('utf-8','gbk',  $v["pSmDate"]). '"';
				
				$list_value["pStatus"] =  '"' . iconv('utf-8','gbk',  $v["pStatus"]). '"';

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

	/*标的登记*/
	public function trade()
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
			$where = " where pErrCode in ('MG00000F','MG02500F','MG02501F','MG02505F') "; 

			//标号
			if(strim($_REQUEST['pBidNo'])!='')
			{		
				$where .= " and deal_id like '%".strim($_REQUEST['pBidNo'])."%'";
			}
			//标名
			if(strim($_REQUEST['deal_name'])!='')
			{		
				$where .= " and d.name like '%".strim($_REQUEST['deal_name'])."%'";
			}

			if(strim($start_time)!="")
			{
				$where .= " and UNIX_TIMESTAMP(pRegDate) >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and UNIX_TIMESTAMP(pRegDate) <=".  to_timespan(strim($end_time));
			}
			
			$list = $GLOBALS['db']->getAll("select irs.*,d.name from ".DB_PREFIX."ips_register_subject irs left join ".DB_PREFIX."deal d on irs.deal_id = d.id ".$where ." order by irs.id desc ".$limit);	

			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."ips_register_subject irs left join ".DB_PREFIX."deal d on irs.deal_id = d.id ".$where);
			foreach($list as $k => $v)
			{
				$list[$k]["status"] = l("P_T_STATUS_".$v["status"]);
				$list[$k]["pTrdCycleType"] = l("P_TRD_CYCLE_TYPE_".$v["pTrdCycleType"]);
				$list[$k]["pRepayMode"] = l("P_REPAY_MODE_".$v["pRepayMode"]);
				$list[$k]["pOperationType"] = l("P_OPERACTION_TYPE_".$v["pOperationType"]);
				$list[$k]["pAcctType"] = l("P_ACCT_TYPE_".$v["pAcctType"]);
				$list[$k]["pBidStatus"] = l("P_BID_STATUS_".$v["pBidStatus"]);
			}
		}
		//易宝  //宝付
		elseif(strtolower($className) == "yeepay" || strtolower($className) == "baofoo")
		{
			$where = " where mer_bill_no <>'' "; 
			
			//标号
			if(strim($_REQUEST['pBidNo'])!='')
			{		
				$where .= " and d.id like '%".strim($_REQUEST['pBidNo'])."%'";
			}
			//标名
			if(strim($_REQUEST['deal_name'])!='')
			{		
				$where .= " and d.name like '%".strim($_REQUEST['deal_name'])."%'";
			}
			
			if(strim($start_time)!="")
			{
				$where .= " and d.create_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and d.create_time <=".  to_timespan(strim($end_time));
			}
			$list = $GLOBALS['db']->getAll("select d.id,name, d.id as pBidNo,d.id as deal_id,deal_status,
			borrow_amount as pLendAmt,guarantees_money as pGuaranteesAmt,
			(services_fee / 100 * borrow_amount) as pLendFee,u.real_name as pRealName,
			repay_time as pTrdCycleValue,d.rate,
			repay_time_type as pTrdCycleType,
			FROM_UNIXTIME( d.create_time, '%Y-%m-%d') as pRegDate
			 from ".DB_PREFIX."deal d left join ".DB_PREFIX."user u on d.user_id = u.id ".$where ." order by d.id desc ".$limit);
			$list_count = $GLOBALS['db']->getOne("select count(*)
			 from ".DB_PREFIX."deal d left join ".DB_PREFIX."user u on d.user_id = u.id ".$where);
			
			foreach($list as $k => $v)
			{
				switch($v["deal_status"])
				{
					case 0: $list[$k]["pBidStatus"] = "待等材料";
					break;
					case 1: $list[$k]["pBidStatus"] = "进行中";
					break;
					case 2: $list[$k]["pBidStatus"] = "满标";
					break;
					case 3: $list[$k]["pBidStatus"] = "流标";
					break;
					case 4: $list[$k]["pBidStatus"] = "还款中";
					break;
					case 5: $list[$k]["pBidStatus"] = "已还清";
					break;
				}
				$list[$k]["pTrdLendRate"] = number_format($list[$k]['rate'],2);
				$list[$k]["pTrdCycleType"] = $v["pTrdCycleType"] == 0 ?"天":"月";
				
				if ($v['loantype'] == 0){
					$list[$k]['pRepayMode'] = 1;//等额本息
				}else if ($v['loantype'] == 1){
					$list[$k]['pRepayMode'] = 2;//付息还本
				}else if($v['loantype'] == 2){
					$list[$k]['pRepayMode'] = 99;//到期本息
				}else{
					$list[$k]['pRepayMode'] = 99;
				}
				$list[$k]["pRepayMode"] = l("P_REPAY_MODE_".$list[$k]["pRepayMode"]);
				$list[$k]["pAcctType"] = l("P_ACCT_TYPE_".$v["pAcctType"]);
			}
		}
		
		$page = new Page($list_count,app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$this->assign('page',$p);
		
		$this->assign("list",$list);
		$this->assign("start_time",$start_time);
		$this->assign("end_time",$end_time);

		$this->display ();
	}
	
	public function trade_export_csv($page = 1)
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
			$where = " where pErrCode in ('MG00000F','MG02500F','MG02501F','MG02505F') "; 

			//标号
			if(strim($_REQUEST['pBidNo'])!='')
			{		
				$where .= " and deal_id like '%".strim($_REQUEST['pBidNo'])."%'";
			}
			//标名
			if(strim($_REQUEST['deal_name'])!='')
			{		
				$where .= " and d.name like '%".strim($_REQUEST['deal_name'])."%'";
			}

			if(strim($start_time)!="")
			{
				$where .= " and UNIX_TIMESTAMP(pRegDate) >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and UNIX_TIMESTAMP(pRegDate) <=".  to_timespan(strim($end_time));
			}
			
			$list = $GLOBALS['db']->getAll("select irs.*,d.name from ".DB_PREFIX."ips_register_subject irs left join ".DB_PREFIX."deal d on irs.deal_id = d.id ".$where ." order by irs.id desc ".$limit);	

			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."ips_register_subject irs left join ".DB_PREFIX."deal d on irs.deal_id = d.id ".$where);
			foreach($list as $k => $v)
			{
				$list[$k]["status"] = l("P_T_STATUS_".$v["status"]);
				$list[$k]["pTrdCycleType"] = l("P_TRD_CYCLE_TYPE_".$v["pTrdCycleType"]);
				$list[$k]["pRepayMode"] = l("P_REPAY_MODE_".$v["pRepayMode"]);
				$list[$k]["pOperationType"] = l("P_OPERACTION_TYPE_".$v["pOperationType"]);
				$list[$k]["pAcctType"] = l("P_ACCT_TYPE_".$v["pAcctType"]);
				$list[$k]["pBidStatus"] = l("P_BID_STATUS_".$v["pBidStatus"]);
			}
		}
		//易宝  //宝付
		elseif(strtolower($className) == "yeepay" || strtolower($className) == "baofoo")
		{
			$where = " where mer_bill_no <>'' "; 
			
			//标号
			if(strim($_REQUEST['pBidNo'])!='')
			{		
				$where .= " and d.id like '%".strim($_REQUEST['pBidNo'])."%'";
			}
			//标名
			if(strim($_REQUEST['deal_name'])!='')
			{		
				$where .= " and d.name like '%".strim($_REQUEST['deal_name'])."%'";
			}
			
			if(strim($start_time)!="")
			{
				$where .= " and d.create_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and d.create_time <=".  to_timespan(strim($end_time));
			}
			$list = $GLOBALS['db']->getAll("select d.id,name, d.id as pBidNo,d.id as deal_id,deal_status,
			borrow_amount as pLendAmt,guarantees_money as pGuaranteesAmt,
			(services_fee / 100 * borrow_amount) as pLendFee,u.real_name as pRealName,
			repay_time as pTrdCycleValue,
			repay_time_type as pTrdCycleType,
			FROM_UNIXTIME( d.create_time, '%Y-%m-%d') as pRegDate
			 from ".DB_PREFIX."deal d left join ".DB_PREFIX."user u on d.user_id = u.id ".$where ." order by d.id desc ".$limit);
			$list_count = $GLOBALS['db']->getOne("select count(*)
			FROM_UNIXTIME( d.create_time, '%Y-%m-%d') as pRegDate
			 from ".DB_PREFIX."deal d left join ".DB_PREFIX."user u on d.user_id = u.id ".$where);
			
			foreach($list as $k => $v)
			{
				switch($v["deal_status"])
				{
					case 0: $list[$k]["pBidStatus"] = "待等材料";
					break;
					case 1: $list[$k]["pBidStatus"] = "进行中";
					break;
					case 2: $list[$k]["pBidStatus"] = "满标";
					break;
					case 3: $list[$k]["pBidStatus"] = "流标";

					break;
					case 4: $list[$k]["pBidStatus"] = "还款中";
					break;
					case 5: $list[$k]["pBidStatus"] = "已还清";
					break;
				}
				$list[$k]["pTrdLendRate"] = str_replace(',', '',number_format($deal['rate']+10,2));
				$list[$k]["pTrdCycleType"] = $v["repay_time_type"] == 0 ?"天":"月";
				
				if ($v['loantype'] == 0){
					$list[$k]['pRepayMode'] = 1;//等额本息
				}else if ($v['loantype'] == 1){
					$list[$k]['pRepayMode'] = 2;//付息还本
				}else if($v['loantype'] == 2){
					$list[$k]['pRepayMode'] = 99;//到期本息
				}else{
					$list[$k]['pRepayMode'] = 99;
				}
				$list[$k]["pRepayMode"] = l("P_REPAY_MODE_".$list[$k]["pRepayMode"]);
				$list[$k]["pAcctType"] = l("P_ACCT_TYPE_".$v["pAcctType"]);
			}
		}
		
		if($list)
		{
			register_shutdown_function(array(&$this, 'trade_export_csv'), $page+1);

			$list_value_old = array('id'=>'""', 'pBidNo'=>'""', 'name'=>'""','pRegDate'=>'""', 'pLendAmt'=>'""',
			 'pGuaranteesAmt'=>'""','pTrdLendRate'=>'""','pTrdCycleValue'=>'""', 
			 'pTrdCycleType'=>'""', 'pRepayMode'=>'""', 'pRealName'=>'""','pBidStatus'=>'""');
	    	if($page == 1)
	    	{
		    	$content = iconv("utf-8","gbk","编号,标号,贷款名称,商户日期,借款金额,保证金,利率,周期值,周期类型,还款方式,姓名,状态");	    		    	
		    	$content = $content . "\n";
	    	}

			
			foreach($list as $k=> $v)
			{
				$list_value = $list_value_old;
				
				$list_value["id"] =  '"' . iconv('utf-8','gbk', $v["id"]). '"';
				
				$list_value["pBidNo"] =  '"' . iconv('utf-8','gbk', $v["pBidNo"]). '"';
				
				$list_value["name"] = '"' . iconv('utf-8','gbk', $v["name"]). '"';
				
				$list_value["pRegDate"] = '"' . iconv('utf-8','gbk', $v["pRegDate"]) . '"';
				
				$list_value["pLendAmt"] = '"' . iconv('utf-8','gbk', $v["pLendAmt"]) . '"';
				
				$list_value["pGuaranteesAmt"] = '"' . iconv('utf-8','gbk', $v["pGuaranteesAmt"]) . '"';
				
				$list_value["pTrdLendRate"] =  '"' . iconv('utf-8','gbk',  $v["pTrdLendRate"]). '"';
				
				$list_value["pTrdCycleType"] =  '"' . iconv('utf-8','gbk', $v["pTrdCycleType"]). '"';
				
				$list_value["pTrdCycleValue"] =  '"' . iconv('utf-8','gbk',  $v["pTrdCycleValue"]). '"';
				
				$list_value["pRepayMode"] =  '"' . iconv('utf-8','gbk',  $v["pRepayMode"]). '"';
				
				$list_value["pRealName"] =  '"' . iconv('utf-8','gbk',  $v["pRealName"]). '"';
				
				$list_value["pBidStatus"] =  '"' . iconv('utf-8','gbk', $v["pBidStatus"]). '"';

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
			$where = " where pErrCode ='MG00000F'  ";
		
			if(isset($_REQUEST['pBidNo'])&&strim($_REQUEST['pBidNo'])!='')
			{
				$where.=" and pBidNo like '%".strim($_REQUEST['pBidNo'])."%'";
			}
			if(isset($_REQUEST['deal_name'])&&strim($_REQUEST['deal_name'])!='')
			{
				$where.=" and b.name like '%".strim($_REQUEST['deal_name'])."%'";
			}
			if(isset($_REQUEST['user_name'])&&strim($_REQUEST['user_name'])!='')
			{
				$where.=" and u.user_name like '%".strim($_REQUEST['user_name'])."%'";
			}

			if(strim($start_time)!="")
			{
				$where .= " and UNIX_TIMESTAMP(pMerDate) >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and UNIX_TIMESTAMP(pMerDate) <=".  to_timespan(strim($end_time));
			}
			$list = $GLOBALS['db']->getAll("select a.*,b.name as deal_name,u.user_name from ".DB_PREFIX."ips_register_creditor as a left join ".DB_PREFIX."deal as b on a.deal_id = b.id left join ".DB_PREFIX."user u on u.id = a.user_id ".$where." order by id desc ".$limit);
			foreach($list as $k=>$v)
			{
				$list[$k]["pRegType"] = l("P_REG_TYPE_".$v["pRegType"]);
				$list[$k]["pAcctType"] = l("P_ACCT_TYPE_".$v["pAcctType"]);
				$list[$k]["pStatus"] = l("P_CREDITOR_STATUS_".$v["pStatus"]);
			}
			
			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."ips_register_creditor as a left join ".DB_PREFIX."deal as b on a.deal_id = b.id ".$where);
		}
		//易宝
		elseif(strtolower($className) == "yeepay")
		{
			$where = " where bizType = 'TENDER'  and a.code ='1' and is_callback = 1 ";
			
			if(isset($_REQUEST['pBidNo'])&&strim($_REQUEST['pBidNo'])!='')
			{
				$where.=" and b.id like '%".strim($_REQUEST['pBidNo'])."%'";
			}
			if(isset($_REQUEST['deal_name'])&&strim($_REQUEST['deal_name'])!='')
			{
				$where.=" and b.name like '%".strim($_REQUEST['deal_name'])."%'";
			}
			if(isset($_REQUEST['user_name'])&&strim($_REQUEST['user_name'])!='')
			{
				$where.=" and u.user_name like '%".strim($_REQUEST['user_name'])."%'";
			}
			
			if(strim($start_time)!="")
			{
				$where .= " and a.create_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and a.create_time <=".  to_timespan(strim($end_time));
			}
			$list = $GLOBALS['db']->getAll("select a.id, b.name as deal_name,     
			a.tenderOrderNo as pBidNo,paymentAmount as pTrdAmt, u.user_name,
			FROM_UNIXTIME(a.create_time,'%Y-%m-%d') as pMerDate,
			fee as pFee, paymentAmount as pTransferAmt, tenderAmount as pAuthAmt,u.real_name as pRealName,
			FROM_UNIXTIME(a.create_time ,'%Y-%m-%d') as pIpsTime,
			1 as pRegType
			 from ".DB_PREFIX."yeepay_cp_transaction as a left join ".DB_PREFIX."deal as b on a.tenderOrderNo = b.id 
			 left join ".DB_PREFIX."user u on a.platformUserNo = u.id ".$where." order by a.id desc ".$limit);

			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."yeepay_cp_transaction as a 
			left join ".DB_PREFIX."deal as b on a.tenderOrderNo = b.id  
			left join ".DB_PREFIX."user u on a.platformUserNo = u.id ".$where);
			
			foreach($list as $k=>$v)
			{
				$list[$k]["pRegType"] = l("P_REG_TYPE_".$v["pRegType"]);
			}
		}
		//宝付
		elseif(strtolower($className) == "baofoo")
		{
			$where = " where action_type = '1' and  a.code ='CSD000' and is_callback = 1 ";
			
			if(isset($_REQUEST['pBidNo'])&&strim($_REQUEST['pBidNo'])!='')
			{
				$where.=" and b.id like '%".strim($_REQUEST['pBidNo'])."%'";
			}
			if(isset($_REQUEST['deal_name'])&&strim($_REQUEST['deal_name'])!='')
			{
				$where.=" and b.name like '%".strim($_REQUEST['deal_name'])."%'";
			}
			if(isset($_REQUEST['user_name'])&&strim($_REQUEST['user_name'])!='')
			{
				$where.=" and u.user_name like '%".strim($_REQUEST['user_name'])."%'";
			}
			
			if(strim($start_time)!="")
			{
				$where .= " and (a.req_time/1000) >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and (a.req_time/1000) <=".  to_timespan(strim($end_time));
			}
			$list = $GLOBALS['db']->getAll("select a.id, b.name as deal_name,
			a.cus_id as pBidNo,load_amount as pTrdAmt, u.user_name, u.real_name as pRealName,
			FROM_UNIXTIME(a.req_time/1000,'%Y-%m-%d') as pMerDate,
			fee as pFee, amount as pTransferAmt, b.borrow_amount as pAuthAmt,
			FROM_UNIXTIME(a.req_time/1000 ,'%Y-%m-%d')as pIpsTime,
			1 as pRegType
			 from ".DB_PREFIX."baofoo_business as a left join ".DB_PREFIX."deal as b on a.cus_id = b.id 
			 left join ".DB_PREFIX."user u on u.id = a.load_user_id ".$where." order by a.id desc ".$limit);
			 
			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."baofoo_business as a 
			left join ".DB_PREFIX."deal as b on a.cus_id = b.id 
			 left join ".DB_PREFIX."user u on u.id = a.load_user_id 
			".$where);
			
			foreach($list as $k=>$v)
			{
				$list[$k]["pRegType"] = l("P_REG_TYPE_".$v["pRegType"]);
			}
		}
		
		$page = new Page($list_count,app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$this->assign('page',$p);
		
		$this->assign("list",$list);
		$this->assign("start_time",$start_time);
		$this->assign("end_time",$end_time);
		
		$this->display ();
	}
	
	public function creditor_export_csv($page = 1)
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
			$where = " where pErrCode ='MG00000F'  ";
		
			if(isset($_REQUEST['pBidNo'])&&strim($_REQUEST['pBidNo'])!='')
			{
				$where.=" and pBidNo like '%".strim($_REQUEST['pBidNo'])."%'";
			}
			if(isset($_REQUEST['deal_name'])&&strim($_REQUEST['deal_name'])!='')
			{
				$where.=" and b.name like '%".strim($_REQUEST['deal_name'])."%'";
			}
			if(isset($_REQUEST['user_name'])&&strim($_REQUEST['user_name'])!='')
			{
				$where.=" and u.user_name like '%".strim($_REQUEST['user_name'])."%'";
			}

			if(strim($start_time)!="")
			{
				$where .= " and UNIX_TIMESTAMP(pMerDate) >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and UNIX_TIMESTAMP(pMerDate) <=".  to_timespan(strim($end_time));
			}
			$list = $GLOBALS['db']->getAll("select a.*,b.name as deal_name,u.user_name from ".DB_PREFIX."ips_register_creditor as a left join ".DB_PREFIX."deal as b on a.deal_id = b.id left join ".DB_PREFIX."user u on u.id = a.user_id ".$where." order by id desc ".$limit);
			foreach($list as $k=>$v)
			{
				$list[$k]["pRegType"] = l("P_REG_TYPE_".$v["pRegType"]);
				$list[$k]["pAcctType"] = l("P_ACCT_TYPE_".$v["pAcctType"]);
				$list[$k]["pStatus"] = l("P_CREDITOR_STATUS_".$v["pStatus"]);
			}
			
			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."ips_register_creditor as a left join ".DB_PREFIX."deal as b on a.deal_id = b.id ".$where);
		}
		//易宝
		elseif(strtolower($className) == "yeepay")
		{
			$where = " where bizType = 'TENDER' and is_complete_transaction = 0 and a.code ='1' and is_callback = 1 ";
			
			if(isset($_REQUEST['pBidNo'])&&strim($_REQUEST['pBidNo'])!='')
			{
				$where.=" and b.id like '%".strim($_REQUEST['pBidNo'])."%'";
			}
			if(isset($_REQUEST['deal_name'])&&strim($_REQUEST['deal_name'])!='')
			{
				$where.=" and b.name like '%".strim($_REQUEST['deal_name'])."%'";
			}
			if(isset($_REQUEST['user_name'])&&strim($_REQUEST['user_name'])!='')
			{
				$where.=" and u.user_name like '%".strim($_REQUEST['user_name'])."%'";
			}
			
			if(strim($start_time)!="")
			{
				$where .= " and a.create_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and a.create_time <=".  to_timespan(strim($end_time));
			}
			$list = $GLOBALS['db']->getAll("select a.id, b.name as deal_name,     
			a.tenderOrderNo as pBidNo,(paymentAmount-fee) as pTrdAmt, u.user_name,
			FROM_UNIXTIME(a.create_time,'%Y-%m-%d') as pMerDate,
			fee as pFee, paymentAmount as pTransferAmt, tenderAmount as pAuthAmt,u.real_name as pRealName,
			FROM_UNIXTIME(a.create_time ,'%Y-%m-%d') as pIpsTime,
			1 as pRegType
			 from ".DB_PREFIX."yeepay_cp_transaction as a left join ".DB_PREFIX."deal as b on a.tenderOrderNo = b.id 
			 left join ".DB_PREFIX."user u on a.platformUserNo = u.id ".$where." order by a.id desc ".$limit);

			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."yeepay_cp_transaction as a 
			left join ".DB_PREFIX."deal as b on a.tenderOrderNo = b.id  
			left join ".DB_PREFIX."user u on a.platformUserNo = u.id ".$where);
			
			foreach($list as $k=>$v)
			{
				$list[$k]["pRegType"] = l("P_REG_TYPE_".$v["pRegType"]);
			}
		}
		//宝付
		elseif(strtolower($className) == "baofoo")
		{
			$where = " where action_type = '1' and  a.code ='CSD000' and is_callback = 1 ";
			
			if(isset($_REQUEST['pBidNo'])&&strim($_REQUEST['pBidNo'])!='')
			{
				$where.=" and b.id like '%".strim($_REQUEST['pBidNo'])."%'";
			}
			if(isset($_REQUEST['deal_name'])&&strim($_REQUEST['deal_name'])!='')
			{
				$where.=" and b.name like '%".strim($_REQUEST['deal_name'])."%'";
			}
			if(isset($_REQUEST['user_name'])&&strim($_REQUEST['user_name'])!='')
			{
				$where.=" and u.user_name like '%".strim($_REQUEST['user_name'])."%'";
			}
			
			if(strim($start_time)!="")
			{
				$where .= " and (a.req_time/1000) >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and (a.req_time/1000) <=".  to_timespan(strim($end_time));
			}
			$list = $GLOBALS['db']->getAll("select a.id, b.name as deal_name,
			a.cus_id as pBidNo,(load_amount-fee) as pTrdAmt, u.user_name, u.real_name as pRealName,
			FROM_UNIXTIME(a.req_time/1000,'%Y-%m-%d') as pMerDate,
			fee as pFee, amount as pTransferAmt, b.borrow_amount as pAuthAmt,
			FROM_UNIXTIME(a.req_time/1000 ,'%Y-%m-%d')as pIpsTime,
			1 as pRegType
			 from ".DB_PREFIX."baofoo_business as a left join ".DB_PREFIX."deal as b on a.cus_id = b.id 
			 left join ".DB_PREFIX."user u on u.id = a.load_user_id ".$where." order by a.id desc ".$limit);
			 
			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."baofoo_business as a 
			left join ".DB_PREFIX."deal as b on a.cus_id = b.id 
			 left join ".DB_PREFIX."user u on u.id = a.load_user_id 
			".$where);
			
			foreach($list as $k=>$v)
			{
				$list[$k]["pRegType"] = l("P_REG_TYPE_".$v["pRegType"]);
			}
		}

		if($list)
		{
			register_shutdown_function(array(&$this, 'creditor_export_csv'), $page+1);
			

			$list_value_old = array('id'=>'""', 'pBidNo'=>'""', 'deal_name'=>'""','user_name'=>'""', 
			'pMerDate'=>'""', 'pRegType'=>'""','pAuthAmt'=>'""','pTrdAmt'=>'""',
			 'pFee'=>'""', 'pRealName'=>'""');
	    	
			if($page == 1)
	    	{
		    	$content = iconv("utf-8","gbk","编号,标号,贷款名称,用户名,商户日期,登记方式,债权面额,交易金额,手续费,姓名");	    		    	
		    	$content = $content . "\n";
	    	}
			foreach($list as $k=> $v)
			{
			
				$list_value = $list_value_old;
				$list_value["id"] =  '"' . iconv('utf-8','gbk', $v["id"]). '"';
				$list_value["pBidNo"] =  '"' . iconv('utf-8','gbk', $v["pBidNo"]) . '"';
				$list_value["deal_name"] =  '"' . iconv('utf-8','gbk', $v["deal_name"]) . '"';
				$list_value["user_name"] = '"' . iconv('utf-8','gbk', $v["user_name"]). '"';
				$list_value["pMerDate"] = '"' . iconv('utf-8','gbk', $v["pMerDate"]) . '"';
				$list_value["pRegType"] =  '"' . iconv('utf-8','gbk', $v["pRegType"]). '"';
				$list_value["pAuthAmt"] =  '"' . iconv('utf-8','gbk', $v["pAuthAmt"]). '"';
				$list_value["pTrdAmt"] =  '"' . iconv('utf-8','gbk', $v["pTrdAmt"]). '"';
				$list_value["pFee"] =  '"' . iconv('utf-8','gbk', $v["pFee"]). '"';
				$list_value["pRealName"] =  '"' . iconv('utf-8','gbk', $v["pRealName"]). '"';
				
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
	public function guarantor()
	{
		$where = " pErrCode ='MG00000F' ";
		//定义条件
		
		if(isset($_REQUEST['pMerCode'])&&strim($_REQUEST['pMerCode'])!='')
		{
			$where.=" and pMerCode like '%".strim($_REQUEST['pMerCode'])."%'";
		}
		if(isset($_REQUEST['pBidNo'])&&strim($_REQUEST['pBidNo'])!='')
		{
			$where.=" and pBidNo like '%".strim($_REQUEST['pBidNo'])."%'";
		}
		if(isset($_REQUEST['pMerBillNo'])&&strim($_REQUEST['pMerBillNo'])!='')
		{
			$where.=" and pMerBillNo like '%".strim($_REQUEST['pMerBillNo'])."%'";
		}
		if(isset($_REQUEST['pAcctType'])&&intval($_REQUEST['pAcctType'])>=0)
			$where.=" and pAcctType = '".intval($_REQUEST['pAcctType'])."'";
			
		if(isset($_REQUEST['pFromIdentNo'])&&strim($_REQUEST['pFromIdentNo'])!='')
		{		
			$where.=" and pFromIdentNo like '%".strim($_REQUEST['pFromIdentNo'])."%'";
		}

		if(isset($_REQUEST['pAccountName'])&&strim($_REQUEST['pAccountName'])!='')
		{		
			$where.=" and pAccountName like '%".strim($_REQUEST['pAccountName'])."%'";
		}
		
		if(isset($_REQUEST['pAccount'])&&strim($_REQUEST['pAccount'])!='')
			$where.=" and pAccount = '".intval($_REQUEST['pAccount'])."'";
				
		if(isset($_REQUEST['pStatus'])&&intval(strim($_REQUEST['pStatus']))>=0)
			$where.=" and pStatus = '".intval($_REQUEST['pStatus'])."'";	
		
		
		if(isset($_REQUEST['pP2PBillNo'])&&strim($_REQUEST['pP2PBillNo'])!='')
			$where.=" and pP2PBillNo = '".$_REQUEST['pP2PBillNo']."'";	
			
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

		if(strim($start_time)!="")
		{
			$where .= " and UNIX_TIMESTAMP(pMerDate) >=".to_timespan(strim($start_time));
		}
		if(strim($end_time) !="")
		{
			$where .= " and UNIX_TIMESTAMP(pMerDate) <=".  to_timespan(strim($end_time));
		}
		
		$model = D ("ips_register_guarantor");
		if (! empty ( $model )) {
			$this->_list ( $model, $where,"id");
		}
		
		$this->display ();
	}
	/*
	public function guarantor_delete()
	{
		//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M("ips_register_guarantor")->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['id'];	
				}
				if($info) $info = implode(",",$info);
				$list = M("ips_register_guarantor")->where ( $condition )->delete();
				if ($list!==false) {
					save_log($info.l("DELETE_SUCCESS"),1);
					$this->success (l("DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("DELETE_FAILED"),0);
					$this->error (l("DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}		
	}
	*/
	public function guarantor_view()
	{
		$id = intval($_REQUEST['id']);
		$ips_info = M("ips_register_guarantor")->where("id=".$id)->find();
		if(!$ips_info)
		{
			$this->error(l("INVALID_ORDER"));
		}
		//$ips_info_items = M("ips_create_new_acct")->where(" id=".$ips_info['id'])->findAll();

		
		$ips_info["deal_name"] = M("deal")->where(" id=".$ips_info["deal_id"])->getField("name");
		
		if($ips_info["pAcctType"] == 0)
		{
			$ips_info["user_name"] = M("user")->where(" id=".$ips_info["agency_id"])->getField("name");
		}
		else if($ips_info["pAcctType"] == 1)
		{
			$ips_info["user_name"] = M("user")->where(" id=".$ips_info["agency_id"])->getField("user_name");
		}
		if($ips_info["pStatus"])
		{
			$ips_info["pStatus"] =  l("P_CREDITOR_STATUS_".$ips_info["pStatus"]);
		}
		if($ips_info["pAcctType"])
		{
			$ips_info["pAcctType"] =  l("P_ACCT_TYPE_".$ips_info["pAcctType"]);
		}
		
		$ips_info['is_callback'] = l("IPS_PASS_".$ips_info["is_callback"]);

		$this->assign("ips_info",$ips_info);
		
		$this->display();
	}
	public function guarantor_export_csv($page = 1)
	{
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
		

		$where = " pErrCode ='MG00000F' ";
		//定义条件
		
		if(isset($_REQUEST['pMerCode'])&&strim($_REQUEST['pMerCode'])!='')
		{
			$where.=" and pMerCode like '%".strim($_REQUEST['pMerCode'])."%'";
		}
		if(isset($_REQUEST['pBidNo'])&&strim($_REQUEST['pBidNo'])!='')
		{
			$where.=" and pBidNo like '%".strim($_REQUEST['pBidNo'])."%'";
		}
		if(isset($_REQUEST['pMerBillNo'])&&strim($_REQUEST['pMerBillNo'])!='')
		{
			$where.=" and pMerBillNo like '%".strim($_REQUEST['pMerBillNo'])."%'";
		}
		if(isset($_REQUEST['pAcctType'])&&intval($_REQUEST['pAcctType'])>=0)
			$where.=" and pAcctType = '".intval($_REQUEST['pAcctType'])."'";
			
		if(isset($_REQUEST['pFromIdentNo'])&&strim($_REQUEST['pFromIdentNo'])!='')
		{		
			$where.=" and pFromIdentNo like '%".strim($_REQUEST['pFromIdentNo'])."%'";
		}

		if(isset($_REQUEST['pAccountName'])&&strim($_REQUEST['pAccountName'])!='')
		{		
			$where.=" and pAccountName like '%".strim($_REQUEST['pAccountName'])."%'";
		}
		
		if(isset($_REQUEST['pAccount'])&&strim($_REQUEST['pAccount'])!='')
			$where.=" and pAccount = '".intval($_REQUEST['pAccount'])."'";
				
		if(isset($_REQUEST['pStatus'])&&intval(strim($_REQUEST['pStatus']))>=0)
			$where.=" and pStatus = '".intval($_REQUEST['pStatus'])."'";	
		
		
		if(isset($_REQUEST['pP2PBillNo'])&&strim($_REQUEST['pP2PBillNo'])!='')
			$where.=" and pP2PBillNo = '".$_REQUEST['pP2PBillNo']."'";	
			
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

		if(strim($start_time)!="")
		{
			$where .= " and UNIX_TIMESTAMP(pMerDate) >=".to_timespan(strim($start_time));
		}
		if(strim($end_time) !="")
		{
			$where .= " and UNIX_TIMESTAMP(pMerDate) <=".  to_timespan(strim($end_time));
		}
		
		$list = M("ips_register_guarantor")
				->where($where)
				->limit($limit)->findAll();
		
		if($list)
		{
			register_shutdown_function(array(&$this, 'guarantor_export_csv'), $page+1);
			
			$list_value_old = array('id'=>'""', 'deal_name'=>'""', 'user_name'=>'""','pMerCode'=>'""', 'pMerBillNo'=>'""', 'pMerDate'=>'""','pBidNo'=>'""','pAmount'=>'""', 'pMarginAmt'=>'""', 'pProFitAmt'=>'""', 'pAcctType'=>'""','pFromIdentNo'=>'""',  'pAccountName'=>'""','pAccount'=>'""','pMemo1'=>'""', 'pMemo2'=>'""', 'pMemo3'=>'""', 'pP2PBillNo'=>'""', 'pRealFreezeAmt'=>'""','pCompenAmt'=>'""', 'pIpsTime'=>'""', 'pStatus'=>'""');
	    	if($page == 1)
	    	{
		    	$content = iconv("utf-8","gbk","编号,贷款名称,用户名,平台账号,商户开户流水号,商户日期,标的号,担保金额,担保保证金,担保收益,担保方类型,担保方证件号码,担保方账户姓名,担保方账户,备注1,备注2,备注3,担保方编号,实际冻结金额,已代偿金额,IPS处理时间,担保状态");	    		    	
		    	$content = $content . "\n";
	    	}

			
			foreach($list as $k=> $v)
			{
			
				$list_value = $list_value_old;
				
				if($v["pAcctType"] == 0)
				{
					$list_value["user_name"] = M("user")->where(" id=".$v["agency_id"])->getField("name");
				}
				else if($v["pAcctType"] == 1)
				{
					$list_value["user_name"] = M("user")->where(" id=".$v["agency_id"])->getField("user_name");
					
				}
				$list_value["user_name"] = '"' . iconv('utf-8','gbk', $list_value["user_name"]). '"';
				$list_value["id"] =  '"' . iconv('utf-8','gbk', $v["id"]). '"';
				
				$list_value["deal_name"] = M("deal")->where(" id=".$v["deal_id"])->getField("name");
				
				$list_value["pAcctType"] =  '"' . iconv('utf-8','gbk',  l("P_ACCT_TYPE_".$v["pAcctType"])). '"';
				
				$list_value["pStatus"] = '"' . iconv('utf-8','gbk', l("P_CREDITOR_STATUS_".$v["pStatus"])) . '"';
				
				$list_value["pMerCode"] = '"' . iconv('utf-8','gbk', $v["pMerCode"]) . '"';
				
				$list_value["pMerBillNo"] = '"' . iconv('utf-8','gbk', $v["pMerBillNo"]) . '"';
				
				$list_value["pBidNo"] =  '"' . iconv('utf-8','gbk', $v["pBidNo"]) . '"';
				
				$list_value["pMerDate"] = '"' . iconv('utf-8','gbk', $v["pMerDate"]) . '"';
				
				$list_value["pAmount"] = '"' . iconv('utf-8','gbk', $v["pAmount"]) . '"';

				$list_value["pAuthNo"] =  '"' . iconv('utf-8','gbk',  $v["pAuthNo"]). '"';
				
				$list_value["pMarginAmt"] =  '"' . iconv('utf-8','gbk', $v["pMarginAmt"]). '"';
				
				$list_value["pProFitAmt"] =  '"' . iconv('utf-8','gbk',  $v["pProFitAmt"]). '"';
				
				$list_value["pFromIdentNo"] =  '"' . iconv('utf-8','gbk',  $v["pFromIdentNo"]). '"';
				
				$list_value["pAccountName"] =  '"' . iconv('utf-8','gbk', $v["pAccountName"]). '"';
	
				$list_value["pAccount"] =  '"' . iconv('utf-8','gbk', $v["pAccount"]). '"';
				
				$list_value["pMemo1"] =  '"' . iconv('utf-8','gbk',  $v["pMemo1"]). '"';
				
				$list_value["pMemo2"] =  '"' . iconv('utf-8','gbk',  $v["pMemo2"]). '"';
				
				$list_value["pMemo3"] =  '"' . iconv('utf-8','gbk',  $v["pMemo3"]). '"';
				
				$list_value["pP2PBillNo"] =  '"' . iconv('utf-8','gbk',  $v["pP2PBillNo"]). '"';
				
				$list_value["pRealFreezeAmt"] =  '"' . iconv('utf-8','gbk',  $v["pRealFreezeAmt"]). '"';
				
				$list_value["pCompenAmt"] =  '"' . iconv('utf-8','gbk', $v["pCompenAmt"]). '"';
				
				$list_value["pIpsTime"] =  '"' . iconv('utf-8','gbk',  $v["pIpsTime"]). '"';
				
				
				
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
	
	public function recharge()
	{
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
			$where = " where pErrCode ='MG00000F' ";
			
			//姓名
			if(strim($_REQUEST['user_name'])!='')
			{		
				$where .= " and user_name like '%".strim($_REQUEST['user_name'])."%'";
			}
			if(strim($start_time)!="")
			{
				$where .= " and UNIX_TIMESTAMP(pTrdDate) >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and UNIX_TIMESTAMP(pTrdDate) <=".  to_timespan(strim($end_time));
			}
			$list = $GLOBALS['db']->getAll("select a.*,u.user_name from ".DB_PREFIX."ips_do_dp_trade a left join ".DB_PREFIX."user u on a.user_id = u.id ".$where.$limit);
			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."ips_do_dp_trade ".$where);
			
			foreach($list as $k => $v)
			{
				$list[$k]["user_type"] = l('P_USER_TYPE_'.$v["user_type"]);
				$list[$k]["pAcctType"] = l('P_ACCT_TYPE_'.$v["pAcctType"]);
				$list[$k]["pChannelType"] = l('P_CHANNEL_TYPE_'.$v["pChannelType"]);
				$list[$k]["pIpsFeeType"] = l('P_IPS_FEE_TYPE_'.$v["pIpsFeeType"]);
			}
		}
		//易宝
		elseif(strtolower($className) == "yeepay")
		{
			$where = " where is_callback = 1 and yr.code = 1 ";
			
			//姓名
			if(strim($_REQUEST['user_name'])!='')
			{		
				$where .= " and u.user_name like '%".strim($_REQUEST['user_name'])."%'";
			}
			if(strim($start_time)!="")
			{
				$where .= " and yr.create_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and yr.create_time <=".  to_timespan(strim($end_time));
			}
			
			$list = $GLOBALS['db']->getAll("select  requestNo as pMerBillNo,idno as pIdentNo,u.user_name,yr.id,u.user_type,
			real_name as pRealName, FROM_UNIXTIME( yr.create_time, '%Y-%m-%d' ) as pTrdDate,
			amount as pTrdAmt ,'2' as pChannelType ,case when feeMode ='PLATFORM' then 1 else 2 end as pIpsFeeType ,fee as pMerFee from ".DB_PREFIX."yeepay_recharge yr left join ".DB_PREFIX."user u on yr.platformUserNo = u.id ".$where." order by yr.id desc ".$limit);
			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."yeepay_recharge yr left join ".DB_PREFIX.".user u on yr.platformUserNo = u.id ".$where);
			foreach($list as $k => $v)
			{
				$list[$k]["user_type"] = l('P_USER_TYPE_'.$v["user_type"]);
				$list[$k]["pAcctType"] = l('P_ACCT_TYPE_'.$v["pAcctType"]);
				$list[$k]["pChannelType"] = l('P_CHANNEL_TYPE_'.$v["pChannelType"]);
				$list[$k]["pIpsFeeType"] = l('P_IPS_FEE_TYPE_'.$v["pIpsFeeType"]);
			}
		}
		//宝付
		elseif(strtolower($className) == "baofoo")
		{
			$where = " where is_callback = 1 and yr.code = 'CSD000' ";
			
			if(strim($start_time)!="")
			{
				$where .= " and yr.create_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and yr.create_time <=".  to_timespan(strim($end_time));
			}
			
			$list = $GLOBALS['db']->getAll("select order_id as pMerBillNo,idno as pIdentNo,u.user_name,yr.id,u.user_type,
			real_name as pRealName, FROM_UNIXTIME( yr.create_time, '%Y-%m-%d' ) as pTrdDate,
			amount as pTrdAmt,'2' as pChannelType,fee_taken_on as pIpsFeeType ,mer_fee as pMerFee from ".DB_PREFIX."baofoo_recharge yr left join ".DB_PREFIX."user u on yr.user_id = u.id ".$where." order by yr.id desc ".$limit);
			
			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."baofoo_recharge yr left join ".DB_PREFIX."user u on yr.user_id = u.id ".$where);
			foreach($list as $k => $v)
			{
				$list[$k]["user_type"] = l('P_USER_TYPE_'.$v["user_type"]);
				$list[$k]["pAcctType"] = l('P_ACCT_TYPE_'.$v["pAcctType"]);
				$list[$k]["pChannelType"] = l('P_CHANNEL_TYPE_'.$v["pChannelType"]);
				$list[$k]["pIpsFeeType"] = l('P_IPS_FEE_TYPE_'.$v["pIpsFeeType"]);
			}		
		}

		$page = new Page($list_count,app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$this->assign('page',$p);
		
		$this->assign("list",$list);
		$this->assign("start_time",$start_time);
		$this->assign("end_time",$end_time);
		
		$this->display ();
	}
	
	public function recharge_export_csv($page = 1)
	{
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
		

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
		
		if($limit)
		{
			$limit = " limit ".$limit;
		}
		
		$className = getCollName();
		//环迅
		if(strtolower($className) == "ips")
		{
			$where = " where pErrCode ='MG00000F' ";
			
			//姓名
			if(strim($_REQUEST['user_name'])!='')
			{		
				$where .= " and user_name like '%".strim($_REQUEST['user_name'])."%'";
			}
			if(strim($start_time)!="")
			{
				$where .= " and UNIX_TIMESTAMP(pTrdDate) >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and UNIX_TIMESTAMP(pTrdDate) <=".  to_timespan(strim($end_time));
			}
			$list = $GLOBALS['db']->getAll("select a.*,u.user_name from ".DB_PREFIX."ips_do_dp_trade a left join ".DB_PREFIX."user u on a.user_id = u.id ".$where.$limit);
			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."ips_do_dp_trade ".$where);
			
			foreach($list as $k => $v)
			{
				$list[$k]["user_type"] = l('P_USER_TYPE_'.$v["user_type"]);
				$list[$k]["pAcctType"] = l('P_ACCT_TYPE_'.$v["pAcctType"]);
				$list[$k]["pChannelType"] = l('P_CHANNEL_TYPE_'.$v["pChannelType"]);
				$list[$k]["pIpsFeeType"] = l('P_IPS_FEE_TYPE_'.$v["pIpsFeeType"]);
			}
		}
		//易宝
		elseif(strtolower($className) == "yeepay")
		{
			$where = " where is_callback = 1 and yr.code = 1 ";
			
			//姓名
			if(strim($_REQUEST['user_name'])!='')
			{		
				$where .= " and u.user_name like '%".strim($_REQUEST['user_name'])."%'";
			}
			if(strim($start_time)!="")
			{
				$where .= " and yr.create_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and yr.create_time <=".  to_timespan(strim($end_time));
			}
			
			$list = $GLOBALS['db']->getAll("select  requestNo as pMerBillNo,idno as pIdentNo,u.user_name,yr.id,u.user_type,
			real_name as pRealName, FROM_UNIXTIME( yr.create_time, '%Y-%m-%d' ) as pTrdDate,
			amount as pTrdAmt ,'2' as pChannelType ,case when feeMode ='PLATFORM' then 1 else 2 end as pIpsFeeType ,fee as pMerFee from ".DB_PREFIX."yeepay_recharge yr left join ".DB_PREFIX."user u on yr.platformUserNo = u.id ".$where.$limit);
			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."yeepay_recharge yr left join ".DB_PREFIX.".user u on yr.platformUserNo = u.id ".$where);
			foreach($list as $k => $v)
			{
				$list[$k]["user_type"] = l('P_USER_TYPE_'.$v["user_type"]);
				$list[$k]["pAcctType"] = l('P_ACCT_TYPE_'.$v["pAcctType"]);
				$list[$k]["pChannelType"] = l('P_CHANNEL_TYPE_'.$v["pChannelType"]);
				$list[$k]["pIpsFeeType"] = l('P_IPS_FEE_TYPE_'.$v["pIpsFeeType"]);
			}
		}
		//宝付
		elseif(strtolower($className) == "baofoo")
		{
			$where = " where is_callback = 1 and yr.code = 'CSD000' ";
			
			if(strim($start_time)!="")
			{
				$where .= " and yr.create_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and yr.create_time <=".  to_timespan(strim($end_time));
			}
			
			$list = $GLOBALS['db']->getAll("select order_id as pMerBillNo,idno as pIdentNo,u.user_name,yr.id,u.user_type,
			real_name as pRealName, FROM_UNIXTIME( yr.create_time, '%Y-%m-%d' ) as pTrdDate,
			amount as pTrdAmt,'2' as pChannelType,fee_taken_on as pIpsFeeType ,mer_fee as pMerFee from ".DB_PREFIX."baofoo_recharge yr left join ".DB_PREFIX."user u on yr.user_id = u.id ".$where.$limit);
			
			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."baofoo_recharge yr left join ".DB_PREFIX."user u on yr.user_id = u.id ".$where);
			foreach($list as $k => $v)
			{
				$list[$k]["user_type"] = l('P_USER_TYPE_'.$v["user_type"]);
				$list[$k]["pAcctType"] = l('P_ACCT_TYPE_'.$v["pAcctType"]);
				$list[$k]["pChannelType"] = l('P_CHANNEL_TYPE_'.$v["pChannelType"]);
				$list[$k]["pIpsFeeType"] = l('P_IPS_FEE_TYPE_'.$v["pIpsFeeType"]);
			}		
		}
		
		if($list)
		{
			register_shutdown_function(array(&$this, 'recharge_export_csv'), $page+1);
			
			$list_value_old = array('id'=>'""',  'user_name'=>'""','user_type'=>'""','pIdentNo'=>'""','pRealName'=>'""','pTrdDate'=>'""','pTrdAmt'=>'""','pChannelType'=>'""','pIpsFeeType'=>'""');
	    	if($page == 1)
	    	{
		    	$content = iconv("utf-8","gbk","编号,用户名,用户类型,证件号码,姓名,充值日期,充值金额,充值渠道种类,手续费支付方"); 		    	
		    	$content = $content . "\n";
	    	}

			
			foreach($list as $k=> $v)
			{
				$list_value = $list_value_old;
				
				$list_value["user_name"] = '"' . iconv('utf-8','gbk', $v["user_name"]). '"';
				
				$list_value["id"] =  '"' . iconv('utf-8','gbk', $v["id"]). '"';
				
				$list_value["user_type"] = '"' . iconv('utf-8','gbk',  $v["user_type"]). '"';

				$list_value["pIdentNo"] = '"' . iconv('utf-8','gbk', $v["pIdentNo"]) . '"';
				
				$list_value["pRealName"] = '"' . iconv('utf-8','gbk', $v["pRealName"]) . '"';

				$list_value["pTrdDate"] =  '"' . iconv('utf-8','gbk', $v["pTrdDate"]) . '"';
				
				$list_value["pTrdAmt"] = '"' . iconv('utf-8','gbk', $v["pTrdAmt"]) . '"';
				
				$list_value["pChannelType"] = '"' . iconv('utf-8','gbk', $v["pChannelType"]) . '"';

				$list_value["pIpsFeeType"] = '"' . iconv('utf-8','gbk', $v["pIpsFeeType"]) . '"';
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
	public function transfer()
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
			$where = " where pErrCode ='MG00000F' ";
			
			if(isset($_REQUEST['user_name'])&& intval(strim($_REQUEST['user_name']))>=0)
			{		
				$where.=" and u.user_name like '%".strim($_REQUEST['user_name'])."%'";
			}
			if(strim($start_time)!="")
			{
				$where .= " and UNIX_TIMESTAMP(pDwDate) >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and UNIX_TIMESTAMP(pDwDate) <=".  to_timespan(strim($end_time));
			}
			$list = $GLOBALS['db']->getAll("select a.*,u.user_name from ".DB_PREFIX."ips_do_dw_trade a left join ".DB_PREFIX."user u on a.user_id = u.id ".$where." order by a.id desc ".$limit);
			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."ips_do_dw_trade ".$where);
			foreach($list as $k=>$v)
			{
				$list[$k]["user_type"] = l('P_USER_TYPE_'.$v["user_type"]);
				$list[$k]["pAcctType"] = l('P_ACCT_TYPE_'.$v["pAcctType"]);
				$list[$k]["pOutType"] = l('P_OUT_TYPE_'.$v["pOutType"]);
				$list[$k]["pIpsFeeType"] =l('P_IPS_FEE_TYPE_'.$v["pIpsFeeType"]);
			}
		
		}
		//易宝
		elseif(strtolower($className) == "yeepay")
		{
			$where = " where yw.code ='1' and yw.is_callback = 1 ";
			
			if(isset($_REQUEST['user_name'])&& intval(strim($_REQUEST['user_name']))>=0)
			{		
				$where.=" and u.user_name like '%".strim($_REQUEST['user_name'])."%'";
			}
			if(strim($start_time)!="")
			{
				$where .= " and yw.create_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and yw.create_time <=".  to_timespan(strim($end_time));
			}
			$y_sql = "select yw.id, requestNo  as pIpsBillNo,u.real_name as pRealName,u.user_name,u.user_type,1 as pOutType,
			FROM_UNIXTIME(yw.create_time ,'%Y-%m-%d')as pDwDate,amount as pTrdAmt,fee as pMerFee , 
			u.idno as pIdentNo,
			case when feeMode ='PLATFORM' then 1 else 2 end as pIpsFeeType
			 from ".DB_PREFIX."yeepay_withdraw yw left join ".DB_PREFIX."user u on yw.platformUserNo = u.id ".$where." order by yw.id desc ".$limit;
			$list = $GLOBALS['db']->getAll($y_sql);
			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."yeepay_withdraw yw left join ".DB_PREFIX."user u on yw.platformUserNo = u.id ".$where);
			foreach($list as $k=>$v)
			{
				$list[$k]["user_type"] = l('P_USER_TYPE_'.$v["user_type"]);
				$list[$k]["pAcctType"] = l('P_ACCT_TYPE_'.$v["pAcctType"]);
				$list[$k]["pOutType"] = l('P_OUT_TYPE_'.$v["pOutType"]);
				$list[$k]["pIpsFeeType"] =l('P_IPS_FEE_TYPE_'.$v["pIpsFeeType"]);
			}
		}
		//宝付
		elseif(strtolower($className) == "baofoo")
		{
			$where = " where yw.code ='CSD000' and yw.is_callback = 1 ";
			
			if(isset($_REQUEST['user_name'])&& intval(strim($_REQUEST['user_name']))>=0)
			{		
				$where.=" and u.user_name like '%".strim($_REQUEST['user_name'])."%'";
			}
			if(strim($start_time)!="")
			{
				$where .= " and yw.create_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and yw.create_time <=".  to_timespan(strim($end_time));
			}
			$y_sql = "select yw.id, order_id as pIpsBillNo,u.real_name as pRealName,u.user_name,u.user_type,1 as pOutType,
			FROM_UNIXTIME(yw.create_time ,'%Y-%m-%d')as pDwDate,amount as pTrdAmt,fee as pMerFee , 
			u.idno as pIdentNo,
			fee_taken_on as pIpsFeeType
			 from ".DB_PREFIX."baofoo_fo_charge yw left join ".DB_PREFIX."user u on yw.user_id = u.id ".$where." order by yw.id desc ".$limit;
			$list = $GLOBALS['db']->getAll($y_sql);
			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."baofoo_fo_charge yw left join ".DB_PREFIX."user u on yw.user_id = u.id ".$where);
			foreach($list as $k=>$v)
			{
				$list[$k]["user_type"] = l('P_USER_TYPE_'.$v["user_type"]);
				$list[$k]["pAcctType"] = l('P_ACCT_TYPE_'.$v["pAcctType"]);
				$list[$k]["pOutType"] = l('P_OUT_TYPE_'.$v["pOutType"]);
				$list[$k]["pIpsFeeType"] =l('P_IPS_FEE_TYPE_'.$v["pIpsFeeType"]);
			}
		}
		
		$page = new Page($list_count,app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$this->assign('page',$p);
		
		$this->assign("list",$list);
		$this->assign("start_time",$start_time);
		$this->assign("end_time",$end_time);
		$this->display ();
	}
	
	public function transfer_export_csv($page = 1)
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
			$where = " where pErrCode ='MG00000F' ";
			
			if(isset($_REQUEST['user_name'])&& intval(strim($_REQUEST['user_name']))>=0)
			{		
				$where.=" and u.user_name like '%".strim($_REQUEST['user_name'])."%'";
			}
			if(strim($start_time)!="")
			{
				$where .= " and UNIX_TIMESTAMP(pDwDate) >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and UNIX_TIMESTAMP(pDwDate) <=".  to_timespan(strim($end_time));
			}
			$list = $GLOBALS['db']->getAll("select a.*,u.user_name from ".DB_PREFIX."ips_do_dw_trade a left join ".DB_PREFIX."user u on a.user_id = u.id ".$where.$limit);
			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."ips_do_dw_trade ".$where);
			foreach($list as $k=>$v)
			{
				$list[$k]["user_type"] = l('P_USER_TYPE_'.$v["user_type"]);
				$list[$k]["pAcctType"] = l('P_ACCT_TYPE_'.$v["pAcctType"]);
				$list[$k]["pOutType"] = l('P_OUT_TYPE_'.$v["pOutType"]);
				$list[$k]["pIpsFeeType"] =l('P_IPS_FEE_TYPE_'.$v["pIpsFeeType"]);
			}
		
		}
		//易宝
		elseif(strtolower($className) == "yeepay")
		{
			$where = " where yw.code ='1' and yw.is_callback = 1 ";
			
			if(isset($_REQUEST['user_name'])&& intval(strim($_REQUEST['user_name']))>=0)
			{		
				$where.=" and u.user_name like '%".strim($_REQUEST['user_name'])."%'";
			}
			if(strim($start_time)!="")
			{
				$where .= " and yw.create_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and yw.create_time <=".  to_timespan(strim($end_time));
			}
			$y_sql = "select yw.id, requestNo  as pIpsBillNo,u.real_name as pRealName,u.user_name,u.user_type,1 as pOutType,
			FROM_UNIXTIME(yw.create_time ,'%Y-%m-%d')as pDwDate,amount as pTrdAmt,fee as pMerFee , 
			u.idno as pIdentNo,
			case when feeMode ='PLATFORM' then 1 else 2 end as pIpsFeeType
			 from ".DB_PREFIX."yeepay_withdraw yw left join ".DB_PREFIX."user u on yw.platformUserNo = u.id ".$where.$limit;
			$list = $GLOBALS['db']->getAll($y_sql);
			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."yeepay_withdraw yw left join ".DB_PREFIX."user u on yw.platformUserNo = u.id ".$where);
			foreach($list as $k=>$v)
			{
				$list[$k]["user_type"] = l('P_USER_TYPE_'.$v["user_type"]);
				$list[$k]["pAcctType"] = l('P_ACCT_TYPE_'.$v["pAcctType"]);
				$list[$k]["pOutType"] = l('P_OUT_TYPE_'.$v["pOutType"]);
				$list[$k]["pIpsFeeType"] =l('P_IPS_FEE_TYPE_'.$v["pIpsFeeType"]);
			}
		}
		//宝付
		elseif(strtolower($className) == "baofoo")
		{
			$where = " where yw.code ='CSD000' and yw.is_callback = 1 ";
			
			if(isset($_REQUEST['user_name'])&& intval(strim($_REQUEST['user_name']))>=0)
			{		
				$where.=" and u.user_name like '%".strim($_REQUEST['user_name'])."%'";
			}
			if(strim($start_time)!="")
			{
				$where .= " and yw.create_time >=".to_timespan(strim($start_time));
			}
			if(strim($end_time) !="")
			{
				$where .= " and yw.create_time <=".  to_timespan(strim($end_time));
			}
			$y_sql = "select yw.id, order_id as pIpsBillNo,u.real_name as pRealName,u.user_name,u.user_type,1 as pOutType,
			FROM_UNIXTIME(yw.create_time ,'%Y-%m-%d')as pDwDate,amount as pTrdAmt,fee as pMerFee , 
			u.idno as pIdentNo,
			fee_taken_on as pIpsFeeType
			 from ".DB_PREFIX."baofoo_fo_charge yw left join ".DB_PREFIX."user u on yw.user_id = u.id ".$where.$limit;
			$list = $GLOBALS['db']->getAll($y_sql);
			$list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."baofoo_fo_charge yw left join ".DB_PREFIX."user u on yw.user_id = u.id ".$where);
			foreach($list as $k=>$v)
			{
				$list[$k]["user_type"] = l('P_USER_TYPE_'.$v["user_type"]);
				$list[$k]["pAcctType"] = l('P_ACCT_TYPE_'.$v["pAcctType"]);
				$list[$k]["pOutType"] = l('P_OUT_TYPE_'.$v["pOutType"]);
				$list[$k]["pIpsFeeType"] =l('P_IPS_FEE_TYPE_'.$v["pIpsFeeType"]);
			}
		}
		
		if($list)
		{
			register_shutdown_function(array(&$this, 'transfer_export_csv'), $page+1);
			
			$list_value_old = array('id'=>'""',  'user_name'=>'""','user_type'=>'""','pOutType'=>'""','pIdentNo'=>'""',  'pRealName'=>'""','pDwDate'=>'""', 'pTrdAmt'=>'""', 'pMerFee'=> '""','pIpsFeeType'=>'""');
	    	if($page == 1)
	    	{

		    	$content = iconv("utf-8","gbk","编号,用户名,用户类型,提现模式,证件号码,姓名,提现日期,交易金额,平台手续费,手续费支付方");	    		    	
		    	$content = $content . "\n";
	    	}

			foreach($list as $k=> $v)
			{
				$list_value = $list_value_old;
				
				$list_value["user_name"] = '"' . iconv('utf-8','gbk', $v["user_name"]). '"';
				
				$list_value["id"] =  '"' . iconv('utf-8','gbk', $v["id"]). '"';
				
				$list_value["user_type"] = '"' . iconv('utf-8','gbk', $v["user_type"]). '"';
				
				$list_value["pOutType"] =  '"' . iconv('utf-8','gbk', $v["pOutType"]). '"';
				
				$list_value["pIdentNo"] = '"' . iconv('utf-8','gbk', $v["pIdentNo"]) . '"';
				
				$list_value["pRealName"] = '"' . iconv('utf-8','gbk', $v["pRealName"]) . '"';
				
				$list_value["pDwDate"] =  '"' . iconv('utf-8','gbk', $v["pDwDate"]) . '"';
				
				$list_value["pMerFee"] = '"' . iconv('utf-8','gbk', $v["pMerFee"]) . '"';
				
				$list_value["pTrdAmt"] = '"' . iconv('utf-8','gbk', $v["pTrdAmt"]) . '"';
				
				$list_value["pIpsFeeType"] = '"' . iconv('utf-8','gbk', $v["pIpsFeeType"]) . '"';

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