<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class paymentModule extends SiteBaseModule
{
	public function pay()
	{
		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".intval($_REQUEST['id']));
		
		if($payment_notice)
		{
			if($payment_notice['is_paid'] == 0)
			{
				$payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id = ".$payment_notice['payment_id']);
				
				if($payment_info){
					require_once APP_ROOT_PATH."system/payment/".$payment_info['class_name']."_payment.php";
					$payment_class = $payment_info['class_name']."_payment";
					$payment_object = new $payment_class();
					$payment_code = $payment_object->get_payment_code($payment_notice['id']);
				}
				
				$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['PAY_NOW']);
				$GLOBALS['tmpl']->assign("payment_code",$payment_code);
				//$GLOBALS['tmpl']->assign("order",$order);
				$GLOBALS['tmpl']->assign("payment_notice",$payment_notice);
				if(intval($_REQUEST['check'])==1)
				{
					showErr($GLOBALS['lang']['PAYMENT_NOT_PAID_RENOTICE']);
				}
				
				
				$GLOBALS['tmpl']->display("page/payment_pay.html");
			}
			else
			{				
				showSuccess('支付成功',0,APP_ROOT."/",1);
			}
		}
		else
		{
			showErr($GLOBALS['lang']['NOTICE_SN_NOT_EXIST'],0,APP_ROOT."/",1);
		}
	}
	public function tip()
	{
		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".intval($_REQUEST['id']));
		$GLOBALS['tmpl']->assign("payment_notice",$payment_notice);
		$GLOBALS['tmpl']->display("page/payment_tip.html");
	}
	
	public function done()
	{
		/*
		$order_id = intval($_REQUEST['id']);
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id);
		
		$deal_ids = $GLOBALS['db']->getOne("select group_concat(deal_id) from ".DB_PREFIX."deal_order_item where order_id = ".$order_id);
		if(!$deal_ids)
		$deal_ids = 0;
		$order_deals = $GLOBALS['db']->getAll("select d.* from ".DB_PREFIX."deal as d where id in (".$deal_ids.")");

		$GLOBALS['tmpl']->assign("order_info",$order_info);
		$GLOBALS['tmpl']->assign("order_deals",$order_deals);
		$is_coupon = 0;	
		$send_coupon_sms = 0;
		foreach($order_deals as $k=>$v)
		{
			if($v['is_coupon'] == 1&&$v['buy_status']>0)
			{
				$is_coupon = 1;
				break;
			}
		}
		
		foreach($order_deals as $k=>$v)
		{
			if($v['forbid_sms'] == 0)
			{
				$send_coupon_sms = 1;
				break;
			}
		}
	
		$is_lottery = 0;	
		foreach($order_deals as $k=>$v)
		{
			if($v['is_lottery'] == 1&&$v['buy_status']>0)
			{
				$is_lottery = 1;
				break;
			}
		}
		
		$GLOBALS['tmpl']->assign("is_lottery",$is_lottery);
		$GLOBALS['tmpl']->assign("is_coupon",$is_coupon);
		$GLOBALS['tmpl']->assign("send_coupon_sms",$send_coupon_sms);
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['PAY_SUCCESS']);
		$GLOBALS['tmpl']->display("page/payment_done.html");
		*/
	}
	
	public function incharge_done()
	{
		$order_id = intval($_REQUEST['id']);
		//$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id);
		//$order_deals = $GLOBALS['db']->getAll("select d.* from ".DB_PREFIX."deal as d where id in (select distinct deal_id from ".DB_PREFIX."deal_order_item where order_id = ".$order_id.")");
		//$GLOBALS['tmpl']->assign("order_info",$order_info);
		//$GLOBALS['tmpl']->assign("order_deals",$order_deals);
	
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['PAY_SUCCESS']);
		$GLOBALS['tmpl']->display("page/payment_done.html");
	}
	
	public function response()
	{
		//支付跳转返回页
		if($GLOBALS['pay_req']['class_name'])
			$_REQUEST['class_name'] = $GLOBALS['pay_req']['class_name'];
				
		$class_name = addslashes(trim($_REQUEST['class_name']));
		$payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where class_name = '".$class_name."'");
		if($payment_info)
		{
			require_once APP_ROOT_PATH."system/payment/".$payment_info['class_name']."_payment.php";
			$payment_class = $payment_info['class_name']."_payment";
			$payment_object = new $payment_class();
			adddeepslashes($_REQUEST);
			$payment_code = $payment_object->response($_REQUEST);
		}
		else
		{
			showErr($GLOBALS['lang']['PAYMENT_NOT_EXIST']);
		}
	}
	
	public function notify()
	{
		//支付跳转返回页
		if($GLOBALS['pay_req']['class_name'])
			$_REQUEST['class_name'] = $GLOBALS['pay_req']['class_name'];
		
		$class_name = addslashes(trim($_REQUEST['class_name']));
		$payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where class_name = '".$class_name."'");
		if($payment_info)
		{
			require_once APP_ROOT_PATH."system/payment/".$payment_info['class_name']."_payment.php";
			$payment_class = $payment_info['class_name']."_payment";
			$payment_object = new $payment_class();
			adddeepslashes($_REQUEST);
			$payment_code = $payment_object->notify($_REQUEST);
		}
		else
		{
			showErr($GLOBALS['lang']['PAYMENT_NOT_EXIST']);
		}
	}
}
?>