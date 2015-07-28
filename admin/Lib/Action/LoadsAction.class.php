<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class LoadsAction extends CommonAction{

    function index() {
    	$extwhere ="" ;
    	$this->getlist($extwhere);
		$this->display();
    }
    
    function hand() {
    	$extwhere =" and dl.is_auto=0 " ;
    	$this->getlist($extwhere);
		$this->display("index");
    }
    
    function auto() {
    	$extwhere =" and dl.is_auto=1 " ;
    	$this->getlist($extwhere);
		$this->display("index");
    }
    
    function success() {
    	$extwhere =" and dl.is_repay=0 " ;
    	$this->getlist($extwhere);
		$this->display("index");
    }
    
    function failed() {
    	$extwhere =" and dl.is_repay=1 " ;
    	$this->getlist($extwhere);
		$this->display("index");
    }
    
    private function getlist($extwhere){
		//分类
		$cate_tree = M("DealCate")->where('is_delete = 0')->findAll();
		$cate_tree = D("DealCate")->toFormatTree($cate_tree,'name');
		$this->assign("cate_tree",$cate_tree);
		
    	$conditon = " 1=1 ";
		//开始加载搜索条件
		if(intval($_REQUEST['deal_id'])>0)
		{
			$conditon .= " and dl.deal_id = ".intval($_REQUEST['deal_id']);
		}

		if(intval($_REQUEST['cate_id'])>0)
		{
			require_once APP_ROOT_PATH."system/utils/child.php";
			$child = new Child("deal_cate");
			$cate_ids = $child->getChildIds(intval($_REQUEST['cate_id']));
			$cate_ids[] = intval($_REQUEST['cate_id']);
			$conditon .= " and d.cate_id in (".implode(",",$cate_ids).")";
		}
		
		
		if(trim($_REQUEST['user_name'])!='')
		{
			$sql  ="select group_concat(id) from ".DB_PREFIX."user where user_name like '%".trim($_REQUEST['user_name'])."%'";
			
			$ids = $GLOBALS['db']->getOne($sql);
			if($ids)
				$conditon .= " and dl.user_id in ($ids) ";
			else
				$conditon .= " and dl.user_id = 0 ";
		}
		
		if(intval($_REQUEST['user_id']) > 0){
			$sql  ="select user_name from ".DB_PREFIX."user where id='".intval($_REQUEST['user_id'])."'";
			$_REQUEST['user_name'] = $GLOBALS['db']->getOne($sql);
			$conditon .= " and dl.user_id = ".intval($_REQUEST['user_id']);
		}
		
		$begin_time  = trim($_REQUEST['begin_time'])==''?0:to_timespan($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time'])==''?0:to_timespan($_REQUEST['end_time']);
		if($begin_time > 0 || $end_time > 0){
			if($end_time==0)
			{
				$conditon .= " and dl.create_time >= $begin_time ";	
			}
			else
				$conditon .= " and dl.create_time between  $begin_time and $end_time ";	
		}
		
		$count = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."deal_load dl LEFT JOIN ".DB_PREFIX."deal d ON d.id =dl.deal_id where $conditon $extwhere ORDER BY dl.id DESC ");
		if (! empty ( $_REQUEST ['listRows'] )) {
			$listRows = $_REQUEST ['listRows'];
		} else {
			$listRows = '';
		}
		$p = new Page ( $count, $listRows );
		if($count>0){
			$list = $GLOBALS['db']->getAll("SELECT dl.*,d.name,d.repay_time,d.repay_time_type,d.loantype,d.rate,d.cate_id FROM ".DB_PREFIX."deal_load dl LEFT JOIN ".DB_PREFIX."deal d ON d.id =dl.deal_id where $conditon $extwhere ORDER BY dl.id DESC  limit  ".$p->firstRow . ',' . $p->listRows);
			$this->assign("list",$list);
		}
		$page = $p->show();
		$this->assign ( "page", $page );
		
	}
}
?>