<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/deal.php';
class deal_bid
{
	public function index(){
		
		$root = array();
		
		$email = strim($GLOBALS['request']['email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['pwd']);//密码
		
		//检查用户,用户密码
		$user = user_check($email,$pwd);
		$user_id  = intval($user['id']);
		if ($user_id >0){
			$root['user_login_status'] = 1;
			
			$id = intval($GLOBALS['request']['id']);
			$deal = get_deal($id);
			if($deal){			
				if($deal['user_id'] == $GLOBALS['user_info']['id']){				
					$root['response_code'] = 0;
					$root['show_err'] = $GLOBALS['lang']['CANT_BID_BY_YOURSELF'];  //当response_code != 0 时，返回：错误内容
				}else{				
					$root['deal'] = $deal;
					$root['user_money'] = $user['money'];//用户金额
					$root['user_money_format'] = format_price($user['money']);//用户金额
					$root['response_code'] = 1;
				}				
			}else{
				$root['response_code'] = 0;
			}
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		output($root);		
	}
}
?>
