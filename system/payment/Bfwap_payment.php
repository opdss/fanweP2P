<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------

$payment_lang = array(
	'name'	=>	'宝付WAP支付',
	'baofoo_account'	=>	'商户号',
	'baofoo_key'		=>	'密钥',
	'baofoo_terminal'		=>	'终端号',	
);

$config = array(
	'baofoo_account'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //商户号: 
	'baofoo_key'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //密钥
	'baofoo_terminal'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //终端号	
);
/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'Bfwap';

    /* 名称 */
    $module['name']    = $payment_lang['name'];


   	/* 支付方式：1：在线支付；0：线下支付; 2:手机支付 */
    $module['online_pay'] = '2';

    /* 配送 */
    $module['config'] = $config;
    
    $module['lang'] = $payment_lang;
    $module['reg_url'] = 'http://www.baofoo.com';
    return $module;
}

require_once(APP_ROOT_PATH.'system/libs/payment.php');
class Bfwap_payment implements payment {	
	public function get_payment_code($payment_notice_id) {
		
        $payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
		//$order = $GLOBALS['db']->getRow("select order_sn,bank_id from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
		
					
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where id=".intval($payment_notice['payment_id']));
		$payment_info['config'] = unserialize($payment_info['config']);
       
       // $_Merchant_url =  SITE_DOMAIN.APP_ROOT.'/baofoo_callback.php?act=response';
       // $_Return_url = SITE_DOMAIN.APP_ROOT.'/baofoo_callback.php?act=notify';

        $_Merchant_url = SITE_DOMAIN.APP_ROOT.'/callback/pay/bfwap_response.php';
        $_Return_url = SITE_DOMAIN.APP_ROOT.'/callback/pay/bfwap_notify.php';
        $_Merchant_url = str_replace("/mapi", "", $_Merchant_url);
        $_Return_url = str_replace("/mapi", "", $_Return_url);
        
        //商户号
        $_MerchantID = $payment_info['config']['baofoo_account'];
        //
        $_Md5Key = $payment_info['config']['baofoo_key'];
        //终端号
        $_TerminalID = $payment_info['config']['baofoo_terminal'];
        
    
        $_PayID = 'all';
        
        /* 交易日期 */
        $_TradeDate = to_date($payment_notice['create_time'], 'YmdHis');
		
        //商户流水号 商户唯一订单号，8-50位
        $_TransID = $payment_notice['notice_sn'];
        
		//订单金额  单位：分
        $_OrderMoney = round($payment_notice['money'],2);
        
        //商品名称 需URL编码
        $_ProductName = '';
        
        //通知方式
        $_NoticeType = 1;
       
		$_AdditionalInfo = $payment_notice_id;
		$_Md5_OrderMoney = $_OrderMoney*100;
		$MARK = "|";
      	$_Signature=md5($_MerchantID.$MARK.$_PayID.$MARK.$_TradeDate.$MARK.$_TransID.$MARK.$_Md5_OrderMoney.$MARK.$_Merchant_url.$MARK.$_Return_url.$MARK.$_NoticeType.$MARK.$_Md5Key);

      	
      	
      	
      	/*交易参数*/
        $parameter = array(
            'MemberID' => $_MerchantID,
        	'TerminalID' => $_TerminalID,            
        	'InterfaceVersion' => '4.0',//接口版本号
			'PayID' => $_PayID,//支付方式
			'TradeDate' => $_TradeDate,//交易时间
        	'TransID' => $_TransID,//流水号
			'OrderMoney' => $_Md5_OrderMoney,//订单金额
			'ProductName' => $_TransID,//产品名称
			'Amount' => 1,//数量			
			'Username' => '',//支付用户名
			'AdditionalInfo' => $_AdditionalInfo,//订单附加消息
			'PageUrl' => $_Merchant_url,//商户通知地址 
			'ReturnUrl' => $_Return_url,//用户通知地址
			'NoticeType' => $_NoticeType,//通知方式
        	'KeyType'=> 1,       //加密类型 1：md5
			'Signature'=>$_Signature			
        );
        
        /*
        $def_url = '<form style="text-align:center;" action="https://tgw.baofoo.com/wapmobile" target="_blank" style="margin:0px;padding:0px" method="POST" >';

        foreach ($parameter AS $key => $val) {
            $def_url .= "<input type='hidden' name='$key' value='$val' />";
        }
        $def_url .= "<input type='submit' class='paybutton' value='前往".$this->payment_lang['baofoo_gateway_'.intval($_PayID)]."' />";
        $def_url .= "</form>";
        */
        
        //https://tgw.baofoo.com/wapmobile;
        //https://gw.baofoo.com/wapmobile
        $url = 'https://gw.baofoo.com/wapmobile?'.http_build_query($parameter);
        
        $pay = array();
        $pay['subject'] = $_TransID;
        $pay['body'] = '会员充值';
        $pay['total_fee'] = $_OrderMoney;
        $pay['total_fee_format'] = format_price($_OrderMoney);
        $pay['out_trade_no'] = $payment_notice['notice_sn'];
        $pay['notify_url'] = $url;
        
        $pay['partner'] = '';//$payment_info['config']['alipay_partner'];//合作商户ID
        $pay['seller'] = '';//$payment_info['config']['alipay_account'];//账户ID
        
        $pay['key'] = '';//$payment_info['config']['alipay_key'];//支付宝(RSA)公钥
        $pay['is_wap'] = 1;//
        $pay['pay_code'] = 'bfwap';
        
        return $pay;
    }

