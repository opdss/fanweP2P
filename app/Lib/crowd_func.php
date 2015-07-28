<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

function syn_deal($deal_id)
{
	$deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."crowd where id = ".$deal_id);
	
	if($deal_info)
	{
		$deal_info['comment_count'] = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."crowd_comment where deal_id = ".$deal_info['id']." and log_id = 0"));
		$deal_info['support_count'] = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."crowd_support_log where deal_id = ".$deal_info['id']));
		$deal_info['focus_count'] = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."crowd_focus_log where deal_id = ".$deal_info['id']));
		$deal_info['view_count'] = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."crowd_visit_log where deal_id = ".$deal_info['id']));
		$deal_info['support_amount'] = doubleval($GLOBALS['db']->getOne("select sum(price) from ".DB_PREFIX."crowd_support_log where deal_id = ".$deal_info['id']));
		$deal_info['delivery_fee_amount'] = doubleval($GLOBALS['db']->getOne("select sum(delivery_fee) from ".DB_PREFIX."crowd_order where deal_id = ".$deal_info['id']." and order_status=3 "));
		
		if($deal_info['type']==0){
			$deal_info["virtual_num"]=$GLOBALS['db']->getOne("select sum(virtual_person) from ".DB_PREFIX."crowd_item where deal_id=".$deal_id);
			$deal_info["virtual_price"]=$GLOBALS['db']->getOne("select sum(virtual_person*price) from ".DB_PREFIX."crowd_item where deal_id=".$deal_id);
			if(($deal_info['support_amount']+$deal_info["virtual_price"])>=$deal_info['limit_price'])
			{
				$deal_info['is_success'] = 1;
			}
			else
			{
				$deal_info['is_success'] = 0;
			}
		}elseif($deal_info['type']==1){
			
		}
		
		if($deal_info['pay_radio'] > 0){
			$deal_info['pay_amount'] = ($deal_info['support_amount']*(1-$deal_info['pay_radio']))+$deal_info['delivery_fee_amount'];
		}
		else
		{
			$deal_info['pay_amount'] = ($deal_info['support_amount']*(1-app_conf("PAY_RADIO")))+$deal_info['delivery_fee_amount'];
		
		}
 
		
		
		$deal_info['tags_match'] = "";
		$deal_info['tags_match_row'] = "";
		$GLOBALS['db']->autoExecute(DB_PREFIX."crowd", $deal_info, $mode = 'UPDATE', "id=".$deal_info['id'], $querymode = 'SILENT');	
		
		$tags_arr = preg_split("/[, ]/",$deal_info["tags"]);

		foreach($tags_arr as $tgs){
			if(trim($tgs)!="")
			insert_match_item(trim($tgs),"crowd",$deal_info['id'],"tags_match");
		}
		
		$name_arr = div_str($deal_info['name']);
		foreach($name_arr as $name_item){
			if(trim($name_item)!="")
			insert_match_item(trim($name_item),"crowd",$deal_info['id'],"name_match");
		}

	}


}
 function  get_type_name($type){
 	switch($type){
 		case 0:
 		return '产品众筹';
 		break;
 		case 1:
 		return '股权众筹';
 		break;
 	}
 }
 
	
?>