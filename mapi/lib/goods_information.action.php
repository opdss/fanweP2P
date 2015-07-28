<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class goods_information
{
	public function index(){
		
		$root = array();
		
		$email = strim($GLOBALS['request']['email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['pwd']);//密码
		
		$id = intval($GLOBALS['request']['id']);
		$user_id = intval($GLOBALS['user_info']['id']);
		
		//检查用户,用户密码
		$user = user_check($email,$pwd);
		$user_id  = intval($user['id']);
//		if ($user_id >0){
			require APP_ROOT_PATH.'app/Lib/deal.php';
			require APP_ROOT_PATH.'app/Lib/uc_goods_func.php';
			
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
			
			if($id==0){
				app_redirect(url("index")); 
			}
			
			$goods = get_goods_info($id,$user_id);
			if(!$goods){
				app_redirect(url("index")); 
			}
			$root['goods_description'] = $goods['description'];
			$root['user_can_buy_number'] = $goods['user_can_buy_number'];
			if($goods['goods_type_id'] >0){
				$goods_type_attr = get_goods_attr($goods['goods_type_id'],$id);
				
				
				$root['has_stock'] = $goods_type_attr['has_stock'];

				$root['goods_type_attr'] = $goods_type_attr['goods_type_attr'];
				$root['json_attr_stock'] = json_encode($goods_type_attr['attr_stock']);
				
			}else{
				$has_stock=1;
				//$GLOBALS['tmpl']->assign("has_stock",$has_stock);
				$root['has_stock'] = $has_stock;
			}
			
			$root['goods'] = $goods;
			
			
//		}else{
//			$root['response_code'] = 0;
//			$root['show_err'] ="未登录";
//			$root['user_login_status'] = 0;
//		}
		$root['program_title'] = "商品详情";
		output($root);		
	}
}
?>
