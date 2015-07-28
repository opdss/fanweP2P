<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/uc.php';

class uc_repay_planModule extends SiteBaseModule
{		
	public function index()
	{
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		$user_id = $GLOBALS['user_info']['id'];
		$status = isset($_REQUEST['stauts']) ? intval($_REQUEST['stauts']) : 3;
		
		$time = isset($_REQUEST['time']) ?  to_timespan($_REQUEST['time'],"Ymd") : "";
		$deal_name = strim($_REQUEST['deal_name']);
		$condition = "";
		if($deal_name!=""){
			$condition.=" and d.name = '".$deal_name."' ";
			$GLOBALS['tmpl']->assign('deal_name',$deal_name);
		}
		if($time!=""){
			$condition.=" and dlr.repay_time = ".$time." ";
			$GLOBALS['tmpl']->assign('time',to_date($time,"Y-m-d"));
		}
		
		$result = getUcRepayPlan($user_id,$status,$limit,$condition);
		
		if($result['rs_count'] > 0){
			
			$page = new Page($result['rs_count'],app_conf("PAGE_SIZE"));   //初始化分页对象
			$p  =  $page->show();
			$GLOBALS['tmpl']->assign('pages',$p);
			$GLOBALS['tmpl']->assign('list',$result['list']);
		}
		
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_REPAY_PLAN']);
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_repay_plan.html");
		
				
		$GLOBALS['tmpl']->assign("status",$status);
		$GLOBALS['tmpl']->display("page/uc.html");
	}
}
?>