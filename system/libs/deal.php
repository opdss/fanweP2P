<?php 
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

define("DEAL_OUT_OF_STOCK",4); //库存不足
define("DEAL_ERROR_MIN_USER_BUY",5); //用户最小购买数不足
define("DEAL_ERROR_MAX_USER_BUY",6); //用户最大购买数超出

define("EXIST_DEAL_COUPON_SN",1);  //团购券序列号已存在
	/**
	 * 公用生成团购券的函数
	 * 用于环境
	 * 1. 自动发放的团购券，即有$order_item_id, 将生成 order_id, order_deal_id, 且user_id与order_id相关数据同步，begin_time与end_time也与deal的同步
	 * 2. 无自定义sn，由系统自动生成，以deal_id数据的code为前缀
	 * 3. 手动生成，指定user_id, is_valid, sn, password,begin_time,end_time, 无order_id与相关数据 * 
	 */
	function add_coupon($deal_id,$user_id,$is_valid=0,$sn='',$password='',$begin_time=0,$end_time=0,$order_item_id=0,$order_id=0 )
	{
		$res = array('status'=>1,'info'=>'',$data=''); //用于返回的结果集
		$coupon_data = array();
		$deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id =".$deal_id);
		//自动发券
		if($order_id>0)
		{
			$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id);			
			$coupon_data['user_id'] = $order_info['user_id'];
			$coupon_data['order_id'] = $order_info['id'];
			$coupon_data['order_deal_id'] = $order_item_id;
		}
		else
		{
			$coupon_data['user_id'] = $user_id;
		}
		
		if($begin_time == 0)
		{
			$coupon_data['begin_time'] = $deal_info['coupon_begin_time'];
		}
		else
		{
			$coupon_data['begin_time'] = $begin_time;
		}
		
		if($end_time == 0)
		{
			$coupon_data['end_time'] = $deal_info['coupon_end_time'];
		}
		else
		{
			$coupon_data['end_time'] = $end_time;
		}
		
		if($sn!='')
		{
			if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon where sn='".$sn."'")>0)
			{
				$res['status'] = 0;
				$res['info'] = EXIST_DEAL_COUPON_SN;
				return $res;
			}
			$coupon_data['sn'] = $sn;
		}
		else
		{
			$coupon_data['sn'] = $deal_info['code'].$deal_id.rand(100000,999999);
		}
		
		if($password!='')
		{
			$coupon_data['password'] = $password;
		}
		else
		{
			$password = unpack('H8',str_shuffle(md5(uniqid())));
			$password = $password[1];
			$coupon_data['password'] = $password;
		}
		
		$coupon_data['is_valid'] = $is_valid;
		$coupon_data['deal_id'] = $deal_id;
		$coupon_data['supplier_id'] = $deal_info['supplier_id'];
		
		while($GLOBALS['db']->autoExecute(DB_PREFIX."deal_coupon",$coupon_data,'INSERT','','SILENT')==false)
		{
			$coupon_data['sn'] = $deal_info['code'].$deal_id.rand(100000,999999);
		}
		
		$res['data'] = $coupon_data;
		return $res;
			
	}


/**
 * 检测团购的时间状态
 * $id 团购ID
 * 
 */
function check_deal_time($id)
{
	$deal_info = get_deal($id);
	$now = TIME_UTC;
	
			
	//开始验证团购时间
	if($deal_info['begin_time']!=0)
	{
		//有开始时间
		if($now<$deal_info['begin_time'])
		{		
			$result['status'] = 0;
			$result['data'] = DEAL_NOTICE;  //未上线
			$result['info'] = $deal_info['sub_name'];
			return $result;
		}
	}
	

			
	if($deal_info['end_time']!=0)
	{
		//有结束时间
		if($now>=$deal_info['end_time'])
		{
			$result['status'] = 0;
			$result['data'] = DEAL_HISTORY;  //过期
			$result['info'] = $deal_info['sub_name'];
			return $result;
		}
	}
	//验证团购时间
	
	$result['status'] = 1;
	$result['info'] = $deal_info['name'];
	return $result;	
}

/**
 * 检测团购的数量状态
 * $id 团购ID
 * $number 数量
 */
