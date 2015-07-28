<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

//购物促销接口
interface promote{
/**
 * region_id      //配送最终地区
 * delivery_id    //配送方式
 * payment        //支付ID
 * account_money  //支付余额
 * all_account_money  //是否全额支付
 * ecvsn  //代金券帐号
 * ecvpassword  //代金券密码
 * goods_list   //统计的商品列表
 * result 与返回的数据结构相同
 * $paid_account_money 订单支付时已付的余额
 * $paid_ecv_money 订单支付时已付的代金
 * 
 * 返回 array(
		'total_price'	=>	$total_price,	商品总价
		'pay_price'		=>	$pay_price,     支付费用
		'pay_total_price'		=>	$total_price+$delivery_fee+$payment_fee-$user_discount,  应付总费用
		'delivery_fee'	=>	$delivery_fee,  运费
		'delivery_info' =>  $delivery_info, 配送方式
		'payment_fee'	=>	$payment_fee,   支付手续费
		'payment_info'  =>	$payment_info,  支付方式
		'user_discount'	=>	$user_discount, 会员折扣
		'account_money'	=>	$account_money, 余额支付	
		'ecv_money'		=>	$ecv_money,		代金券金额
		'region_info'	=>	$region_info,	地区数据
		'is_delivery'	=>	$is_delivery,   是否要配送
		'return_total_score'	=>	$return_total_score,   购买返积分
		'return_total_money'	=>	$return_total_money    购买返现
		
		
	$old_result 前一个促销规则得到的结果
		
 */
function count_buy_total($region_id,$delivery_id,$payment,$account_money,$all_account_money,$ecvsn,$ecvpassword,$goods_list,$result,$paid_account_money,$paid_ecv_money,$old_result);
}
?>