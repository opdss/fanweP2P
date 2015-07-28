<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class goods_address
{
	public function index(){
		
		$root = array();
		
		$email = strim($GLOBALS['request']['email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['pwd']);//密码
		$goods_id = intval($GLOBALS['request']['id']);
		$number =  intval($GLOBALS['request']['number']); 
		$user_id = intval($GLOBALS['user_info']['id']);
		$page = intval($GLOBALS['request']['page']);
		
		//检查用户,用户密码
		$user = user_check($email,$pwd);
		$user_id  = intval($user['id']);
		if ($user_id >0){
			if($page==0)
				$page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
					
			$sql = "SELECT * from ".DB_PREFIX."user_address where user_id = '$user_id' limit $limit "  ;
			$user_address = $GLOBALS['db']->getAll($sql);
			$sql_count = "SELECT COUNT(*) from ".DB_PREFIX."user_address where user_id = '$user_id' limit $limit "  ;
			$count = $GLOBALS['db']->getOne($sql_count);
			
			$root['page'] = array("page"=>$page,"page_total"=>ceil($count/app_conf("DEAL_PAGE_SIZE")),"page_size"=>app_conf("DEAL_PAGE_SIZE"));
			$root['user_address'] = $user_address;
			$root['goods_id'] = $goods_id;
			$root['number'] = $number;
								
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "收货地址";
		output($root);		
	}
}
?>
