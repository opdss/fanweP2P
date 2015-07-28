<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/deal.php';
require APP_ROOT_PATH.'app/Lib/common.php';
class IpsTransferAction extends CommonAction{
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
		
			$sql = "select dlt.id as mid,t.*,d.`name`,u.user_name,tu.user_name as t_user_name,(dlt.load_money - dlt.transfer_amount) as leave_money from ".DB_PREFIX."ips_transfer as t
			LEFT JOIN ".DB_PREFIX."deal as d on d.id = t.deal_id
			LEFT JOIN ".DB_PREFIX."deal_load_transfer as dlt on dlt.id = t.ref_data
			LEFT JOIN ".DB_PREFIX."user as u on u.id = d.user_id
			left join ".DB_PREFIX."user tu on tu.id = dlt.t_user_id
			where t.pTransferType = 4 ";
					
					$count_sql = "select count(*) from ".DB_PREFIX."ips_transfer as t
			LEFT JOIN ".DB_PREFIX."deal as d on d.id = t.deal_id
			LEFT JOIN ".DB_PREFIX."deal_load_transfer as dlt on dlt.id = t.ref_data
			LEFT JOIN ".DB_PREFIX."user as u on u.id = d.user_id
			left join ".DB_PREFIX."user tu on tu.id = dlt.t_user_id
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
			
			$condition = " where bizType = 'CREDIT_ASSIGNMENT' and is_complete_transaction = 1 and t.code ='1' and is_callback = 1  ";
			
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
		
			$sql = "select dlt.id as mid,t.*,d.`name`,u.user_name,tu.user_name as t_user_name,dlt.ips_status,dlt.t_user_id ,
			requestNo as pIpsBillNo,tenderOrderNo as pBidNo,FROM_UNIXTIME(t.create_time ,'%Y-%m-%d') as pDate,
			FROM_UNIXTIME(t.update_time ,'%Y-%m-%d') as pIpsTime
			from ".DB_PREFIX."yeepay_cp_transaction as t
			LEFT JOIN ".DB_PREFIX."deal as d on d.id = t.tenderOrderNo
			LEFT JOIN ".DB_PREFIX."deal_load_transfer as dlt on dlt.id = t.transfer_id
			LEFT JOIN ".DB_PREFIX."user as u on u.id = dlt.user_id
			left join ".DB_PREFIX."user tu on tu.id = dlt.t_user_id";
			
			$count_sql = "select count(*) 
			from ".DB_PREFIX."yeepay_cp_transaction as t
			LEFT JOIN ".DB_PREFIX."deal as d on d.id = t.tenderOrderNo
			LEFT JOIN ".DB_PREFIX."deal_load_transfer as dlt on dlt.id = t.transfer_id
			LEFT JOIN ".DB_PREFIX."user as u on u.id = dlt.user_id
			left join ".DB_PREFIX."user tu on tu.id = dlt.t_user_id";

