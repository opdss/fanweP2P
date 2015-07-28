<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/uc_func.php';
class uc_invest
{
	public function index(){
		
		$root = array();
		
		$email = strim($GLOBALS['request']['email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['pwd']);//密码
		$page = intval($GLOBALS['request']['page']);
		
		$mode = strim($GLOBALS['request']['mode']);//index,invite,ing,over,bad
		
		//检查用户,用户密码
		$user = user_check($email,$pwd);
		$user_id  = intval($user['id']);
		if ($user_id >0){
			$root['user_login_status'] = 1;
			
			$status = intval($GLOBALS['request']['status']);
			$root['status'] = $status;
			/*
			 * $status 1 进行中,2还款中,3已还清,4满标,5流标,0或其他 默认为全部
			 */
			if(isset($status) && $status=="1"){
				$result = getInvestList($mode = "in",$user_id,$page,$email,$user['user_pwd']);	//进行中
			}elseif(isset($status) && $status=="2"){
				$result = getInvestList($mode = "ing",$user_id,$page,$email,$user['user_pwd']); //还款中
			}elseif(isset($status) && $status=="3"){
				$result = getInvestList($mode = "over",$user_id,$page,$email,$user['user_pwd']); //已还清
			}elseif(isset($status) && $status=="4"){
				$result = getInvestList($mode = "full",$user_id,$page,$email,$user['user_pwd']); //满标
			}elseif(isset($status) && $status=="5"){
				$result = getInvestList($mode = "flow",$user_id,$page,$email,$user['user_pwd']); //流标
			}else{
				$result = getInvestList($mode,$user_id,$page,$email,$user['user_pwd']);
			}
			
			 //修改描述字段
			$list_coyp=$result['list'];
			 foreach ($list_coyp  as $key => $value) {

			$list_coyp[$key]['description_ios']=$list_coyp[$key]['description'];
			unset($list_coyp[$key]['descreption']);
			}
			$root['item'] = $list_coyp;
		
			$root['page'] = array("page"=>$page,"page_total"=>ceil($result['count']/app_conf("DEAL_PAGE_SIZE")),"page_size"=>app_conf("DEAL_PAGE_SIZE"));
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "我的投资";
		output($root);		
	}
}
?>
