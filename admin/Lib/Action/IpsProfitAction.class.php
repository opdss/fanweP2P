<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------

class IpsProfitAction extends CommonAction{
	public function index()
	{
		$condition = " and t.pErrCode = 'MG00000F' ";
		
		$sql = "select t.*,td.id as mid,d.`name`,u.user_name from ".DB_PREFIX."ips_transfer_detail as td
LEFT JOIN ".DB_PREFIX."ips_transfer t on t.id = td.pid
LEFT JOIN ".DB_PREFIX."deal as d on d.id = t.deal_id
LEFT JOIN ".DB_PREFIX."user as u on u.ips_acct_no = td.pFIpsAcctNo
left join ".DB_PREFIX."user as da on da.ips_acct_no = td.pTIpsAcctNo
where t.pTransferType = 5";
		
		$count_sql = "select count(*) from ".DB_PREFIX."ips_transfer_detail as td
LEFT JOIN ".DB_PREFIX."ips_transfer t on t.id = td.pid
LEFT JOIN ".DB_PREFIX."deal as d on d.id = t.deal_id
LEFT JOIN ".DB_PREFIX."user as u on u.ips_acct_no = td.pFIpsAcctNo
left join ".DB_PREFIX."user as da on da.ips_acct_no = td.pTIpsAcctNo
where t.pTransferType = 5";
		
		/**
			id
			deal_id
			ref_data		
			pMerCode
			pMerBillNo
			pBidNo
			pDate
			pTransferType
			pTransferMode
			pErrCode
			pErrMsg
			pIpsBillNo
			pIpsTime
			is_callback
			pMemo1
			pMemo2
			pMemo3
			name
			user_name
		**/
		if(strim($_REQUEST['pMerCode'])!='')
		{		
			$condition .= " and t.pMerCode like '%".strim($_REQUEST['pMerCode'])."%'";
		}

		if(strim($_REQUEST['pMerBillNo'])!='')
		{		
			$condition .= " and t.pMerBillNo like '%".strim($_REQUEST['pMerBillNo'])."%'";
		}
		
		if(strim($_REQUEST['pBidNo'])!='')
		{		
			$condition .= " and t.pBidNo like '%".strim($_REQUEST['pBidNo'])."%'";
		}
		
		if(strim($_REQUEST['pIpsBillNo'])!='')
		{		
			$condition .= " and t.pIpsBillNo like '%".strim($_REQUEST['pIpsBillNo'])."%'";
		}

		if(isset($_REQUEST['pTransferMode'])&&intval(strim($_REQUEST['pTransferMode']))!=-1)
		{		
			$condition .= " and t.pTransferMode = " .intval(stirm($_REQUEST['pTransferMode']));
		}
		
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
			$condition .= " and UNIX_TIMESTAMP(t.pDate) >=".to_timespan(strim($start_time));
		}
		if(strim($end_time) !="")
		{
			$condition .= " and UNIX_TIMESTAMP(t.pDate) <=".  to_timespan(strim($end_time));
		}

		
		//取得满足条件的记录数
		$count = $GLOBALS['db']->getOne($count_sql.$condition);
		//print_r($count);die;
		//$name=$this->getActionName();
		
		
		if ($count > 0) {
			//创建分页对象
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $count, $listRows );
			//分页查询数据
			$voList = $GLOBALS['db']->getAll($sql.$condition." limit ".$p->firstRow . ',' . $p->listRows);
//			echo $model->getlastsql();
			//分页跳转的时候保证查询条件
			foreach ( $_REQUEST as $key => $val ) {
				if (! is_array ( $val )) {
					$p->parameter .= "$key=" . urlencode ( $val ) . "&";
				}
			}
			