function check_deal_number($id,$number = 0)
{
	$id = intval($id);
	$deal_info = load_auto_cache("cache_deal_cart",array("id"=>$id));
	
	/*验证数量*/	
	//定义几组需要的数据
	//1. 本团购记录下的购买量
	$deal_buy_count = intval($GLOBALS['db']->getOne("select buy_count from ".DB_PREFIX."deal where id=".$id));
	//2. 本团购当前会员的购物车中数量
	$deal_user_cart_count = intval($GLOBALS['db']->getOne("select sum(number) from ".DB_PREFIX."deal_cart where session_id='".es_session::id()."' and deal_id =".$id." and user_id = ".intval($GLOBALS['user_info']['id'])));
	//3. 本团购当前会员已付款的数量
	$deal_user_paid_count = intval($GLOBALS['db']->getOne("select sum(oi.number) from ".DB_PREFIX."deal_order_item as oi left join ".DB_PREFIX."deal_order as o on oi.order_id = o.id where o.user_id = ".intval($GLOBALS['user_info']['id'])." and o.pay_status = 2 and oi.deal_id = ".$id." and o.is_delete = 0"));
	//4. 本团购当前会员未付款的数量
	$deal_user_unpaid_count = intval($GLOBALS['db']->getOne("select sum(oi.number) from ".DB_PREFIX."deal_order_item as oi left join ".DB_PREFIX."deal_order as o on oi.order_id = o.id where o.user_id = ".intval($GLOBALS['user_info']['id'])." and o.pay_status <> 2 and oi.deal_id = ".$id." and o.is_delete = 0"));

	$sql = "select count(*) from ".DB_PREFIX."deal_cart where session_id = '".es_session::id()."' and deal_id = ".$id." and user_id = ".intval($GLOBALS['user_info']['id'])." and number <= 0";
	$invalid_count = intval($GLOBALS['db']->getOne($sql));
	if($invalid_count>0)
	{
		$result['status'] = 0;
		$result['data'] = DEAL_ERROR_MIN_USER_BUY;  //用户最小购买数不足
		$result['info'] = $deal_info['sub_name']." ".sprintf($GLOBALS['lang']['DEAL_USER_MIN_BOUGHT'],1);
		return $result;
	}
	
	if($deal_user_cart_count + $deal_buy_count + $deal_user_unpaid_count + $number > $deal_info['max_bought'] && $deal_info['max_bought'] > 0)
	{		
		$result['status'] = 0;
		$result['data'] = DEAL_OUT_OF_STOCK;  //库存不足
		$result['info'] = $deal_info['sub_name']." ".sprintf($GLOBALS['lang']['DEAL_MAX_BOUGHT'],$deal_info['max_bought']);
		return $result;
	}
	if($deal_user_cart_count + $deal_user_paid_count + $number < $deal_info['user_min_bought'] && $deal_info['user_min_bought'] > 0&&ACTION_NAME!='addcart')
	{
		$result['status'] = 0;
		$result['data'] = DEAL_ERROR_MIN_USER_BUY;  //用户最小购买数不足
		$result['info'] = $deal_info['sub_name']." ".sprintf($GLOBALS['lang']['DEAL_USER_MIN_BOUGHT'],$deal_info['user_min_bought']);
		return $result;
	}
	if($deal_user_cart_count + $deal_user_paid_count + $deal_user_unpaid_count + $number > $deal_info['user_max_bought'] && $deal_info['user_max_bought'] > 0)
	{
		$result['status'] = 0;
		$result['data'] = DEAL_ERROR_MAX_USER_BUY;  //用户最大购买数超出
		$result['info'] = $deal_info['sub_name']." ".sprintf($GLOBALS['lang']['DEAL_USER_MAX_BOUGHT'],$deal_info['user_max_bought']);
		return $result;
	}
	/*验证数量*/
	
	$result['status'] = 1;
	$result['info'] = $deal_info['sub_name'];
	return $result;	
}


/**
 * 检测团购的属性数量状态
 * $id 团购ID
 * $attr_setting 属性组合的字符串
 * $number 数量
 */
function check_deal_number_attr($id,$attr_setting,$number=0)
{
	$id = intval($id);	
	$deal_info = load_auto_cache("cache_deal_cart",array("id"=>$id));
	
	$attr_stock_cfg = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."attr_stock where deal_id = ".$id." and locate(attr_str,'".$attr_setting."') > 0 ");

	
	$stock_setting = intval($attr_stock_cfg['stock_cfg']);
	$stock_attr_setting = $attr_stock_cfg['attr_str'];
	// 获取到当前规格的库存
		
	/*验证数量*/	
	//定义几组需要的数据
	//1. 本团购记录下的购买量
	$deal_buy_count = intval($GLOBALS['db']->getOne("select sum(oi.number) from ".DB_PREFIX."deal_order_item as oi left join ".DB_PREFIX."deal_order as o on oi.order_id = o.id where o.pay_status = 2 and oi.deal_id = ".$id." and o.is_delete = 0 and oi.attr_str like '%".$stock_attr_setting."%' and o.after_sale = 0"));
	//2. 本团购当前会员的购物车中数量
	$deal_user_cart_count = intval($GLOBALS['db']->getOne("select sum(number) from ".DB_PREFIX."deal_cart where session_id='".es_session::id()."' and deal_id =".$id." and user_id = ".intval($GLOBALS['user_info']['id'])." and attr_str like '%".$stock_attr_setting."%'"));
	//3. 本团购当前会员未付款的数量
	$deal_user_unpaid_count = intval($GLOBALS['db']->getOne("select sum(oi.number) from ".DB_PREFIX."deal_order_item as oi left join ".DB_PREFIX."deal_order as o on oi.order_id = o.id where o.user_id = ".intval($GLOBALS['user_info']['id'])." and o.pay_status <> 2 and oi.deal_id = ".$id." and o.is_delete = 0 and oi.attr_str like '%".$stock_attr_setting."%'"));
	

	if($deal_user_cart_count + $deal_buy_count + $deal_user_unpaid_count + $number > $stock_setting && $stock_setting > 0)
	{		
		$result['status'] = 0;
		$result['data'] = DEAL_OUT_OF_STOCK;  //库存不足
		$result['info'] = $deal_info['sub_name'].$stock_attr_setting." ".sprintf($GLOBALS['lang']['DEAL_MAX_BOUGHT'],$stock_setting);
		$result['attr'] = $stock_attr_setting;
		return $result;
	}
	/*验证数量*/
	
	$result['status'] = 1;
	$result['info'] = $deal_info['sub_name'];	
	return $result;	

}
?>