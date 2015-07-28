<?php
// +----------------------------------------------------------------------
// | easethink 方维借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class OverdueBillMonthAction extends CommonAction{
	public function index()
	{	
		$this->assign("main_title","本月到期账单");
		
		if (isset ( $_REQUEST ['_order'] )) {
			$sorder = $_REQUEST ['_order'];
		}
		else{
			$sorder = "id";
		}
		
		switch($sorder){
			case "name":
			case "cate_id":
					$order ="d.".$sorder;
				break;
			case "has_repay_status":
					$order ="dl.status";
				break;
			case "site_bad_status":
					$order ="dl.is_site_bad";
				break;
			case "is_has_send":
					$order ="d.send_three_msg_time";
				break;
			case "l_key_index":
					$order ="dl.l_key";
				break;
			default : 
				$order ="dl.".$sorder;
				break;
		}
		
	
		
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		}
		else{
			$sort = "desc";
		}
		
		
		//分类
		$cate_tree = M("DealCate")->where('is_delete = 0')->findAll();
		$cate_tree = D("DealCate")->toFormatTree($cate_tree,'name');
		$this->assign("cate_tree",$cate_tree);
		
		//开始加载搜索条件
		$condition =" 1=1 ";
		
		if(isset($_REQUEST['status'])){
			$status = intval($_REQUEST['status']);
			if($status >0){
				if(($status-1)==0)
					$condition .= " AND dl.has_repay=0 ";
				else
					$condition .= " AND dl.has_repay=1 and dl.status=".($status-2);
			}
		}
		else{
			$condition .= " AND dl.has_repay=0 ";
			$_REQUEST['status'] = 1;
		}
		if($status >0){
			if(($status-1)==0)
				$condition .= " AND dl.has_repay=0 ";  //0未还,1已还 2部分还款
			else
				$condition .= " AND dl.has_repay=1 and dl.status=".($status-2);
		}
		
		$deal_status = intval($_REQUEST['deal_status']);
		if($deal_status >0){
			$condition .= " AND dl.is_site_bad=".($deal_status-1);
		}//是否坏账
		
		$begin_time  = to_timespan(to_date(TIME_UTC ,"Y-m-d"),"Y-m-d");
		$end_time  = to_timespan(to_date(TIME_UTC ,"Y-m-d"),"Y-m-d") + 31*24*3600 ;
		$condition .= " and dl.repay_time between  $begin_time and $end_time ";	//一个月(31天)内的待还款账单
		
		if(trim($_REQUEST['name'])!='')
		{
			$condition .= " and d.name like '%".trim($_REQUEST['name'])."%'";
		}
		
		
		if(trim($_REQUEST['user_name'])!='')
		{
			$condition .= " and dl.user_id in (select id from  ".DB_PREFIX."user WHERE user_name='".trim($_REQUEST['user_name'])."')";			
		}
		
		//当前登录管理员所属会员
		
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$adm_name = $adm_session['adm_name'];
		$adm_id = intval($adm_session['adm_id']);
		
		$is_department = M("Admin")->where("id=".$adm_id)->getField("is_department");
		
		if( $is_department == 0){
			$condition .= " and d.admin_id =  ".$adm_id;
		}elseif($is_department == 1)
		{
			$adm_pid = $GLOBALS['db']->getAll("SELECT id FROM  ".DB_PREFIX."admin WHERE  pid = ".$adm_id);
			$flatmap = array_map("array_pop",$adm_pid);
			$adm_pid=implode(',',$flatmap);
			$condition .= " and d.admin_id in  ( ".$adm_id.",".$adm_pid." ) ";
		}
	
		if(intval($_REQUEST['cate_id'])>0)
		{
			require_once APP_ROOT_PATH."system/utils/child.php";
			$child = new Child("deal_cate");
			$cate_ids = $child->getChildIds(intval($_REQUEST['cate_id']));
			$cate_ids[] = intval($_REQUEST['cate_id']);
			$condition .=" and d.cate_id in (".implode(",",$cate_ids).") ";
		}
	
		$sql_count = " SELECT count(*) FROM ".DB_PREFIX."deal_repay dl LEFT JOIN ".DB_PREFIX."deal d ON d.id=dl.deal_id LEFT JOIN ".DB_PREFIX."user u on dl.user_id = u.id WHERE $condition ";
	
		$rs_count = $GLOBALS['db']->getOne($sql_count);
		$list = array();
		
		if($rs_count > 0){
			
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $rs_count, $listRows );
			
			$sql_list =  " SELECT dl.*,dl.l_key + 1 as l_key_index,d.name,d.cate_id,d.send_three_msg_time,u.user_name,u.mobile FROM ".DB_PREFIX."deal_repay dl LEFT JOIN ".DB_PREFIX."deal d  ON d.id=dl.deal_id left join ".DB_PREFIX."user u on u.id=dl.user_id WHERE $condition ORDER BY $order $sort LIMIT ".$p->firstRow . ',' . $p->listRows;
			
			$list = $GLOBALS['db']->getAll($sql_list);
			
			foreach($list as $k=>$v){
				$list[$k]['l_key_index'] = "第 ".$v['l_key_index']." 期";
				if($v['send_three_msg_time'] == $v['repay_time']){
					$list[$k]['is_has_send'] = 1;
				}
				else{
					$list[$k]['is_has_send'] = 0;
				}
				if($v['has_repay']==1){
					switch($v['status']){
						case 0;
							$list[$k]['has_repay_status'] = "提前还款";
							break;
						case 1;
							$list[$k]['has_repay_status'] = "准时还款";
							break;
						case 2;
							$list[$k]['has_repay_status'] = "逾期还款";
							break;
						case 3;
							$list[$k]['has_repay_status'] = "严重逾期";
							break;
					}
				}
				else{
					$list[$k]['has_repay_status'] = "<span style='color:red'>未还</span>";
				}
				
				if($v['is_site_bad'] == 1){
					$list[$k]['site_bad_status'] = "<span style='color:red'>坏账</span>";
				}
				else{
					$list[$k]['site_bad_status'] = "正常";
				}
			}
			
			$page = $p->show();
			$this->assign ( "page", $page );
			
		}
		
		$sortImg = $sort; //排序图标
		$sortAlt = $sort == 'desc' ? l("ASC_SORT") : l("DESC_SORT"); //排序提示
		$sort = $sort == 'desc' ? 1 : 0; //排序方式
		
		$this->assign ( 'sort', $sort );
		$this->assign ( 'order', $sorder );
		$this->assign ( 'sortImg', $sortImg );
		$this->assign ( 'sortType', $sortAlt );
		
		$this->assign("list",$list);
		$this->display ();
		return;
	}
	
	public function repay_plan()
	{
		require_once(APP_ROOT_PATH."app/Lib/common.php");
		require_once(APP_ROOT_PATH."app/Lib/deal.php");
		$id = intval($_REQUEST['id']);
	
		if($id==0){
			$this->success("数据错误");
		}
		$deal_info = get_deal($id);
	
		if(!$deal_info){
			$this->success("借款不存在");
		}
	
		$this->assign("deal_info",$deal_info);
		$repay_list  = get_deal_load_list($deal_info);
	
		if(!$repay_list){
			$this->success("无还款信息");
		}
	
		foreach($repay_list as $k=>$v){
			$repay_list[$k]['idx'] = $k + 1;
		}
		$this->assign("repay_list",$repay_list);
		$this->assign("deal_id",$id);
		$this->assign("deal_info",$deal_info);
		$this->display();
	}
	


	function repay_plan_a(){
		$deal_id = intval($_REQUEST['deal_id']);
		$l_key = intval($_REQUEST['l_key']);
		$obj = strim($_REQUEST['obj']);
	
		if($deal_id==0){
			$this->error("数据错误");
		}
		require_once(APP_ROOT_PATH."app/Lib/common.php");
		require_once(APP_ROOT_PATH."app/Lib/deal.php");
	
		$deal_info = get_deal($deal_id);
	
		if(!$deal_info){
			$this->error("借款不存在");
		}
	
	
		//输出投标列表
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
	
		$page_size = 10;
	
		$limit = (($page-1)*$page_size).",".$page_size;
	
		$result = get_deal_user_load_list($deal_info,0,$l_key,-1,0,0,1,$limit);
	
		foreach ($result['item'] as $k=>$v)
		{
			$result['item'][$k]['interest_money']=format_price($v['interest_money']);
		}
	
		$rs_count = $result['count'];
		$page_all = ceil($rs_count/$page_size);
	
		$this->assign("load_user",$result['item']);
		$this->assign("l_key",$l_key);
		$this->assign("page_all",$page_all);
		$this->assign("rs_count",$rs_count);
		$this->assign("page",$page);
		$this->assign("deal_id",$deal_id);
	
		$this->assign("obj",$obj);
		$this->assign("page_prev",$page - 1);
		$this->assign("page_next",$page + 1);
		$html = $this->fetch();
	
		$this->success($html);
	}
	
	function repay_log(){
		$id = intval($_REQUEST['id']);
		$deal_repay = $GLOBALS['db']->getRow("SELECT dr.*,dr.l_key + 1 as l_key_index,d.name FROM  ".DB_PREFIX."deal_repay dr LEFT JOIN ".DB_PREFIX."deal d ON d.id=dr.deal_id WHERE  dr.id=".$id);
		if(!$deal_repay){
			$this->error("账单不存在");
		}
		$sql_count = " SELECT count(*) FROM ".DB_PREFIX."deal_repay_log WHERE repay_id= ".$id;
	
		$rs_count = $GLOBALS['db']->getOne($sql_count);
		$list = array();
		if($rs_count > 0){
				
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $rs_count, $listRows );
				
			$sql_list =  " SELECT * FROM ".DB_PREFIX."deal_repay_log WHERE repay_id= ".$id." ORDER BY id DESC LIMIT ".$p->firstRow . ',' . $p->listRows;
			$list = $GLOBALS['db']->getAll($sql_list);
				
			$page = $p->show();
			$this->assign ( "page", $page );
				
		}
		$this->assign("list",$list);
		$this->assign("deal_repay",$deal_repay);
		$this->display ();
	
	}
	

	function op_status(){
		$id = intval($_REQUEST['id']);
		$deal_repay = $GLOBALS['db']->getRow("SELECT dr.*,dr.l_key + 1 as l_key_index,d.name FROM  ".DB_PREFIX."deal_repay dr LEFT JOIN ".DB_PREFIX."deal d ON d.id=dr.deal_id WHERE  dr.id=".$id);
		if(!$deal_repay){
			$this->error("账单不存在");
		}
		if($deal_repay['has_repay']==1){
			switch($deal_repay['status']){
				case 0;
				$deal_repay['has_repay_status'] = "提前还款";
				break;
				case 1;
				$deal_repay['has_repay_status'] = "准时还款";
				break;
				case 2;
				$deal_repay['has_repay_status'] = "逾期还款";
				break;
				case 3;
				$deal_repay['has_repay_status'] = "严重逾期";
				break;
			}
		}
		else{
			$deal_repay['has_repay_status'] = "<span style='color:red'>未还</span>";
		}
	
		if($deal_repay['is_site_bad'] == 1){
			$deal_repay['site_bad_status'] = "<span style='color:red'>坏账</span>";
		}
		else{
			$deal_repay['site_bad_status'] = "正常";
		}
	
		$this->assign("deal_repay",$deal_repay);
		$this->display();
	}
	
	public function do_op_status(){
		$id = intval($_REQUEST['id']);
	
		$deal_repay = $GLOBALS['db']->getRow("SELECT dr.*,dr.l_key + 1 as l_key_index,d.name FROM  ".DB_PREFIX."deal_repay dr LEFT JOIN ".DB_PREFIX."deal d ON d.id=dr.deal_id WHERE  dr.id=".$id);
		if(!$deal_repay){
			$this->error("账单不存在");
		}
		if(intval($_REQUEST['is_site_bad'])==0){
			$this->success("操作完成");
			die();
		}
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$msg = strim($_REQUEST['desc']);
		$GLOBALS['db']->query("UPDATE ".DB_PREFIX."deal_repay SET is_site_bad=".(intval($_REQUEST['is_site_bad']) -1)." WHERE id=".$id);
		if($GLOBALS['db']->affected_rows()){
				
			save_log($deal_repay['name']." 第 ".($deal_repay['l_key_index'] + 1)."期，坏账操作",1);
				
			repay_log($id,$msg,0,$adm_session['adm_id']);
			$this->success("操作成功");
				
		}
		else{
			save_log($deal_repay['name']." 第 ".($deal_repay['l_key_index'] + 1)."期，坏账操作",0);
				
			$this->error("操作失败");
		}
	}
	
	
	
	public function overdue_bill()
	{
		$this->assign("main_title","逾期账单");
	
		if (isset ( $_REQUEST ['_order'] )) {
			$sorder = $_REQUEST ['_order'];
		}
		else{
			$sorder = "id";
		}
	
	
		switch($sorder){
			case "name":
			case "cate_id":
				$order ="d.".$sorder;
				break;
			case "has_repay_status":
				$order ="dl.status";
				break;
			case "site_bad_status":
				$order ="dl.is_site_bad";
				break;
			case "is_has_send":
				$order ="d.send_three_msg_time";
				break;
			case "l_key_index":
				$order ="dl.l_key";
				break;
			default :
				$order ="dl.".$sorder;
				break;
		}
	
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		}
		else{
			$sort = "desc";
		}
	
		//分类
		$cate_tree = M("DealCate")->where('is_delete = 0')->findAll();
		$cate_tree = D("DealCate")->toFormatTree($cate_tree,'name');
		$this->assign("cate_tree",$cate_tree);
	
		//开始加载搜索条件
		$condition .= "  (dl.repay_time + 24*3600 - 1) < ".TIME_UTC."  ";
	
		$begin_time  = !isset($_REQUEST['begin_time'])? 0 : to_timespan($_REQUEST['begin_time'],"Y-m-d");
		if($begin_time > 0){
			if($begin_time > TIME_UTC)
			{
				$this->error("不能超过当前时间");
			}
			$condition .= " and dl.repay_time >= $begin_time ";
		}
	
		$deal_status = intval($_REQUEST['deal_status']);
		if($deal_status >0){
		$condition .= " AND dl.is_site_bad=".($deal_status-1);
		}
	
		if(trim($_REQUEST['name'])!='')
		{
			$condition .= " and d.name like '%".trim($_REQUEST['name'])."%'";
		}
	
			if(trim($_REQUEST['user_name'])!='')
		{
			$condition .= " and dl.user_id in (select id from  ".DB_PREFIX."user WHERE user_name='".trim($_REQUEST['user_name'])."')";
		}
		
		//当前登录管理员所属会员
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$adm_name = $adm_session['adm_name'];
		$adm_id = intval($adm_session['adm_id']);
	
		$is_department = M("Admin")->where("id=".$adm_id)->getField("is_department");
		
		if( $is_department == 0){
			$condition .= " and d.admin_id =  ".$adm_id;
		}elseif($is_department == 1)
		{
			$adm_pid = $GLOBALS['db']->getAll("SELECT id FROM  ".DB_PREFIX."admin WHERE  pid = ".$adm_id);
			$flatmap = array_map("array_pop",$adm_pid);
			$adm_pid=implode(',',$flatmap);
			$condition .= " and d.admin_id in  ( ".$adm_id.",".$adm_pid." ) ";
		}
	
		if(intval($_REQUEST['cate_id'])>0)
		{
		require_once APP_ROOT_PATH."system/utils/child.php";
		$child = new Child("deal_cate");
			$cate_ids = $child->getChildIds(intval($_REQUEST['cate_id']));
			$cate_ids[] = intval($_REQUEST['cate_id']);
			$condition .=" and d.cate_id in (".implode(",",$cate_ids).") ";
		}
		$condition .= " and dl.status !=1  ";
		
		$sql_count = " SELECT count(*) FROM ".DB_PREFIX."deal_repay dl LEFT JOIN ".DB_PREFIX."deal d ON d.id=dl.deal_id left join ".DB_PREFIX."user u on u.id=dl.user_id  WHERE $condition ";
		
		$rs_count = $GLOBALS['db']->getOne($sql_count);
		$list = array();
		if($rs_count > 0){
			
		if (! empty ( $_REQUEST ['listRows'] )) {
		$listRows = $_REQUEST ['listRows'];
		} else {
			$listRows = '';
		}
			$p = new Page ( $rs_count, $listRows );
				
			$sql_list =  " SELECT dl.*,dl.l_key + 1 as l_key_index,d.name,d.cate_id,d.send_three_msg_time,u.user_name,u.mobile FROM ".DB_PREFIX."deal_repay dl LEFT JOIN ".DB_PREFIX."deal d  ON d.id=dl.deal_id left join ".DB_PREFIX."user u on u.id=dl.user_id WHERE $condition ORDER BY $order $sort LIMIT ".$p->firstRow . ',' . $p->listRows;
			
			$list = $GLOBALS['db']->getAll($sql_list);
			
			foreach($list as $k=>$v){
				$list[$k]['l_key_index'] = "第 ".$v['l_key_index']." 期";
				if($v['send_three_msg_time'] == $v['repay_time']){
				$list[$k]['is_has_send'] = 1;
				}
				else{
					$list[$k]['is_has_send'] = 0;
				}
				if($v['has_repay']==1){
					switch($v['status']){
								case 0;
							$list[$k]['has_repay_status'] = "提前还款";
							break;
								case 1;
								$list[$k]['has_repay_status'] = "准时还款";
							break;
								case 2;
								$list[$k]['has_repay_status'] = "逾期还款";
							break;
										case 3;
										$list[$k]['has_repay_status'] = "严重逾期";
												break;
							}
				}
				else{
					$list[$k]['has_repay_status'] = "<span style='color:red'>未还</span>";
				}
		
				if($v['is_site_bad'] == 1){
					$list[$k]['site_bad_status'] = "<span style='color:red'>坏账</span>";
				}
				else{
					$list[$k]['site_bad_status'] = "正常";
				}
			}
						
			$page = $p->show();
			$this->assign ( "page", $page );
	}
	
	$sortImg = $sort; //排序图标
	$sortAlt = $sort == 'desc' ? l("ASC_SORT") : l("DESC_SORT"); //排序提示
	$sort = $sort == 'desc' ? 1 : 0; //排序方式
	
	$this->assign ( 'sort', $sort );
	$this->assign ( 'order', $sorder );
	$this->assign ( 'sortImg', $sortImg );
	$this->assign ( 'sortType', $sortAlt );
	
	$this->assign("list",$list);
	$this->display ();
	return;
	}
	
	
	public function repayment_bill()
	{
		$this->assign("main_title","已还款账单");
	
		if (isset ( $_REQUEST ['_order'] )) {
			$sorder = $_REQUEST ['_order'];
		}
		else{
			$sorder = "id";
		}
	
		switch($sorder){
			case "name":
			case "cate_id":
				$order ="d.".$sorder;
				break;
			case "has_repay_status":
				$order ="dl.status";
				break;
			case "site_bad_status":
				$order ="dl.is_site_bad";
				break;
			case "is_has_send":
				$order ="d.send_three_msg_time";
				break;
			case "l_key_index":
				$order ="dl.l_key";
				break;
			default :
				$order ="dl.".$sorder;
				break;
		}
	
	
	
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		}
		else{
			$sort = "desc";
		}
	
	
		//分类
		$cate_tree = M("DealCate")->where('is_delete = 0')->findAll();
		$cate_tree = D("DealCate")->toFormatTree($cate_tree,'name');
		$this->assign("cate_tree",$cate_tree);
	
		//开始加载搜索条件
		$condition =" 1=1 ";
		 //0未还,1已还 2部分还款
		$condition .= " AND dl.has_repay=1 ";
	
	
		if(trim($_REQUEST['name'])!='')
		{
			$condition .= " and d.name like '%".trim($_REQUEST['name'])."%'";
		}
	
		if(trim($_REQUEST['user_name'])!='')
		{
			$condition .= " and dl.user_id in (select id from  ".DB_PREFIX."user WHERE user_name='".trim($_REQUEST['user_name'])."')";
		}
	

		$begin_time  = !isset($_REQUEST['begin_time'])? to_timespan(to_date(TIME_UTC ,"Y-m-d"),"Y-m-d")  : (trim($_REQUEST['begin_time']) =="" ? 0 : to_timespan($_REQUEST['begin_time'],"Y-m-d"));
		$end_time  = !isset($_REQUEST['end_time'])?to_timespan(to_date(TIME_UTC ,"Y-m-d"),"Y-m-d") + 31*24*3600: (trim($_REQUEST['end_time']) =="" ? 0 : to_timespan($_REQUEST['end_time'],"Y-m-d"));
		if($begin_time > 0 || $end_time > 0){
			if($end_time==0)
			{
				$condition .= " and dl.repay_time >= $begin_time ";	
			}
			else
				$condition .= " and dl.repay_time between  $begin_time and $end_time ";	
		}
		
		$_REQUEST['begin_time'] = to_date($begin_time ,"Y-m-d");
		$_REQUEST['end_time'] = to_date($end_time ,"Y-m-d");
		
		//当前登录管理员所属会员
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$adm_name = $adm_session['adm_name'];
		$adm_id = intval($adm_session['adm_id']);
	
		$is_department = M("Admin")->where("id=".$adm_id)->getField("is_department");
	
		if( $is_department == 0){
			$condition .= " and d.admin_id =  ".$adm_id;
		}elseif($is_department == 1)
		{
			$adm_pid = $GLOBALS['db']->getAll("SELECT id FROM  ".DB_PREFIX."admin WHERE  pid = ".$adm_id);
			$flatmap = array_map("array_pop",$adm_pid);
			$adm_pid=implode(',',$flatmap);
			$condition .= " and d.admin_id in  ( ".$adm_id.",".$adm_pid." ) ";
		}
	
		if(intval($_REQUEST['cate_id'])>0)
		{
			require_once APP_ROOT_PATH."system/utils/child.php";
			$child = new Child("deal_cate");
			$cate_ids = $child->getChildIds(intval($_REQUEST['cate_id']));
			$cate_ids[] = intval($_REQUEST['cate_id']);
			$condition .=" and d.cate_id in (".implode(",",$cate_ids).") ";
		}
	
		$sql_count = " SELECT count(*) FROM ".DB_PREFIX."deal_repay dl LEFT JOIN ".DB_PREFIX."deal d ON d.id=dl.deal_id LEFT JOIN ".DB_PREFIX."user u on dl.user_id = u.id WHERE $condition ";
	
		$rs_count = $GLOBALS['db']->getOne($sql_count);
		$list = array();
	
		if($rs_count > 0){
				
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $rs_count, $listRows );
				
			$sql_list =  " SELECT dl.*,dl.l_key + 1 as l_key_index,d.name,d.cate_id,d.send_three_msg_time,u.user_name,u.mobile FROM ".DB_PREFIX."deal_repay dl LEFT JOIN ".DB_PREFIX."deal d  ON d.id=dl.deal_id left join ".DB_PREFIX."user u on u.id=dl.user_id WHERE $condition ORDER BY $order $sort LIMIT ".$p->firstRow . ',' . $p->listRows;
				
			$list = $GLOBALS['db']->getAll($sql_list);
				
			foreach($list as $k=>$v){
				$list[$k]['l_key_index'] = "第 ".$v['l_key_index']." 期";
				if($v['send_three_msg_time'] == $v['repay_time']){
					$list[$k]['is_has_send'] = 1;
				}
				else{
					$list[$k]['is_has_send'] = 0;
				}
				if($v['has_repay']==1){
					switch($v['status']){
						case 0;
						$list[$k]['has_repay_status'] = "提前还款";
						break;
						case 1;
						$list[$k]['has_repay_status'] = "准时还款";
						break;
						case 2;
						$list[$k]['has_repay_status'] = "逾期还款";
						break;
						case 3;
						$list[$k]['has_repay_status'] = "严重逾期";
						break;
					}
				}
				else{
					$list[$k]['has_repay_status'] = "<span style='color:red'>未还</span>";
				}
	
				if($v['is_site_bad'] == 1){
					$list[$k]['site_bad_status'] = "<span style='color:red'>坏账</span>";
				}
				else{
					$list[$k]['site_bad_status'] = "正常";
				}
			}
				
			$page = $p->show();
			$this->assign ( "page", $page );
				
		}
	
		$sortImg = $sort; //排序图标
		$sortAlt = $sort == 'desc' ? l("ASC_SORT") : l("DESC_SORT"); //排序提示
		$sort = $sort == 'desc' ? 1 : 0; //排序方式
	
		$this->assign ( 'sort', $sort );
		$this->assign ( 'order', $sorder );
		$this->assign ( 'sortImg', $sortImg );
		$this->assign ( 'sortType', $sortAlt );
		$this->assign("list",$list);
		$this->display ();
		return;
	}
	
	
	//还款中借款标
	public function repayloan_scale(){
		
	
		if (isset ( $_REQUEST ['_order'] )) {
			$sorder = $_REQUEST ['_order'];
		}
		else{
			$sorder = "id";
		}
	
		switch($sorder){
			case "name":
			case "cate_id":
				$order =$sorder;
				break;
			default :
				$order = $sorder;
				break;
		}
	
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		}
		else{
			$sort = "asc";
		}
	
	
		//开始加载搜索条件
		$condition =" 1=1 ";
	
	
		if(trim($_REQUEST['name'])!='')
		{
			$condition .= " and d.name like '%".trim($_REQUEST['name'])."%'";
		}
		
		if(trim($_REQUEST['user_name'])!='')
		{
			$condition .= " and u.user_name like '%".trim($_REQUEST['user_name'])."%'  ";
		}
		
		//当前登录管理员所属会员
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$adm_name = $adm_session['adm_name'];
		$adm_id = intval($adm_session['adm_id']);
	
		$is_department = M("Admin")->where("id=".$adm_id)->getField("is_department");
	
		if( $is_department == 0){
			$condition .= " and d.admin_id =  ".$adm_id; 
		}elseif($is_department == 1)
		{
			$adm_pid = $GLOBALS['db']->getAll("SELECT id FROM  ".DB_PREFIX."admin WHERE  pid = ".$adm_id);
			$flatmap = array_map("array_pop",$adm_pid);
			$adm_pid=implode(',',$flatmap);
			$condition .= " and d.admin_id in  ( ".$adm_id.",".$adm_pid." ) ";
		}
		
		//分类
		$cate_tree = M("DealCate")->where('is_delete = 0')->findAll();
		$cate_tree = D("DealCate")->toFormatTree($cate_tree,'name');
		$this->assign("cate_tree",$cate_tree);
		
		if(intval($_REQUEST['cate_id'])>0)
		{
			require_once APP_ROOT_PATH."system/utils/child.php";
			$child = new Child("deal_cate");
			$cate_ids = $child->getChildIds(intval($_REQUEST['cate_id']));
			$cate_ids[] = intval($_REQUEST['cate_id']);
			$condition .=" and d.cate_id in (".implode(",",$cate_ids).") ";
		}
		
		$condition .=" and d.deal_status = 4  "; //0待等材料，1进行中，2满标，3流标，4还款中，5已还清

		$sql_count = " SELECT count(*) FROM ".DB_PREFIX."deal d left join ".DB_PREFIX."user u on d.user_id = u.id   WHERE $condition ";
		$rs_count = $GLOBALS['db']->getOne($sql_count);
		$list = array();
	
		if($rs_count > 0){
				
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $rs_count, $listRows );
				
			$sql_list =  " SELECT d.*,u.admin_id,u.user_name FROM ".DB_PREFIX."deal d left join ".DB_PREFIX."user u on d.user_id = u.id  WHERE $condition ORDER BY $order $sort LIMIT ".$p->firstRow . ',' . $p->listRows;
				
			$list = $GLOBALS['db']->getAll($sql_list);
				
			foreach($list as $k=>$v){
				//	$list[$k]['site_bad_status'] = "正常";
			}
				
			$page = $p->show();
			$this->assign ( "page", $page );
				
		}
	
		$sortImg = $sort; //排序图标
		$sortAlt = $sort == 'desc' ? l("ASC_SORT") : l("DESC_SORT"); //排序提示
		$sort = $sort == 'desc' ? 1 : 0; //排序方式
	
		$this->assign ( 'sort', $sort );
		$this->assign ( 'order', $sorder );
		$this->assign ( 'sortImg', $sortImg );
		$this->assign ( 'sortType', $sortAlt );
	
		$this->assign("list",$list);
		$this->display ();
		return;
	}
	
	//已完成借款标
	public function completedloan_scale(){
	
		if (isset ( $_REQUEST ['_order'] )) {
			$sorder = $_REQUEST ['_order'];
		}
		else{
			$sorder = "id";
		}
	
		switch($sorder){
			case "name":
			case "cate_id":
				$order =$sorder;
				break;
			default :
				$order = $sorder;
				break;
		}
	
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		}
		else{
			$sort = "asc";
		}
	
	
		//开始加载搜索条件
		$condition =" 1=1 ";
	
	
		if(trim($_REQUEST['name'])!='')
		{
			$condition .= " and d.name like '%".trim($_REQUEST['name'])."%'";
		}
	
		if(trim($_REQUEST['user_name'])!='')
		{
			$condition .= " and u.user_name like '%".trim($_REQUEST['user_name'])."%'  ";
		}
	
		//当前登录管理员所属会员
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$adm_name = $adm_session['adm_name'];
		$adm_id = intval($adm_session['adm_id']);
	
		$is_department = M("Admin")->where("id=".$adm_id)->getField("is_department");
	
		if( $is_department == 0){
			$condition .= " and d.admin_id =  ".$adm_id;
		}elseif($is_department == 1)
		{
			$adm_pid = $GLOBALS['db']->getAll("SELECT id FROM  ".DB_PREFIX."admin WHERE  pid = ".$adm_id);
			$flatmap = array_map("array_pop",$adm_pid);
			$adm_pid=implode(',',$flatmap);
			$condition .= " and d.admin_id in  ( ".$adm_id.",".$adm_pid." ) ";
		}
	
		//分类
		$cate_tree = M("DealCate")->where('is_delete = 0')->findAll();
		$cate_tree = D("DealCate")->toFormatTree($cate_tree,'name');
		$this->assign("cate_tree",$cate_tree);
	
		if(intval($_REQUEST['cate_id'])>0)
		{
			require_once APP_ROOT_PATH."system/utils/child.php";
			$child = new Child("deal_cate");
			$cate_ids = $child->getChildIds(intval($_REQUEST['cate_id']));
			$cate_ids[] = intval($_REQUEST['cate_id']);
			$condition .=" and d.cate_id in (".implode(",",$cate_ids).") ";
		}
	
		$condition .=" and d.deal_status = 5  "; //0待等材料，1进行中，2满标，3流标，4还款中，5已还清
	
		$sql_count = " SELECT count(*) FROM ".DB_PREFIX."deal d left join ".DB_PREFIX."user u on d.user_id = u.id   WHERE $condition ";
		$rs_count = $GLOBALS['db']->getOne($sql_count);
		$list = array();
	
		if($rs_count > 0){
	
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $rs_count, $listRows );
	
			$sql_list =  " SELECT d.*,u.admin_id,u.user_name FROM ".DB_PREFIX."deal d left join ".DB_PREFIX."user u on d.user_id = u.id  WHERE $condition ORDER BY $order $sort LIMIT ".$p->firstRow . ',' . $p->listRows;
	
			$list = $GLOBALS['db']->getAll($sql_list);
	
			foreach($list as $k=>$v){
				//	$list[$k]['site_bad_status'] = "正常";
			}
	
			$page = $p->show();
			$this->assign ( "page", $page );
	
		}
	
		$sortImg = $sort; //排序图标
		$sortAlt = $sort == 'desc' ? l("ASC_SORT") : l("DESC_SORT"); //排序提示
		$sort = $sort == 'desc' ? 1 : 0; //排序方式
	
		$this->assign ( 'sort', $sort );
		$this->assign ( 'order', $sorder );
		$this->assign ( 'sortImg', $sortImg );
		$this->assign ( 'sortType', $sortAlt );
	
		$this->assign("list",$list);
		$this->display ();
		return;
	}
	
	//待分配借款标
	public function unallocated_standard(){
	
		if (isset ( $_REQUEST ['_order'] )) {
			$sorder = $_REQUEST ['_order'];
		}
		else{
			$sorder = "id";
		}
	
		switch($sorder){
			case "name":
			case "cate_id":
				$order =$sorder;
				break;
			default :
				$order = $sorder;
				break;
		}
	
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		}
		else{
			$sort = "desc";
		}
	
		//开始加载搜索条件
		$condition =" 1=1 ";
	
		if(trim($_REQUEST['name'])!='')
		{
			$condition .= " and d.name like '%".trim($_REQUEST['name'])."%'";
		}
	
		if(trim($_REQUEST['user_name'])!='')
		{
			$condition .= " and u.user_name like '%".trim($_REQUEST['user_name'])."%'  ";
		}
	
		//未分配的用户
		/*
		$user_id = $GLOBALS['db']->getAll("SELECT id FROM  ".DB_PREFIX."user WHERE  admin_id = 0");
		$flatmap = array_map("array_pop",$user_id);
		$user_id=implode(',',$flatmap);
		$condition .= " AND d.user_id in ( ". $user_id ." )";
		*/
		
		//type 0未分配管理员 或 客服 1 未分配管理员   2未分配客服  
		$type = isset($_REQUEST['type']) ? intval($_REQUEST['type']) : 0;
		
		if($type == 0){
			$condition .= " AND (d.admin_id = 0 or d.customers_id = 0 ) ";
		}elseif($type == 1){
			$condition .= " AND d.admin_id = 0 ";
		}else{
			$condition .= " AND d.customers_id = 0 ";
		}
		$this->assign ( 'type', $type );
		
		//分类
		$cate_tree = M("DealCate")->where('is_delete = 0')->findAll();
		$cate_tree = D("DealCate")->toFormatTree($cate_tree,'name');
		$this->assign("cate_tree",$cate_tree);
	
		if(intval($_REQUEST['cate_id'])>0)
		{
			require_once APP_ROOT_PATH."system/utils/child.php";
			$child = new Child("deal_cate");
			$cate_ids = $child->getChildIds(intval($_REQUEST['cate_id']));
			$cate_ids[] = intval($_REQUEST['cate_id']);
			$condition .=" and d.cate_id in (".implode(",",$cate_ids).") ";
		}
	
		$sql_count = " SELECT count(*) FROM ".DB_PREFIX."deal d left join ".DB_PREFIX."user u on d.user_id = u.id   WHERE $condition ";
		$rs_count = $GLOBALS['db']->getOne($sql_count);
		$list = array();
	
		if($rs_count > 0){
	
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $rs_count, $listRows );
	
			$sql_list =  " SELECT d.*,a.adm_name as admin_name,c.name as customer_name FROM ".DB_PREFIX."deal d left join ".DB_PREFIX."admin a on d.admin_id = a.id left join ".DB_PREFIX."customer c on c.id = d.customers_id  WHERE $condition ORDER BY $order $sort LIMIT ".$p->firstRow . ',' . $p->listRows;
	
			$list = $GLOBALS['db']->getAll($sql_list);
	
			$page = $p->show();
			$this->assign ( "page", $page );
	
		}
	
		$sortImg = $sort; //排序图标
		$sortAlt = $sort == 'desc' ? l("ASC_SORT") : l("DESC_SORT"); //排序提示
		$sort = $sort == 'desc' ? 1 : 0; //排序方式
	
		$this->assign ( 'sort', $sort );
		$this->assign ( 'order', $sorder );
		$this->assign ( 'sortImg', $sortImg );
		$this->assign ( 'sortType', $sortAlt );
	
		$this->assign("list",$list);
		$this->display ();
		return;
	}
	
	//已坏账借款标
	public function badloan_scale(){
	
		if (isset ( $_REQUEST ['_order'] )) {
			$sorder = $_REQUEST ['_order'];
		}
		else{
			$sorder = "id";
		}
	
		switch($sorder){
			case "name":
			case "cate_id":
				$order =$sorder;
				break;
			default :
				$order = $sorder;
				break;
		}
	
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		}
		else{
			$sort = "desc";
		}
	
	
		//开始加载搜索条件
		$condition =" 1=1 ";
	
	
		if(trim($_REQUEST['name'])!='')
		{
			$condition .= " and d.name like '%".trim($_REQUEST['name'])."%'";
		}
	
		if(trim($_REQUEST['user_name'])!='')
		{
			$condition .= " and u.user_name like '%".trim($_REQUEST['user_name'])."%'  ";
		}
	
		//当前登录管理员所属会员
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$adm_name = $adm_session['adm_name'];
		$adm_id = intval($adm_session['adm_id']);
	
		$is_department = M("Admin")->where("id=".$adm_id)->getField("is_department");
	
		if( $is_department == 0){
			$condition .= " and d.admin_id =  ".$adm_id;
		}elseif($is_department == 1)
		{
			$adm_pid = $GLOBALS['db']->getAll("SELECT id FROM  ".DB_PREFIX."admin WHERE  pid = ".$adm_id);
			$flatmap = array_map("array_pop",$adm_pid);
			$adm_pid=implode(',',$flatmap);
			$condition .= " and d.admin_id in  ( ".$adm_id.",".$adm_pid." ) ";
		}
	
		//分类
		$cate_tree = M("DealCate")->where('is_delete = 0')->findAll();
		$cate_tree = D("DealCate")->toFormatTree($cate_tree,'name');
		$this->assign("cate_tree",$cate_tree);
	
		if(intval($_REQUEST['cate_id'])>0)
		{
			require_once APP_ROOT_PATH."system/utils/child.php";
			$child = new Child("deal_cate");
			$cate_ids = $child->getChildIds(intval($_REQUEST['cate_id']));
			$cate_ids[] = intval($_REQUEST['cate_id']);
			$condition .=" and d.cate_id in (".implode(",",$cate_ids).") ";
		}
		
		//坏账
		$condition .=" and d.deal_status > 0  and dr.is_site_bad = 1"; //0待等材料，1进行中，2满标，3流标，4还款中，5已还清
		
		/*
		$bad_id = $GLOBALS['db']->getAll("SELECT DISTINCT user_id FROM  ".DB_PREFIX."user_sta WHERE  bad_count > 0");
		
		$flatmap = array_map("array_pop",$bad_id);
		$bad_id=implode(',',$flatmap);
		$condition .= " AND d.user_id in ( ". $bad_id ." )";*/
		
		
		$sql_count = " SELECT count(*) FROM ".DB_PREFIX."deal d 
				left join ".DB_PREFIX."deal_repay dr on dr.deal_id =d.id
				left join ".DB_PREFIX."user u on d.user_id = u.id   WHERE $condition ";
		$rs_count = $GLOBALS['db']->getOne($sql_count);
		$list = array();
	
		if($rs_count > 0){
	
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $rs_count, $listRows );
	
			$sql_list =  " SELECT d.*,u.admin_id,u.user_name,dr.is_site_bad FROM ".DB_PREFIX."deal d
					left join ".DB_PREFIX."deal_repay dr on dr.deal_id =d.id 
					left join ".DB_PREFIX."user u on d.user_id = u.id  
				WHERE $condition ORDER BY $order $sort LIMIT ".$p->firstRow . ',' . $p->listRows;
	
			$list = $GLOBALS['db']->getAll($sql_list);
	
			$page = $p->show();
			$this->assign ( "page", $page );
	
		}
	
		$sortImg = $sort; //排序图标
		$sortAlt = $sort == 'desc' ? l("ASC_SORT") : l("DESC_SORT"); //排序提示
		$sort = $sort == 'desc' ? 1 : 0; //排序方式
	
		$this->assign ( 'sort', $sort );
		$this->assign ( 'order', $sorder );
		$this->assign ( 'sortImg', $sortImg );
		$this->assign ( 'sortType', $sortAlt );
	
		$this->assign("list",$list);
		$this->display ();
		return;
	}
	

	public function edit() {
		$id = intval($_REQUEST ['id']);
		$condition['is_delete'] = 0;
		$condition['id'] = $id;
		$vo = M("Deal")->where($condition)->find();
		
		$vo['start_time'] = $vo['start_time']!=0?to_date($vo['start_time']):'';
	
		if($vo['deal_status'] ==0){
			$level_list = load_auto_cache("level");
			$u_level = M("User")->where("id=".$vo['user_id'])->getField("level_id");
			$vo['services_fee'] = $level_list['services_fee'][$u_level];
		}
	
		if($vo['deal_sn']==""){
			$deal_sn = "MER".to_date(TIME_UTC,"Y")."".str_pad($id,7,0,STR_PAD_LEFT);
			$this->assign ( 'deal_sn', $deal_sn );
		}
	
		$user_info = M("User") -> getById($vo['user_id']);
		$old_imgdata_str = unserialize($user_info['view_info']);
	
		foreach($old_imgdata_str as $k=>$v){
			$old_imgdata_str[$k]['key'] = $k;  /*+一个key*/
		}
		$this->assign("user_info",$user_info);
		$this->assign("old_imgdata_str",$old_imgdata_str);
	
	
		$vo['view_info'] = unserialize($vo['view_info']);
	
		if($vo['publish_wait'] == 1){
			$vo['manage_fee'] = app_conf("MANAGE_FEE");
			$vo['user_loan_manage_fee'] = app_conf("USER_LOAN_MANAGE_FEE");
			$vo['manage_impose_fee_day1'] = app_conf("MANAGE_IMPOSE_FEE_DAY1");
			$vo['manage_impose_fee_day2'] = app_conf("MANAGE_IMPOSE_FEE_DAY2");
			$vo['impose_fee_day1'] = app_conf("IMPOSE_FEE_DAY1");
			$vo['impose_fee_day2'] = app_conf("IMPOSE_FEE_DAY2");
			$vo['user_load_transfer_fee'] = app_conf("USER_LOAD_TRANSFER_FEE");
			$vo['compensate_fee'] = app_conf("COMPENSATE_FEE");
			$vo['user_bid_rebate'] = app_conf("USER_BID_REBATE");
			$vo['generation_position'] = 100;
		}
	
		$this->assign ( 'vo', $vo );
	
		$citys = M("DealCity")->where('is_delete= 0 and is_effect=1 ')->findAll();
		$citys_link = M("DealCityLink")->where("deal_id=".$id)->findAll();
		foreach($citys as $k=>$v){
			foreach($citys_link as $kk=>$vv){
				if($vv['city_id'] == $v['id'])
					$citys[$k]['is_selected'] = 1;
			}
		}
	
		$this->assign ( 'citys', $citys );
	
		if(trim($_REQUEST['type'])=="deal_status"){
			$this->display ("Deal:deal_status");
			exit();
		}
	
		$deal_cate_tree = M("DealCate")->where('is_delete = 0')->findAll();
		$deal_cate_tree = D("DealCate")->toFormatTree($deal_cate_tree,'name');
		$this->assign("deal_cate_tree",$deal_cate_tree);
	
		$deal_agency = M("User")->where('is_effect = 1 and user_type =2')->order('sort DESC')->findAll();
		$this->assign("deal_agency",$deal_agency);
	
		$deal_type_tree = M("DealLoanType")->findAll();
		$deal_type_tree = D("DealLoanType")->toFormatTree($deal_type_tree,'name');
		$this->assign("deal_type_tree",$deal_type_tree);
	
		$loantype_list = load_auto_cache("loantype_list");
		$this->assign("loantype_list",$loantype_list);
		$this->display ();
	}
	
	
	public function update() {
		B('FilterString');
		$data = M("Deal")->create ();
	
		$log_info = M("Deal")->where("id=".intval($data['id']))->getField("name");
		//开始验证有效性
		$this->assign("jumpUrl","javascript:history.back(-1);");
		if(!check_empty($data['name']))
		{
			$this->error(L("DEAL_NAME_EMPTY_TIP"));
		}
		if(!check_empty($data['sub_name']))
		{
			$this->error(L("DEAL_SUB_NAME_EMPTY_TIP"));
		}
		if($data['cate_id']==0)
		{
			$this->error(L("DEAL_CATE_EMPTY_TIP"));
		}
		if(D("Deal")->where("deal_sn='".$data['deal_sn']."' and id<>".$data['id'])->count() > 0){
			$this->error("借款编号已存在");
		}
	
		$loantype_list = load_auto_cache("loantype_list");
		if(!in_array($data['repay_time_type'],$loantype_list[$data['loantype']]['repay_time_type'])){
			$this->error("还款方式不支持当前借款期限类型");
		}
	
		$data['update_time'] = TIME_UTC;
		$data['publish_wait'] = 0;
	
		$data['start_time'] = trim($data['start_time'])==''?0:to_timespan($data['start_time']);
	
		$user_info = M("User") -> getById($data['user_id']);
		$old_imgdata_str = unserialize($user_info['view_info']);
	
	
		$data['view_info'] = array();
		foreach($_REQUEST['key'] as $k=>$v){
			if(isset($old_imgdata_str[$v])){
				$data['view_info'][$v] = $old_imgdata_str[$v];
			}
		}
		$data['view_info'] = serialize($data['view_info']);
	
		if($data['deal_status'] == 4){
			if($GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."deal_load where deal_id=".$data['id']) <floatval($data['borrow_amount'])){
				$this->error("未满标无法设置为还款状态!");
				exit();
			}
		}
	
		if($data['agency_id']!=M("Deal")->where("id=".$data['id'])->getField("agency_id")){
			$data['agency_status'] = 0;
		}
	
		// 更新数据
		$list=M("Deal")->save($data);
		if (false !== $list) {
				
			M("DealCityLink")->where ("deal_id=".$data['id'])->delete();
			foreach($_REQUEST['city_id'] as $k=>$v){
				if(intval($v) > 0){
					$deal_city_link['deal_id'] = $data['id'];
					$deal_city_link['city_id'] = intval($v);
					M("DealCityLink")->add ($deal_city_link);
				}
	
			}
				
			require_once(APP_ROOT_PATH."app/Lib/common.php");
			if($data['is_delete']==3){
				//发送失败短信通知
				if(app_conf("SMS_ON")==1){
					$user_info  = D("User")->where("id=".$data['user_id'])->find();
					$deal_info = D("Deal")->where("id=".$data['id'])->find();
	
					$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_SMS_DEAL_DELETE'");
					$tmpl_content = $tmpl['content'];
						
					$notice['user_name'] = $user_info["user_name"];
					$notice['deal_name'] = $data['name'];
					$notice['site_name'] = app_conf("SHOP_TITLE");
					$notice['delete_msg'] = $data['delete_msg'];
					$notice['deal_publish_time'] = to_date($deal_info['create_time'],"Y年m月d日");
						
					$GLOBALS['tmpl']->assign("notice",$notice);
						
					$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
						
					$msg_data['dest'] = $user_info['mobile'];
					$msg_data['send_type'] = 0;
					$msg_data['title'] = "审核失败通知";
					$msg_data['content'] = addslashes($msg);;
					$msg_data['send_time'] = 0;
					$msg_data['is_send'] = 0;
					$msg_data['create_time'] = TIME_UTC;
					$msg_data['user_id'] = $user_info['id'];
					$msg_data['is_html'] = $tmpl['is_html'];
					$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
				}
			}
			else{
				//成功提示
				syn_deal_status($data['id']);
				syn_deal_match($data['id']);
				//发送电子协议邮件
				require_once(APP_ROOT_PATH."app/Lib/deal.php");
				send_deal_contract_email($data['id'],array(),$data['user_id']);
			}
			//成功提示
			save_log("编号：".$data['id']."，".$log_info.L("UPDATE_SUCCESS"),1);
			$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			$dbErr = M()->getDbError();
			save_log("编号：".$data['id']."，".$log_info.L("UPDATE_FAILED").$dbErr,0);
			$this->error(L("UPDATE_FAILED").$dbErr,0);
		}
	}
	
	public function set_effect()
	{
		$id = intval($_REQUEST['id']);
		$ajax = intval($_REQUEST['ajax']);
		$info = M("Deal")->where("id=".$id)->getField("name");
		$c_is_effect = M("Deal")->where("id=".$id)->getField("is_effect");  //当前状态
		$n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
		M("Deal")->where("id=".$id)->setField("is_effect",$n_is_effect);
		save_log($info.l("SET_EFFECT_".$n_is_effect),1);
	
		$this->ajaxReturn($n_is_effect,l("SET_EFFECT_".$n_is_effect),1)	;
	}
	
	
	public function add_admin_distribut()
	{
		$id = intval($_REQUEST ['id']);
		$deal_info = M("Deal")->where("id=".$id)->find();
		$this->assign ( 'deal_info', $deal_info);
		//管理员列表
		$adm_sql = " SELECT * FROM ".DB_PREFIX."admin WHERE is_delete= 0 and is_effect=1 and is_department = 0";
		$adm_list = $GLOBALS['db']->getAll($adm_sql);
		$this->assign ('admins', $adm_list );
		$this->display ();
	}
	
	public function add_customer_distribut()
	{
		$id = intval($_REQUEST ['id']);
		$deal_info = M("Deal")->where("id=".$id)->find();
		$this->assign ('deal_info', $deal_info);
		//客服列表
		$customer_sql =  " SELECT * FROM ".DB_PREFIX."customer WHERE is_delete= 0 and is_effect=1";
		$customer_list = $GLOBALS['db']->getAll($customer_sql);
		$this->assign ( 'customers', $customer_list );
		$this->display ();
	}
	
	public function all_loan()
	{
		if (isset ( $_REQUEST ['_order'] )) {
			$sorder = $_REQUEST ['_order'];
		}
		else{
			$sorder = "id";
		}
	
		switch($sorder){
			case "name":
			case "cate_id":
				$order =$sorder;
				break;
			default :
				$order = $sorder;
				break;
		}
	
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		}
		else{
			$sort = "desc";
		}
	
		//开始加载搜索条件
		$condition =" 1=1 ";
	
		if(trim($_REQUEST['name'])!='')
		{
			$condition .= " and d.name like '%".trim($_REQUEST['name'])."%'";
		}
	
		if(trim($_REQUEST['user_name'])!='')
		{
			$condition .= " and u.user_name like '%".trim($_REQUEST['user_name'])."%'  ";
		}
	
		//type 0所有 1已分配管理员   2已分配管理员或客服  
		$type = isset($_REQUEST['type']) ? intval($_REQUEST['type']) : 0;
		
		if($type == 0){
			
		}elseif($type == 1){
			$condition .= " AND d.admin_id != 0 ";
		}else{
			$condition .= " AND d.admin_id != 0 or d.customers_id != 0  ";
		}
		$this->assign ( 'type', $type );
		
		//分类
		$cate_tree = M("DealCate")->where('is_delete = 0')->findAll();
		$cate_tree = D("DealCate")->toFormatTree($cate_tree,'name');
		$this->assign("cate_tree",$cate_tree);
		
		//管理员名
		$admin_id = isset($_REQUEST['admin_id']) ? intval($_REQUEST['admin_id']) : 0;
		$this->assign ( 'admin_id', $admin_id );
		if($admin_id != 0){
			$condition .= " AND d.admin_id = ".$admin_id;
		}
		$admin_list = M("Admin")->where('is_department = 0')->findAll();
		$this->assign("admin_list",$admin_list);
		
		
		//客服名
		$customer_id = isset($_REQUEST['customer_id']) ? intval($_REQUEST['customer_id']) : 0;
		$this->assign ( 'customer_id', $customer_id );
		if($customer_id != 0){
			$condition .= " AND d.customers_id = ".$customer_id;
		}
		$customer_list = M("Customer")->where('is_delete = 0 and is_effect= 1')->findAll();
		$this->assign("customer_list",$customer_list);
		
		if(intval($_REQUEST['cate_id'])>0)
		{
			require_once APP_ROOT_PATH."system/utils/child.php";
			$child = new Child("deal_cate");
			$cate_ids = $child->getChildIds(intval($_REQUEST['cate_id']));
			$cate_ids[] = intval($_REQUEST['cate_id']);
			$condition .=" and d.cate_id in (".implode(",",$cate_ids).") ";
		}
	
		$sql_count = " SELECT count(*) FROM ".DB_PREFIX."deal d left join ".DB_PREFIX."user u on d.user_id = u.id   WHERE $condition ";
		$rs_count = $GLOBALS['db']->getOne($sql_count);
		$list = array();
	
		if($rs_count > 0){
	
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $rs_count, $listRows );
	
			$sql_list =  " SELECT d.*,a.adm_name as admin_name,c.name as customer_name FROM ".DB_PREFIX."deal d left join ".DB_PREFIX."admin a on d.admin_id = a.id left join ".DB_PREFIX."customer c on c.id = d.customers_id  WHERE $condition ORDER BY $order $sort LIMIT ".$p->firstRow . ',' . $p->listRows;
	
			$list = $GLOBALS['db']->getAll($sql_list);
	
			$page = $p->show();
			$this->assign ( "page", $page );
	
		}
	
		$sortImg = $sort; //排序图标
		$sortAlt = $sort == 'desc' ? l("ASC_SORT") : l("DESC_SORT"); //排序提示
		$sort = $sort == 'desc' ? 1 : 0; //排序方式
	
		$this->assign ( 'sort', $sort );
		$this->assign ( 'order', $sorder );
		$this->assign ( 'sortImg', $sortImg );
		$this->assign ( 'sortType', $sortAlt );
	
		$this->assign("list",$list);
		$this->display ();
		return;
	}
	
	
}
?>