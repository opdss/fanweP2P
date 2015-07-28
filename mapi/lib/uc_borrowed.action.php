<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_borrowed
{
	public function index(){
		
		$root = array();
		
		$email = strim($GLOBALS['request']['email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['pwd']);//密码
		
		$page = intval($GLOBALS['request']['page']);
		
		
		//检查用户,用户密码
		$user = user_check($email,$pwd);
		$user_id  = intval($user['id']);
		if ($user_id >0){
			require APP_ROOT_PATH.'app/Lib/deal.php';
			
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
			
			if($page==0)
				$page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
			$status = intval($GLOBALS['request']['status']);
			$root['status'] = $status;
			/*
			 * $status 1 进行中,2还款中,3已还清,4满标,5流标,0或其他 默认为全部
			 */
			if(isset($status) && $status=="1"){
				$result = get_deal_list($limit,0,"deal_status='1' and user_id=".$user_id ,"id DESC"); //进行中
			}elseif(isset($status) && $status=="2"){
				$result = get_deal_list($limit,0,"deal_status='4' and user_id=".$user_id ,"id DESC"); //还款中
			}elseif(isset($status) && $status=="3"){
				$result = get_deal_list($limit,0,"deal_status='5' and user_id=".$user_id ,"id DESC"); //已还清
			}elseif(isset($status) && $status=="4"){
				$result = get_deal_list($limit,0,"deal_status='2' and user_id=".$user_id ,"id DESC"); //满标
			}elseif(isset($status) && $status=="5"){
				$result = get_deal_list($limit,0,"deal_status='3' and user_id=".$user_id ,"id DESC"); //流标
			}else{
				$result = get_deal_list($limit,0,"user_id=".$user_id,"id DESC");
			}
		
			 
			$root['item'] = $result['list'];
			$root['page'] = array("page"=>$page,"page_total"=>ceil($result['count']/app_conf("PAGE_SIZE")),"page_size"=>app_conf("PAGE_SIZE"));
			
			
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "我的借款";
		output($root);		
	}
}
?>
