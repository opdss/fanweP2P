<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_goods_order
{
	public function index(){
		
		$root = array();
		
		$email = strim($GLOBALS['request']['email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['pwd']);//密码
		
		$id = intval($GLOBALS['request']['id']);
		//$user_id = intval($GLOBALS['user_info']['id']);
		$page = intval($GLOBALS['request']['page']);
		//检查用户,用户密码
		$user = user_check($email,$pwd);
		$user_id  = intval($user['id']);
		if ($user_id >0){
			require APP_ROOT_PATH.'app/Lib/deal.php';
			require APP_ROOT_PATH.'app/Lib/uc_goods_func.php';
			
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
			
			$order_sn = strim($_REQUEST['order_sn']);
			$time = isset($_REQUEST['time']) ?  to_date(to_timespan($_REQUEST['time'],"Y-m-d"),"Y-m-d") : "";
			
			if($page==0)
				$page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
			
			$condition = " 1=1 ";
			if($order_sn != "")
			$condition.=" and go.order_sn = '".$order_sn."' ";
			
			if($time!=""){
				$condition.=" and go.ex_date = '".$time."' ";
				$root['time'] = $time;
			}
			
			$root['user_id'] = $user_id;
			
			$orders = get_order($limit,$user_id,$condition);
			
			$root['page'] = array("page"=>$page,"page_total"=>ceil($orders['count']/app_conf("DEAL_PAGE_SIZE")),"page_size"=>app_conf("DEAL_PAGE_SIZE"));
			$root['order_sn'] = $order_sn;
			$root['order_info'] = $orders['list'];
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "我的兑现";
		output($root);		
	}
}
?>
