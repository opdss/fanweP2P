<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

$payment_lang = array(
	'name'	=>	'Paypal在线支付',
	'paypal_account'	=>	'Paypal帐号',
	'paypal_currency'	=>	'Paypal货币代码',
	'paypal_unit'	=>		'Paypal货币单位',
	'paypal_rate'		=>	'汇率', //如当前网站的金额为RMB，支付为USD， 则请填写汇率为 0.15 ，即当日1RMB = 0.15美元
);
$config = array(
	'paypal_account'	=>	array(
		'INPUT_TYPE'	=>	'0',
	), //Paypal帐号
	'paypal_currency'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //Paypal货币代码: 
	'paypal_unit'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //Paypal货币单位
	'paypal_rate'	=>	array(
		'INPUT_TYPE'	=>	'0'
		//汇率
	),
);
/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'Paypal';

    /* 名称 */
    $module['name']    = $payment_lang['name'];


    /* 支付方式：1：在线支付；0：线下支付 */
    $module['online_pay'] = '1';

    /* 配送 */
    $module['config'] = $config;
    
    $module['lang'] = $payment_lang;
    
    $module['reg_url'] = 'http://www.paypal.com';
    
    return $module;
}

// 余额支付模型
require_once(APP_ROOT_PATH.'system/libs/payment.php');
class Paypal_payment implements payment {

	public function get_payment_code($payment_notice_id)
	{
		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
		$order_sn = $GLOBALS['db']->getOne("select order_sn from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
		
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where id=".intval($payment_notice['payment_id']));
		$payment_info['config'] = unserialize($payment_info['config']);
		
		$money = round(($payment_notice['money'] * floatval($payment_info['config']['paypal_rate'])),2);
		
		$data_return_url = SITE_DOMAIN.APP_ROOT.'/index.php?ctl=payment&act=response&class_name=Paypal';
		$data_notify_url = SITE_DOMAIN.APP_ROOT.'/index.php?ctl=payment&act=notify&class_name=Paypal';

		
		$code  = '<form style="text-align:center;" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">' .   // 不能省略
            "<input type='hidden' name='cmd' value='_xclick'>" .                             // 不能省略
            "<input type='hidden' name='business' value='".$payment_info['config']['paypal_account']."'>" .                 // 贝宝帐号
            "<input type='hidden' name='item_name' value='".$order_sn."[PN:".$payment_notice['notice_sn']."]'>" .                 // payment for
            "<input type='hidden' name='amount' value='".$money."'>" .                        // 订单金额
            "<input type='hidden' name='currency_code' value='".$payment_info['config']['paypal_currency']."'>" .            // 货币
            "<input type='hidden' name='return' value='$data_return_url'>" .                    // 付款后页面
            "<input type='hidden' name='invoice' value='".$payment_notice['notice_sn']."'>" .                      // 订单号
            "<input type='hidden' name='charset' value='utf-8'>" .                              // 字符集
            "<input type='hidden' name='no_shipping' value='1'>" .                              // 不要求客户提供收货地址
            "<input type='hidden' name='no_note' value=''>" .                                  // 付款说明
            "<input type='hidden' name='notify_url' value='$data_notify_url'>" .
            "<input type='hidden' name='rm' value='2'>" .
            "<input type='hidden' name='cancel_return' value=''>";
		
		if(!empty($payment_info['logo']))
			$code .= '<img src='.APP_ROOT.$payment_info['logo'].' style="border:solid 1px #ccc;" />';
       
        
        $code .= "<input type='submit' class='paybutton' value='去Paypal支付'>";                      // 按钮
        $code  .= "</form>";
		$code .="<br /><span class='red'>".$GLOBALS['lang']['PAY_TOTAL_PRICE'].":".$payment_info['config']['paypal_unit']." ".number_format($money,2)."</span>";
		return $code;
		
	}
	
	public function response($request)
	{
		$payment = $GLOBALS['db']->getRow("select id,config from ".DB_PREFIX."payment where class_name='Paypal'");  
    	$payment['config'] = unserialize($payment['config']);
    	$merchant_id    = $payment['config']['paypal_account'];  
    	
    	
    	
    	// assign posted variables to local variables
        $item_name = $request['item_name'];
        $item_number = $request['item_number'];
        $payment_status = $request['payment_status'];
        $payment_amount = floatval($request['mc_gross']);
        $payment_currency = $request['mc_currency'];
        $txn_id = $request['txn_id'];
        $receiver_email = $request['receiver_email'];
        $payer_email = $request['payer_email'];
        $data_id = $request['invoice'];


        //开始初始化参数
        $payment_notice_sn = $data_id;
    	$money = $payment_amount;
    	
    	$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where notice_sn = '".$payment_notice_sn."'");
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);

		if ($payment_status != 'Completed' && $payment_status != 'Pending')
	    {
				showErr("支付不成功");	
	    }
	    elseif ($receiver_email != $merchant_id)
	    {
	             showErr("商户号不匹配");	
	    }         
	    elseif ( abs($payment_notice['money']*$payment['config']['paypal_rate']- $payment_amount) > 0.009)
	    {         	
	             showErr("金额不匹配");	
	    }
	    elseif ($payment['config']['paypal_currency'] != $payment_currency)
	    {
	             showErr("货币不对");
	    }
	    else
	    {
	    	require_once APP_ROOT_PATH."system/libs/cart.php";
			$rs = payment_paid($payment_notice['id'],$txn_id);						
			$is_paid = intval($GLOBALS['db']->getOne("select is_paid from ".DB_PREFIX."payment_notice where id = '".intval($payment_notice['id'])."'"));
			if ($is_paid == 1){
				app_redirect(url("index","payment#incharge_done",array("id"=>$payment_notice['id']))); //支付成功
			}else{
				app_redirect(url("index","payment#pay",array("id"=>$payment_notice['id'])));
			}			
	    }

	}
	