			//print_r($sql.$condition);die;
			$list = $GLOBALS['db']->getAll( $sql.$condition." order by t.id desc ".$limit);
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
			//print_r($count);die;
			//$name=$this->getActionName();
		}
		//宝付
		elseif(strtolower($className) == "baofoo")
		{
			$condition = " where ref_type = '1' and t.code ='CSD000' ";
			
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
		
			$sql = "select dlt.id as mid, t.*,d.`name`,u.user_name,tu.user_name as t_user_name,dlt.ips_status,dlt.t_user_id ,
			order_id as pIpsBillNo,dlt.deal_id as pBidNo,FROM_UNIXTIME(t.req_time/1000 ,'%Y-%m-%d') as pDate,
			FROM_UNIXTIME(t.req_time/1000 ,'%Y-%m-%d') as pIpsTime
			from ".DB_PREFIX."baofoo_acct_trans as t
			LEFT JOIN ".DB_PREFIX."deal_load_transfer as dlt on dlt.id = t.ref_id
			LEFT JOIN ".DB_PREFIX."deal as d on d.id = dlt.deal_id
			LEFT JOIN ".DB_PREFIX."user as u on u.id = dlt.user_id
			left join ".DB_PREFIX."user tu on tu.id = dlt.t_user_id";
			
			$count_sql = "select count(*) 
			from ".DB_PREFIX."baofoo_acct_trans as t
			LEFT JOIN ".DB_PREFIX."deal_load_transfer as dlt on dlt.id = t.ref_id
			LEFT JOIN ".DB_PREFIX."deal as d on d.id = dlt.deal_id
			LEFT JOIN ".DB_PREFIX."user as u on u.id = dlt.user_id
			left join ".DB_PREFIX."user tu on tu.id = dlt.t_user_id";
			//print_r($sql.$condition);die;
			//print_r($sql.$condition);die;
			$list = $GLOBALS['db']->getAll( $sql.$condition." order by t.id desc ".$limit);
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
		}
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
		//$this->assign('user_id',$GLOBALS["user_info"]["id"]);
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
		
			$sql = "select dlt.id as mid,t.*,d.`name`,u.user_name,tu.user_name as t_user_name,(dlt.load_money - dlt.transfer_amount) as leave_money from ".DB_PREFIX."ips_transfer as t
			LEFT JOIN ".DB_PREFIX."deal as d on d.id = t.deal_id
			LEFT JOIN ".DB_PREFIX."deal_load_transfer as dlt on dlt.id = t.ref_data
			LEFT JOIN ".DB_PREFIX."user as u on u.id = d.user_id
			left join ".DB_PREFIX."user tu on tu.id = dlt.t_user_id
			where t.pTransferType = 4 ";
					
					$count_sql = "select count(*) from ".DB_PREFIX."ips_transfer as t
			LEFT JOIN ".DB_PREFIX."deal as d on d.id = t.deal_id
			LEFT JOIN ".DB_PREFIX."deal_load_transfer as dlt on dlt.id = t.ref_data
			LEFT JOIN ".DB_PREFIX."user as u on u.id = d.user_id
			left join ".DB_PREFIX."user tu on tu.id = dlt.t_user_id
			where t.pTransferType = 4 ";
	
			//print_r($sql.$condition);die;
			$list = $GLOBALS['db']->getAll( $sql.$condition.$limit);
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
			//print_r($count);die;
			//$name=$this->getActionName();
		}
		//易宝
		elseif(strtolower($className) == "yeepay")
		{
			
			$condition = " where bizType = 'CREDIT_ASSIGNMENT' and is_complete_transaction = 1 and t.code ='1' and is_callback = 1  ";
			
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
		
			$sql = "select dlt.id as mid,t.*,d.`name`,u.user_name,tu.user_name as t_user_name,dlt.ips_status,dlt.t_user_id ,
			requestNo as pIpsBillNo,tenderOrderNo as pBidNo,FROM_UNIXTIME(t.create_time ,'%Y-%m-%d') as pDate,
			FROM_UNIXTIME(t.update_time ,'%Y-%m-%d') as pIpsTime
			from ".DB_PREFIX."yeepay_cp_transaction as t
			LEFT JOIN ".DB_PREFIX."deal as d on d.id = t.tenderOrderNo
			LEFT JOIN ".DB_PREFIX."deal_load_transfer as dlt on dlt.id = t.transfer_id
			LEFT JOIN ".DB_PREFIX."user as u on u.id = d.user_id
			left join ".DB_PREFIX."user tu on tu.id = dlt.t_user_id";
			
			$count_sql = "select count(*) 
			from ".DB_PREFIX."yeepay_cp_transaction as t
			LEFT JOIN ".DB_PREFIX."deal as d on d.id = t.tenderOrderNo
			LEFT JOIN ".DB_PREFIX."deal_load_transfer as dlt on dlt.id = t.transfer_id
			LEFT JOIN ".DB_PREFIX."user as u on u.id = d.user_id
			left join ".DB_PREFIX."user tu on tu.id = dlt.t_user_id";
	
			//print_r($sql.$condition);die;
			$list = $GLOBALS['db']->getAll( $sql.$condition.$limit);
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
			//print_r($count);die;
			//$name=$this->getActionName();
		}
		//宝付
		elseif(strtolower($className) == "baofoo")
		{
			$condition = " where ref_type = '1' and t.code ='CSD000' ";
			
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
		
			$sql = "select dlt.id as mid, t.*,d.`name`,u.user_name,tu.user_name as t_user_name,dlt.ips_status,dlt.t_user_id ,
			order_id as pIpsBillNo,dlt.deal_id as pBidNo,FROM_UNIXTIME(t.req_time /1000,'%Y-%m-%d') as pDate,
			FROM_UNIXTIME(t.req_time/1000 ,'%Y-%m-%d') as pIpsTime
			from ".DB_PREFIX."baofoo_acct_trans as t
			LEFT JOIN ".DB_PREFIX."deal_load_transfer as dlt on dlt.id = t.ref_id
			LEFT JOIN ".DB_PREFIX."deal as d on d.id = dlt.deal_id
			LEFT JOIN ".DB_PREFIX."user as u on u.id = d.user_id
			left join ".DB_PREFIX."user tu on tu.id = dlt.t_user_id";
			
			$count_sql = "select count(*) 
			from ".DB_PREFIX."baofoo_acct_trans as t
			LEFT JOIN ".DB_PREFIX."deal_load_transfer as dlt on dlt.id = t.ref_id
			LEFT JOIN ".DB_PREFIX."deal as d on d.id = dlt.deal_id
			LEFT JOIN ".DB_PREFIX."user as u on u.id = d.user_id
			left join ".DB_PREFIX."user tu on tu.id = dlt.t_user_id";
			//print_r($sql.$condition);die;
			//print_r($sql.$condition);die;
			$list = $GLOBALS['db']->getAll( $sql.$condition.$limit);
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
		}
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
		
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv'), $page+1);
         
			$list_value_old = array(
				'id'=>'""', 
				'pBidNo'=>'""', 
				'name'=>'""', 
				'user_name'=>'""',
				'left_benjin_format' => '""',
				'left_lixi_format'=>'""',
				'transfer_amount_format'=>'""', 
				'transfer_income_format'=>'""',
				't_user_name'=>'""',
				'pDate'=>'""', 
			);
			
	    	if($page == 1)
	    	{
		    	$content = iconv("utf-8","gbk","编号,标号,贷款名称,转让者,剩余本金,剩余利息,转让金额,受让收益,承接人,承接时间");	    		    	
		    	$content = $content . "\n";
	    	}

			foreach($list as $k=> $v)
			{
				$list_value = $list_value_old;
				
				$list_value["id"] = '"' . iconv('utf-8','gbk', $v['id']) . '"';
				$list_value["name"] = '"' . iconv('utf-8','gbk', $v['name']) . '"';				
				$list_value["pBidNo"] =  '"' . iconv('utf-8','gbk', $v["pBidNo"]). '"';
				$list_value["pDate"] =  '"' . iconv('utf-8','gbk', $v["pDate"]). '"';
				$list_value["t_user_name"] =  '"' . iconv('utf-8','gbk', $v["t_user_name"]). '"';
				$list_value["left_benjin_format"] =  '"' . iconv('utf-8','gbk', $v["left_benjin_format"]). '"';
				$list_value["left_lixi_format"] =  '"' . iconv('utf-8','gbk', $v["left_lixi_format"]). '"';
				$list_value["transfer_amount_format"] =  '"' . iconv('utf-8','gbk', $v["transfer_amount_format"]). '"';
				$list_value["transfer_income_format"] =  '"' . iconv('utf-8','gbk', $v["transfer_income_format"]). '"';
				$list_value["user_name"] =  '"' . iconv('utf-8','gbk', $v["user_name"]). '"';

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
			
			$sql = "select dlr.*,d.name as deal_name,u.user_name,tu.user_name as t_user_name from ".DB_PREFIX."deal_load_repay as dlr LEFT JOIN ".DB_PREFIX."user as u on u.id = dlr.user_id left join ".DB_PREFIX."user as tu on tu.id = dlr.t_user_id left join ".DB_PREFIX."deal as d on dlr.deal_id = d.id where dlr.load_id =".intval($load_info['load_id']) . " and dlr.user_id =".intval($load_info['user_id']) . " and dlr.deal_id = ".$load_info["deal_id"];
		
			$count_sql = "select count(*) from ".DB_PREFIX."deal_load_repay as dlr LEFT JOIN ".DB_PREFIX."user as u on u.id = dlr.user_id 
			left join ".DB_PREFIX."user as tu on tu.id = dlr.t_user_id 
			left join ".DB_PREFIX."deal as d on dlr.deal_id = d.id where dlr.load_id =".intval($load_info['load_id']) . " and dlr.user_id =".intval($load_info['user_id']) . " and dlr.deal_id = ".$load_info["deal_id"];
	
			
			$list = $GLOBALS['db']->getAll( $sql.$condition." order by dlr.id desc ".$limit);
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

			$list = $GLOBALS['db']->getAll( $sql.$condition." order by dlr.id desc ".$limit);
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

			$list = $GLOBALS['db']->getAll( $sql.$condition." order by dlr.id desc ".$limit);
			//取得满足条件的记录数
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
			
		}
		foreach($list as $k =>$v)
		{
			$list[$k]["ll_key"] = $list[$k]["l_key"] +1;
			$list[$k]["uu_key"] = $list[$k]["u_key"] +1;
			if($v["has_repay"])
			{
				$list[$k]["status"] = l("REPAY_STATUS_".$v["status"]);;
			}
			else
			{
				$list[$k]["status"] = "";
			}
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
			
			$sql = "select dlr.*,d.name as deal_name,u.user_name,tu.user_name as t_user_name from ".DB_PREFIX."deal_load_repay as dlr LEFT JOIN ".DB_PREFIX."user as u on u.id = dlr.user_id left join ".DB_PREFIX."user as tu on tu.id = dlr.t_user_id left join ".DB_PREFIX."deal as d on dlr.deal_id = d.id where dlr.load_id =".intval($load_info['load_id']) . " and dlr.user_id =".intval($load_info['user_id']) . " and dlr.deal_id = ".$load_info["deal_id"];
		
			$count_sql = "select count(*) from ".DB_PREFIX."deal_load_repay as dlr LEFT JOIN ".DB_PREFIX."user as u on u.id = dlr.user_id 
			left join ".DB_PREFIX."user as tu on tu.id = dlr.t_user_id 
			left join ".DB_PREFIX."deal as d on dlr.deal_id = d.id where dlr.load_id =".intval($load_info['load_id']) . " and dlr.user_id =".intval($load_info['user_id']) . " and dlr.deal_id = ".$load_info["deal_id"];
	
			
			$list = $GLOBALS['db']->getAll( $sql.$condition." order by dlr.id desc ".$limit);
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

			$list = $GLOBALS['db']->getAll( $sql.$condition." order by dlr.id desc ".$limit);
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

			$list = $GLOBALS['db']->getAll( $sql.$condition." order by dlr.id desc ".$limit);
			//取得满足条件的记录数
			$list_count = $GLOBALS['db']->getOne($count_sql.$condition);
			
		}
		foreach($list as $k =>$v)
		{
			$list[$k]["ll_key"] = $list[$k]["l_key"] +1;
			$list[$k]["uu_key"] = $list[$k]["u_key"] +1;
		}
		
		if($list)
		{
			register_shutdown_function(array(&$this, 'relation_export_csv'), $page+1);
			
			$list_value = array('id'=>'""', 'deal_name'=>'""', 'self_money'=>'""','repay_money'=>'""', 'manage_money'=>'""','manage_interest_money'=>'""', 'impose_money'=>'""','repay_time'=>'""','true_repay_time'=>'""','status'=>'""', 'is_site_repay'=>'""', 'l_key'=>'""', 'u_key'=>'""','has_repay'=>'""',  'repay_manage_money'=>'""','repay_manage_impose_money'=>'""','user_name'=>'""', 't_user_name'=>'""');
	    	if($page == 1)
	    	{
		    	$content = iconv("utf-8","gbk","编号,借款名称,本金,还款金额,管理费,利息管理费,罚息,还款日,实际还款时间,还款状态,付款方式,还款期号,还款顺序,订单状态,从借款者均摊下来的管理费,接入者均摊下来的逾期管理费,投标人,承接人");	    		    	
		    	$content = $content . "\n";
	    	}
			
			foreach($list as $k=> $v)
			{
				
				$list_value["id"] =  '"' . iconv('utf-8','gbk', $v["id"]). '"';
				
				$list_value["deal_name"] =  '"' . iconv('utf-8','gbk',  M("deal")->where(" id=".strim($v['deal_id']))->getField("name")). '"';
				
				$list_value["self_money"] =  '"' . iconv('utf-8','gbk', $v["self_money"]). '"';
				
				$list_value["repay_money"] =  '"' . iconv('utf-8','gbk', $v["repay_money"]). '"';
				
				$list_value["manage_money"] =  '"' . iconv('utf-8','gbk', $v["manage_money"]). '"';
				
				$list_value["impose_money"] =  '"' . iconv('utf-8','gbk', $v["impose_money"]). '"';
				
				$list_value["repay_time"] =  '"' . iconv('utf-8','gbk', $v["repay_time"]). '"';
				
				$list_value["true_repay_time"] =  '"' . iconv('utf-8','gbk', $v["true_repay_time"]). '"';
				
				if($v['status']!='')
				{
					$list_value["status"] =  '"' . iconv('utf-8','gbk', l("REPAY_STATUS_".strim($v['status']))). '"';		
				}
				else
				{
					$list_value["status"] = "";
				}
				
				if($v['is_site_repay']!='')
				{
					$list_value["is_site_repay"] =  '"' . iconv('utf-8','gbk', l("IS_SITE_REPAY_".strim($v['is_site_repay']))). '"';		
				}
				else
				{
					$list_value["is_site_repay"] = "";
				}
				
				$list_value["l_key"] =  '"' . iconv('utf-8','gbk', $v["l_key"]). '"';
				
				$list_value["u_key"] =  '"' . iconv('utf-8','gbk', $v["u_key"]). '"';
				
				if($v['has_repay']!='')
				{
					$list_value["has_repay"] =  '"' . iconv('utf-8','gbk', l("HAS_REPAY_".strim($v['has_repay']))). '"';		
				}
				else
				{
					$list_value["has_repay"] = "";
				}
				
				$list_value["repay_manage_money"] =  '"' . iconv('utf-8','gbk', $v["repay_manage_money"]). '"';
				
				$list_value["repay_manage_impose_money"] =  '"' . iconv('utf-8','gbk', $v["repay_manage_impose_money"]). '"';
				
				$list_value["user_name"] =  '"' . iconv('utf-8','gbk', $v["user_name"]). '"';
				
				$list_value["t_user_name"] =  '"' . iconv('utf-8','gbk', $v["t_user_name"]). '"';
				
				$list_value["msg"] =  '"' . iconv('utf-8','gbk',  $v["msg"]). '"';

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