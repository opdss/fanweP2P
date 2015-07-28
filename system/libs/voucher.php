<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

//关于代金券的全局函数
/**
 * 代金券发放
 * @param $ecv_type_id 代金券类型ID
 * @param $user_id  发放给的会员。0为线下模式的发放
 */
function send_voucher($ecv_type_id,$user_id=0,$is_password=false)
{
	$ecv_type = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."ecv_type where id = ".$ecv_type_id);
	if(!$ecv_type)return false;
	if($is_password)$ecv_data['password'] = rand(10000000,99999999);
	$ecv_data['use_limit'] = $ecv_type['use_limit'];
	$ecv_data['begin_time'] = $ecv_type['begin_time'];
	$ecv_data['end_time'] = $ecv_type['end_time'];
	$ecv_data['money'] = $ecv_type['money'];
	$ecv_data['ecv_type_id'] = $ecv_type_id;
	$ecv_data['user_id'] = $user_id;	

	do{
		$ecv_data['sn'] = uniqid();
		//$ecv_data['sn'] = md5(TIME_UTC);
		$GLOBALS['db']->autoExecute(DB_PREFIX."ecv",$ecv_data,'INSERT','','SILENT');
		$insert_id = $GLOBALS['db']->insert_id();
	}while(intval($insert_id) == 0);
	if($insert_id)
	{
		$GLOBALS['db']->query("update ".DB_PREFIX."ecv_type set gen_count = gen_count + 1 where id = ".$ecv_type_id);
	}
	return $insert_id;
}

?>