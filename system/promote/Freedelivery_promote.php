<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------

$lang = array(
	'name'	=>	'全场免运费',
	'description'	=>	'全场所有商品团购免运费',
);
$config = array(

);

/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'Freedelivery';

    /* 名称 */
    $module['name']    = $lang['name'];
    
    /* 描述 */
    $module['description']    = $lang['description'];

	$module['lang'] = $config;
    $module['lang'] = $lang;
    return $module;
}

// 余额支付模型
require_once(APP_ROOT_PATH.'system/libs/promote.php');
class Freedelivery_promote implements promote {
	public function count_buy_total($region_id,
									$delivery_id,
									$payment,
									$account_money,
									$all_account_money,
									$ecvsn,
									$ecvpassword,
									$goods_list,
									$result,
									$paid_account_money,
									$paid_ecv_money,
									$old_result){
										
		
		
		
		$old_result['pay_total_price'] = $old_result['pay_total_price']  - $old_result['delivery_fee'];
		$old_result['pay_price'] = $old_result['pay_price'] - $old_result['delivery_fee'];
		$old_result['delivery_fee'] = 0;
		
		
		//同步计算余额支付
			$old_result['pay_price'] = $old_result['total_price'] + $old_result['delivery_fee']; //加上运费
			$old_result['pay_price'] = $old_result['pay_price'] - $old_result['paid_account_money'] - $old_result['paid_ecv_money'];
			$old_result['pay_price'] = $old_result['pay_price'] - $old_result['user_discount']; //扣除用户折扣
			
			// 当余额 + 代金券 > 支付总额时优先用代金券付款  ,代金券不够付，余额为扣除代金券后的余额
			if($old_result['ecv_money'] + $old_result['account_money'] > $old_result['pay_price'])
			{
				if($old_result['ecv_money'] >= $old_result['pay_price'])
				{
					$ecv_use_money = $old_result['pay_price'];
					$old_result['account_money'] = 0;
				}
				else
				{
					$ecv_use_money = $old_result['ecv_money'];
					$old_result['account_money'] = $old_result['pay_price'] - $ecv_use_money;
				}
			}
			else
			{
				$ecv_use_money = $old_result['ecv_money'];
			}
		
				
		    $old_result['pay_price'] = $old_result['pay_price'] - $ecv_use_money - $old_result['account_money'];
		//同步计算余额支付
		
		$result = $old_result;
		
		
		return $result;
	}
}
?>