	public function notify($request)
	{
		$payment = $GLOBALS['db']->getRow("select id,config from ".DB_PREFIX."payment where class_name='Paypal'");  
    	$payment['config'] = unserialize($payment['config']);
    	$merchant_id    = $payment['config']['paypal_account'];  
    	
    	
    	
    	// assign posted variables to local variables
        $item_name = $request['item_name'];
        $item_number = $request['item_number'];
        $payment_status = $request['payment_status'];
        $payment_amount = floatval($request['mc_gross']);
        $payment_currency = $request['mc_currency'];
        $txn_id = $request['txn_id'];
        $receiver_email = $request['receiver_email'];
        $payer_email = $request['payer_email'];
        $data_id = $request['invoice'];


        //开始初始化参数
        $payment_notice_sn = $data_id;
    	$money = $payment_amount;
    	
    	$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where notice_sn = '".$payment_notice_sn."'");
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);

		if ($payment_status != 'Completed' && $payment_status != 'Pending')
	    {
				echo 0;
	    }
	    elseif ($receiver_email != $merchant_id)
	    {
	         echo 0;	
	    }         
	    elseif ( abs($payment_notice['money']*$payment['config']['paypal_rate']- $payment_amount) > 0.009)
	    {         	
	        echo 0;
	    }
	    elseif ($payment['config']['paypal_currency'] != $payment_currency)
	    {
	        echo 0;
	    }
	    else
	    {
	    	require_once APP_ROOT_PATH."system/libs/cart.php";
			$rs = payment_paid($payment_notice['id'],$txn_id);						
			if($rs)
			{
				$rs = order_paid($payment_notice['order_id']);
				echo 1;
			}
			else
			{
				echo 0;
			}
	    }
	}
	
	public function get_display_code()
	{
		$payment_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where class_name='Paypal'");
		if($payment_item)
		{
			$html = "<label class='f_l ui-radiobox' rel='common_payment' style='background:url(".APP_ROOT.$payment_item['logo'].")' title='".$payment_item['name']."'>".
					"<input type='radio' name='payment' value='".$payment_item['id']."' />&nbsp;";
					if($payment_item['logo']==""){
						$html .=$payment_item['name'];
					}
					$html .= "</label>";
			return $html;
		}
		else
		{
			return '';
		}
	}
}
?>