			$page = $p->show ();
			//模板赋值显示
			$this->assign ( 'list', $voList );
			$this->assign ( "page", $page );
			$this->assign ( "nowPage",$p->nowPage);
		}
		$this->display ();
		
	}
	/*
	public function delete() {
		//删除指定记录
		
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M("ips_transfer_detail")->where($condition)->findAll();
				foreach($rel_data as $data)
				{
					$info[] = $data['id'];	
				}
				if($info) $info = implode(",",$info);
				$list = M("ips_transfer_detail")->where ( $condition )->delete();
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
	}*/
	public function view()
	{
		$id = intval($_REQUEST['id']);
		if(!$id)
		{
			$this->error(l("INVALID_ORDER"));
			return;
		}
		
		$sql = "select t.*,td.id as mid,d.`name`,u.user_name from ".DB_PREFIX."ips_transfer_detail as td
LEFT JOIN ".DB_PREFIX."ips_transfer t on t.id = td.pid
LEFT JOIN ".DB_PREFIX."deal as d on d.id = t.deal_id
LEFT JOIN ".DB_PREFIX."user as u on u.ips_acct_no = td.pFIpsAcctNo
left join ".DB_PREFIX."user as da on da.ips_acct_no = td.pTIpsAcctNo
where t.pTransferType = 5 and td.id =".$id;
		
		$ips_info = $GLOBALS['db']->getRow($sql);
		
		if(!$ips_info)
		{
			$this->error(l("INVALID_ORDER"));
		}
	
		$ips_info["is_callback"] =  l("IPS_IS_CALLBACK_".$ips_info["is_callback"]);
		
		if($ips_info["pTransferType"]!="")
		{
			$ips_info["pTransferType"] =  l("P_TRANSFER_TYPE_".$ips_info["pTransferType"]);
		}
		if($ips_info["pTransferMode"]!="")
		{
			$ips_info["pTransferMode"] =  l("P_TRANSFER_MODE_".$ips_info["pTransferMode"]);
		}
		
		$this->assign("ips_info",$ips_info);
		
		$this->display();
	}

	public function export_csv($page = 1)
	{
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
		
		$sql = "select t.*,td.id as mid,d.`name`,u.user_name from ".DB_PREFIX."ips_transfer_detail as td
LEFT JOIN ".DB_PREFIX."ips_transfer t on t.id = td.pid
LEFT JOIN ".DB_PREFIX."deal as d on d.id = t.deal_id
LEFT JOIN ".DB_PREFIX."user as u on u.ips_acct_no = td.pFIpsAcctNo
left join ".DB_PREFIX."user as da on da.ips_acct_no = td.pTIpsAcctNo
where t.pTransferType = 5";

		$condition = " and t.pErrCode = 'MG00000F' ";
		
		if(strim($_REQUEST['pMerCode'])!='')
		{		
			$condition .= " and t.pMerCode like '%".strim($_REQUEST['pMerCode'])."%'";
		}

		if(strim($_REQUEST['pMerBillNo'])!='')
		{		
			$condition .= " and t.pMerBillNo like '%".strim($_REQUEST['pMerBillNo'])."%'";
		}
		
		if(strim($_REQUEST['pBidNo'])!='')
		{		
			$condition .= " and t.pBidNo like '%".strim($_REQUEST['pBidNo'])."%'";
		}
		
		if(strim($_REQUEST['pIpsBillNo'])!='')
		{		
			$condition .= " and t.pIpsBillNo like '%".strim($_REQUEST['pIpsBillNo'])."%'";
		}

		if(isset($_REQUEST['pTransferMode'])&&intval(strim($_REQUEST['pTransferMode']))!=-1)
		{		
			$condition .= " and t.pTransferMode = " .intval(strim($_REQUEST['pTransferMode']));
		}
		
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
			$condition .= " and UNIX_TIMESTAMP(t.pDate) >=".to_timespan(strim($start_time));
		}
		if(strim($end_time) !="")
		{
			$condition .= " and UNIX_TIMESTAMP(t.pDate) <=".  to_timespan(strim($end_time));
		}

		$list = $GLOBALS['db']->getAll($sql.$condition." limit ".$limit);
		
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv'), $page+1);
			
			/**
			id
			deal_id
			ref_data		
			pMerCode
			pMerBillNo
			pBidNo
			pDate
			pTransferType
			pTransferMode
			pErrCode
			pErrMsg
			pIpsBillNo
			pIpsTime
			is_callback
			pMemo1
			pMemo2
			pMemo3
			name
			user_name
		**/
			$list_value_old = array(
				'id'=>'""', 
				'name'=>'""', 
				'ref_data' => '""',
				'pMerCode'=>'""',
				'pMerBillNo'=>'""', 
				'pBidNo'=>'""', 
				'pDate'=>'""',
				'pTransferType'=>'""',
				'pTransferMode'=>'""', 
				'pMemo1'=>'""',
				'pMemo2'=>'""',
				'pMemo3'=>'""', 
				'pIpsBillNo'=>'""', 
				'pIpsTime'=>'""', 
				'user_name'=>'""',
			);
			
	    	if($page == 1)
	    	{
		    	$content = iconv("utf-8","gbk","编号,贷款名称,还款日期,平台账号,商户开户流水号,标的号,商户日期,转账类型,转账方式,备注1,备注2,备注3,IPS订单号,IPS处理时间,转出方");	    		    	
		    	$content = $content . "\n";
	    	}

			foreach($list as $k=> $v)
			{
				$list_value = $list_value_old;
				$list_value["id"] = '"' . iconv('utf-8','gbk', $v['id']) . '"';
				
				$list_value["name"] = '"' . iconv('utf-8','gbk', $v['name']) . '"';

				$list_value["ref_data"] =  '"' . iconv('utf-8','gbk',  $v["ref_data"]). '"';
				
				$list_value["pMerCode"] =  '"' . iconv('utf-8','gbk', $v["pMerCode"]). '"';
				
				$list_value["pMerBillNo"] =  '"' . iconv('utf-8','gbk', $v["pMerBillNo"]). '"';
				
				$list_value["pBidNo"] =  '"' . iconv('utf-8','gbk', $v["pBidNo"]). '"';
				
				$list_value["pDate"] =  '"' . iconv('utf-8','gbk', $v["pDate"]). '"';
				
				$list_value["pTransferType"] =  '"' . iconv('utf-8','gbk', l("P_TRANSFER_TYPE_".$v["pTransferType"])). '"';
				
				$list_value["pTransferMode"] =  '"' . iconv('utf-8','gbk', l("P_TRANSFER_MODE_".$v["pTransferMode"])). '"';

				$list_value["pMemo1"] =  '"' . iconv('utf-8','gbk', $v["pMemo1"]).'"';
				
				$list_value["pMemo2"] =  '"' . iconv('utf-8','gbk', $v["pMemo2"]). '"';
				
				$list_value["pMemo3"] =  '"' . iconv('utf-8','gbk', $v["pMemo3"]). '"';
				
				$list_value["pIpsTime"] =  '"' . iconv('utf-8','gbk', $v["pIpsTime"]). '"';
				
				$list_value["name"] =  '"' . iconv('utf-8','gbk', $v["name"]). '"';
				
				$list_value["user_name"] =  '"' . iconv('utf-8','gbk', $v["user_name"]). '"';
				
				$list_value["pIpsBillNo"] =  '"' . iconv('utf-8','gbk', $v["pIpsBillNo"]). '"';


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