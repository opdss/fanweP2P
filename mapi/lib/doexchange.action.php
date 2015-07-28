<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class doexchange
{
	public function index(){
		
		$root = array();
		$email = strim($GLOBALS['request']['email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['pwd']);//密码
		
		$user_id = intval($GLOBALS['user_info']['id']);
		$address_id = intval($GLOBALS['request']['address_id']);
		$goods_id = intval($GLOBALS['request']['id']);
		$number = intval($GLOBALS['request']['number']);
		//检查用户,用户密码
		$user = user_check($email,$pwd);
		$root['user_info'] = $user;
		$user_id  = intval($user['id']);
		if ($user_id >0){
			require APP_ROOT_PATH.'app/Lib/deal.php';
			require APP_ROOT_PATH.'app/Lib/uc_goods_func.php';
			
			if($number <= 0)
			{
				$root['show_err']="请输入正确的兑换数量";
				$root['status'] = 0;
			}
			
			$return = array("jump"=>url("index","uc_goods_order"));
			
			$goods = get_goods_info($goods_id,$user_id);
			if($goods <= 0)
			{
				$root['show_err']="商品不存在";
				$root['status'] = 0;
			}
			if($number > $goods['user_can_buy_number']){
				$root['show_err']="超出你所能兑换的数量";
				$root['status'] = 0;
			}
			
			if($address_id == 0 || $address_id == "" ){
				$root['show_err']="请填写收货信息";
				$root['status'] = 0;
				output($root);
			}
			if($goods_id == 0 || $goods_id == "" ){
				$root['show_err']="没有该物品";
				$root['status'] = 0;
				output($root);
			}
			
			$user_info = $GLOBALS['user_info'];
	
			if(($goods['score'])*$number > $user_info['score']){
				$root['show_err'] = "积分不足，无法兑换";
				$root['status'] = 0;
			}
			
			$order_data['order_sn'] = to_date(get_gmtime(),"Ymdhis").rand(10,99);
			$order_data['goods_id'] = $goods_id;
			$order_data['goods_name'] = $goods['name'];
			$order_data['score'] = $goods['score'];
			$order_data['total_score'] = ($goods['score'])*$number;  //总积分 =（积分+属性积分）*数量     
		
			$order_data['number'] = $number; 		//数量   
			$order_data['delivery_sn'] = "";  		//快递单号
			$order_data['order_status'] = 0; 		//(0:未发货;1:已发货)
			$order_data['user_id'] = $user_id;
			$order_data['ex_time'] = TIME_UTC;
			$order_data['ex_date'] = to_date(TIME_UTC,"Y-m-d");
			
			$order_data['delivery_time'] = "";
			$order_data['delivery_date'] = "";
			$order_data['is_delivery'] = $goods['is_delivery'];
			
			
			require_once APP_ROOT_PATH."system/libs/user.php";
					
			
			if(intval($goods['is_delivery']) == 1)//判断是否需要配送
			{
				$sql = "SELECT * from ".DB_PREFIX."user_address where id = ".$address_id ;
				$address_info = $GLOBALS['db']->getRow($sql);
				//提交订单
				$order_data['delivery_addr'] = strim($address_info['address']);
				$order_data['delivery_tel'] = strim($address_info['phone']);
				$order_data['delivery_name'] = strim($address_info['name']) ;
			}
			
			$GLOBALS['db']->autoExecute(DB_PREFIX."goods_order",$order_data,"INSERT");
			
			if($GLOBALS['db']->affected_rows()){
				//订单提交成功,增加购买用户数和扣除积分
				$goods_data['buy_number'] = $goods['buy_number']+$number;
				$goods_data['invented_number'] = $goods['invented_number']+1;
				$GLOBALS['db']->autoExecute(DB_PREFIX."goods",$goods_data,"UPDATE","id=".$goods['id']);
				$msg = '积分兑换商品<a href="'.url("index","goods_information#index",array("id"=>$goods['id'])).'" target="_blank">'.$order_data['goods_name'].'</a>';
				modify_account(array('score'=>-$order_data['total_score']),$GLOBALS['user_info']['id'],$msg,22);
				$root['show_err'] = "兑换成功";
				$root['status'] = 1;
				
			}else{
				$root['show_err'] = "兑换失败";
				$root['status'] = 0;
			}
			
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "商品详情";
		output($root);		
	}
}
?>
