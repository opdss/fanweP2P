<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/deal.php';
require APP_ROOT_PATH.'app/Lib/uc_goods_func.php';
class goods_informationModule extends SiteBaseModule
{
	public function index(){
		$id = intval($_REQUEST['id']);
		$user_id= intval($GLOBALS['user_info']['id']);
		if($id==0){
			app_redirect(url("index")); 
		}
		
		$goods = get_goods_info($id,$user_id);
		if(!$goods){
			app_redirect(url("index")); 
		}
		if($goods['goods_type_id'] >0){
			$result = get_goods_attr($goods['goods_type_id'],$id);
			$GLOBALS['tmpl']->assign("json_attr_stock",json_encode($result['attr_stock']));
			$GLOBALS['tmpl']->assign("has_stock",$result['has_stock']);
			$GLOBALS['tmpl']->assign("goods_type_attr",$result['goods_type_attr']);
		}else{
			$has_stock=1;
			$GLOBALS['tmpl']->assign("has_stock",$has_stock);
		}
		$GLOBALS['tmpl']->assign("goods",$goods);
		$GLOBALS['tmpl']->display("page/goods_information.html");
	}
	
	public function address(){
		$goods_id = intval($_REQUEST['id']);
		$user_id = intval($GLOBALS['user_info']['id']);
		$number =  intval($_REQUEST['number']); 
				
		if($user_id ==0)
		{
			$data['info']="请先登录！";
			$data['status'] = 2;
			ajax_return($data);
		}
		
		if($number <= 0)
		{
			$return['info']="请输入正确的兑换数量";
			$return['status'] = 0;
			ajax_return($return);
		}
		
		$sql = "SELECT * from ".DB_PREFIX."user_address where user_id = ".$user_id ;
		$user_address = $GLOBALS['db']->getAll($sql);
		
		$GLOBALS['tmpl']->assign("user_address",$user_address);
		$GLOBALS['tmpl']->assign("goods_id",$goods_id);
		$GLOBALS['tmpl']->assign("number",$number);
		$html = $GLOBALS['tmpl']->fetch("page/goods_address.html");
		
		$data['info'] = $html;
		$data['status'] = 1;
		ajax_return($data);
	}
	
	public function doexchange(){
		
		$user_id = $GLOBALS['user_info']['id'];
		$address_id = intval($_REQUEST['address_id']);
		$add_addr = intval($_REQUEST['add_addr']);
		$goods_id = intval($_REQUEST['goods_id']);
		$number = intval($_REQUEST['number']);
		$attr = $_REQUEST['attr'];
		
		if($user_id==0){
			$return['info']="请先登录！";
			$return['status'] = 2;
			ajax_return($return);
		}
		
		if($number <= 0)
		{
			$return['info']="请输入正确的兑换数量";
			$return['status'] = 0;
			ajax_return($return);
		}
		
		$return = array("jump"=>url("index","uc_goods_order"));
		
		$goods = get_goods_info($goods_id,$user_id);
		if($goods <= 0)
		{
			$return['info']="商品不存在";
			$return['status'] = 0;
			ajax_return($return);
		}
		if($number > $goods['user_can_buy_number']){
			$return['info']="超出你所能兑换的数量";
			$return['status'] = 0;
			ajax_return($return);
		}
		//判断是否有属性库存
		$attr_stocks = get_attr_stock($goods,$number,$goods_id,$attr);
		
		if($add_addr==1 && $goods['is_delivery'] == 1){
			$user_name = strim($_REQUEST['user_name']);//收货名称
			if($user_name == ""){
				$return['info']="请输入收货人姓名";
				$return['status'] = 0;
				ajax_return($return);
			}
			$user_phone = strim($_REQUEST['user_phone']);
			if($user_phone == ""){
				$return['info']="请输入收货人电话";
				$return['status'] = 0;
				ajax_return($return);
			}
			$user_address = strim($_REQUEST['user_address']);
			if($user_address == ""){
				$return['info']="请输入收货人地址";
				$return['status'] = 0;
				ajax_return($return);
			}
			//提交地址
			$addr_data['user_id'] = $user_id;
			$addr_data['name'] = $user_name;
			$addr_data['address'] = $user_address;
			$addr_data['phone'] = $user_phone;
			$addr_data['is_default'] = 1;
			$GLOBALS['db']->autoExecute(DB_PREFIX."user_address",$addr_data,"INSERT");
			$address_id = $GLOBALS['db']->insert_id();
			if(intval($address_id) == 0){
				$return['info'] = "地址保存失败";
				$return['status'] = 0;
				ajax_return($return);
			}
		}
		
		//库存属性 star
		$order_data = array();
		if(isset($temp_attr_stock[$attr_str])){
			$GLOBALS['db']->query("UPDATE ".DB_PREFIX."goods_attr_stock SET buy_count = buy_count +". $number ." WHERE id=".$temp_attr_stock[$attr_str]['id']);
			$order_data['attr_stock_id'] = $temp_attr_stock[$attr_str]['id'];
		}
		
		if($attr){
			$order_data['attr'] = serialize($attr);
		}
		
		//库存属性 end 
		
		$user_info = $GLOBALS['user_info'];

		if(($goods['score']+$goods_attr_scoresum)*$number > $user_info['score']){
			$return['info'] = "积分不足，无法兑换";
			$return['status'] = 0;
			ajax_return($return);
		}
		
		$order_data['order_sn'] = to_date(get_gmtime(),"Ymdhis").rand(10,99);
		$order_data['goods_id'] = $goods_id;
		$order_data['goods_name'] = $goods['name'];
		$order_data['score'] = $goods['score'];
		$order_data['total_score'] = ($goods['score']+$goods_attr_scoresum)*$number;  //总积分 =（积分+属性积分）*数量     
	
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
			$return['info'] = "兑换成功";
			$return['status'] = 1;
			
		}else{
			$return['info'] = "兑换失败";
			$return['status'] = 0;
		}
		
		ajax_return($return);
	}
	
}
?>
