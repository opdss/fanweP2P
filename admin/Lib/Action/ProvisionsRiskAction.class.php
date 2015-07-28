<?php
// +----------------------------------------------------------------------
// | easethink 易想商城系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class ProvisionsRiskAction extends CommonAction{
	public function index()
	{
		$this->assign("main_title","风险准备金");
		
		if (isset ( $_REQUEST ['_order'] )) {
			$sorder = $_REQUEST ['_order'];
		}
		else{
			$sorder = "id";
		}
		
		switch($sorder){
			case "admin_name":
				$order ="user_id";
				break;
			case "amount":
				$order ="amount";
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
		
		$condition = " 1= 1 ";
		
		
		$begin_time  = !isset($_REQUEST['begin_time'])? 0 : (trim($_REQUEST['begin_time']) =="" ? 0 : to_timespan($_REQUEST['begin_time'],"Y-m-d"));
		$end_time  = !isset($_REQUEST['end_time'])? 0 : (trim($_REQUEST['end_time']) =="" ? 0 : to_timespan($_REQUEST['end_time'],"Y-m-d"));
	
		if($begin_time > 0 || $end_time > 0){
			if($end_time==0)
			{
				$condition .= " and pr.optime >= $begin_time ";
			}
			else
				$condition .= " and pr.optime between  $begin_time and $end_time ";
		}
		
		$_REQUEST['begin_time'] = to_date($begin_time ,"Y-m-d");
		$_REQUEST['end_time'] = to_date($end_time ,"Y-m-d");
		
		if(trim($_REQUEST['adm_name'])!='')
		{
			$condition .= " and a.adm_name like '%".trim($_REQUEST['adm_name'])."%'";
		}
		
		$sql_count = " SELECT count(*) FROM ".DB_PREFIX."provisions_risk pr left join ".DB_PREFIX."admin a on a.id=pr.admin_id  WHERE $condition ";
		
		$rs_count = $GLOBALS['db']->getOne($sql_count);
		
		$list = array();
		if($rs_count > 0){
		
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $rs_count, $listRows );
		
			$sql_list =  " select pr.* ,a.adm_name from ".DB_PREFIX."provisions_risk pr left join ".DB_PREFIX."admin a on a.id=pr.admin_id  WHERE $condition ORDER BY $order $sort LIMIT ".$p->firstRow . ',' . $p->listRows;
				
			$list = $GLOBALS['db']->getAll($sql_list);
			foreach($list as $k=>$v){
				//$list[$k]['type_format'] = $type_name[$v['type']];
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
	
	public function add() {
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$adm_name = $adm_session['adm_name'];
		$adm_id = intval($adm_session['adm_id']);
		$this->assign ( 'adm_name', $adm_name );
		$this->display ();
	}
	
	
	public function insert()
	{
	
		$data = M("ProvisionsRisk")->create ();
	
		$money = $GLOBALS['db']->getOne("select sum(amount) from ".DB_PREFIX."provisions_risk");
		$data['money'] = $money+$data['amount'] ;
	
		$data['optime'] = TIME_UTC;
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$adm_id = intval($adm_session['adm_id']);
		$data['admin_id'] = $adm_id;
		print_r($data);
		// 更新数据
		$list=M("ProvisionsRisk")->add ($data);
	
		if (false !== $list) {
	
			save_log($data['name'].L("INSERT_SUCCESS"),1);
			$this->assign("jumpUrl",u(MODULE_NAME."/add"));
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			$dbErr = M()->getDbError();
			save_log($data['name'].L("INSERT_FAILED").$dbErr,0);
			$this->error(L("INSERT_FAILED").$dbErr);
		}
	
	}
	
}
?>