     public function response($request) {
		$return_res = array(
            'info' => '',
            'status' => false,
        );
		
        /* 取返回参数 */
        $MemberID=$request['MemberID'];//商户号
		$TerminalID =$request['TerminalID'];//商户终端号
		$TransID =$request['TransID'];//商户流水号
		$Result=$request['Result'];//支付结果
		$ResultDesc=$request['ResultDesc'];//支付结果描述
		$FactMoney=$request['FactMoney'];//实际成功金额
		$AdditionalInfo=$request['AdditionalInfo'];//订单附加消息
		$SuccTime=$request['SuccTime'];//支付完成时间
		$Md5Sign=$request['Md5Sign'];//md5签名
		
		
        /*获取支付信息*/
        $payment = $GLOBALS['db']->getRow("select id,config from ".DB_PREFIX."payment where class_name='Bfwap'");  
    	$payment['config'] = unserialize($payment['config']);
    	
		$_Md5Key= $payment['config']['baofoo_key'];
		$payment_notice_sn = intval($AdditionalInfo);
		$gopayOutOrderId =  $TransID;
		
		$MARK = "~|~";
        /*比对连接加密字符串*/
		$WaitSign=md5('MemberID='.$MemberID.$MARK.'TerminalID='.$TerminalID.$MARK.'TransID='.$TransID.$MARK.'Result='.$Result.$MARK.'ResultDesc='.$ResultDesc.$MARK.'FactMoney='.$FactMoney.$MARK.'AdditionalInfo='.$AdditionalInfo.$MARK.'SuccTime='.$SuccTime.$MARK.'Md5Sign='.$_Md5Key);

        if ($Md5Sign != $WaitSign) {
        	echo $GLOBALS['payment_lang']["VALID_ERROR"];
        } else {
	        $payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = '".$payment_notice_sn."'");
			require_once APP_ROOT_PATH."system/libs/cart.php";
			$rs = payment_paid($payment_notice['id'],$gopayOutOrderId);		

			$is_paid = intval($GLOBALS['db']->getOne("select is_paid from ".DB_PREFIX."payment_notice where id = '".intval($payment_notice['id'])."'"));
			if ($is_paid == 1){
				echo "支付成功1<br />请点左上角<b>返回</b>按钮";
			}else{
				echo "支付失败3<br />请点左上角<b>返回</b>按钮";
			}			
        }
    }
    
     public function notify($request) {
		$return_res = array(
            'info' => '',
            'status' => false,
        );
		
         /* 取返回参数 */
        $MemberID=$request['MemberID'];//商户号
		$TerminalID =$request['TerminalID'];//商户终端号
		$TransID =$request['TransID'];//商户流水号
		$Result=$request['Result'];//支付结果
		$ResultDesc=$request['ResultDesc'];//支付结果描述
		$FactMoney=$request['FactMoney'];//实际成功金额
		$AdditionalInfo=$request['AdditionalInfo'];//订单附加消息
		$SuccTime=$request['SuccTime'];//支付完成时间
		$Md5Sign=$request['Md5Sign'];//md5签名
		

        /*获取支付信息*/
        $payment = $GLOBALS['db']->getRow("select id,config from ".DB_PREFIX."payment where class_name='Bfwap'");  
    	$payment['config'] = unserialize($payment['config']);
    	
		$_Md5Key= $payment['config']['baofoo_key'];
		$payment_notice_sn = intval($AdditionalInfo);
		$gopayOutOrderId =  $TransID;
		
		$MARK = "~|~";
        /*比对连接加密字符串*/
		$WaitSign=md5('MemberID='.$MemberID.$MARK.'TerminalID='.$TerminalID.$MARK.'TransID='.$TransID.$MARK.'Result='.$Result.$MARK.'ResultDesc='.$ResultDesc.$MARK.'FactMoney='.$FactMoney.$MARK.'AdditionalInfo='.$AdditionalInfo.$MARK.'SuccTime='.$SuccTime.$MARK.'Md5Sign='.$_Md5Key);

        if ($Md5Sign != $WaitSign) {
        	echo "Md5CheckFail";
        } else {
	        $payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = '".$payment_notice_sn."'");
			$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
			require_once APP_ROOT_PATH."system/libs/cart.php";
			$rs = payment_paid($payment_notice['id'],$gopayOutOrderId);						
			if($rs)
			{
				$rs = order_paid($payment_notice['order_id']);				
				if($rs)
				{
					//开始更新相应的outer_notice_sn					
					//$GLOBALS['db']->query("update ".DB_PREFIX."payment_notice set outer_notice_sn = '".$gopayOutOrderId."' where id = ".$payment_notice['id']);
					echo "OK";
				}
				else 
				{
					echo "OK";
				}
			}
			else
			{
				echo "OrderFail";
			}
        }
    }

    public function get_display_code() {
        return '';
    }

    
    public function orderquery($payment_notice_id){
    	
    }
}
?>
