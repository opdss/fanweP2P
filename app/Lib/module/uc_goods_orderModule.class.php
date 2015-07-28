<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/deal.php';
require APP_ROOT_PATH.'app/Lib/uc.php';
require APP_ROOT_PATH.'app/Lib/uc_goods_func.php';
class uc_goods_orderModule extends SiteBaseModule
{
	public function index(){
		$order_sn = strim($_REQUEST['order_sn']);
		$time = isset($_REQUEST['time']) ?  to_date(to_timespan($_REQUEST['time'],"Y-m-d"),"Y-m-d") : "";
		
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		
		$condition = " 1=1 ";
		if($order_sn != "")
		$condition.=" and go.order_sn = '".$order_sn."' ";
		
		if($time!=""){
			$condition.=" and go.ex_date = '".$time."' ";
			$GLOBALS['tmpl']->assign('time',$time);
		}
		$user_id = $GLOBALS['user_info']['id'];
		
		$result = get_order($limit,$user_id,$condition);
		
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		$GLOBALS['tmpl']->assign("order_sn",$order_sn);
		$GLOBALS['tmpl']->assign("order_info",$result['list']);
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_goods_order.html");
		
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	
	
	public function delete_order(){
	
		$id = intval($_REQUEST['id']);
		$total_score = intval($_REQUEST['total_score']);

		$count_sql = "SELECT count(*) from ".DB_PREFIX."goods_order where id=".$id." and order_status =0  and user_id=".$GLOBALS['user_info']['id'];
		$order_count = $GLOBALS['db']->getOne($count_sql);
		if(!$order_count)
		{
			showErr('订单取消失败',0,url("index","uc_goods_order"));
		}
		require_once APP_ROOT_PATH."system/libs/user.php";
		modify_account(array('score'=>$total_score),$GLOBALS['user_info']['id'],"用户取消订单积分返还",22);
		
	
		$GLOBALS['db']->query("update ".DB_PREFIX."goods_order set order_status = 3 where id = ".$id);
		
		if($GLOBALS['db']->affected_rows() == 0)
		{
			showErr('订单取消失败',0,url("index","uc_goods_order"));
		}
		else{
			showSuccess('订单取消成功',0,url("index","uc_goods_order"));
		}
	
	}
	
	
}
?>