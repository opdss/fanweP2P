<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

$payment_lang = array(
	'name'	=>	'环讯支付',
	'Mer_code'	=>	'商户号',
	'Mer_key'	=>	'证书',
	'VALID_ERROR'	=>	'支付验证失败',
	'PAY_FAILED'	=>	'支付失败',
	'GO_TO_PAY'	=>	'前往环讯支付',
);
$config = array(
	'Mer_code'	=>	array(
		'INPUT_TYPE'	=>	'0',
	), //商户号
	'Mer_key'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //证书
);
/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'Ips';
    
    /* 名称 */
    $module['name']    = $payment_lang['name'];

    /* 支付方式：1：在线支付；0：线下支付 */
    $module['online_pay'] = '1';

    /* 配送 */
    $module['config'] = $config;
    
    $module['lang'] = $payment_lang;
    
    $module['reg_url'] = 'http://account.ips.com.cn/';
    
    return $module;
}

// 环讯支付模型
require_once(APP_ROOT_PATH.'system/libs/payment.php');

class Ips_payment implements payment {
	private $payment_lang = array(
		'GO_TO_PAY'	=>	'前往%s支付',
	);
	
	function get_payment_code($payment_notice_id){
	
		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
	
		//$order_sn = $GLOBALS['db']->getOne("select order_sn from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
		$money = round($payment_notice['money'],2);
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where id=".intval($payment_notice['payment_id']));
		$payment_info['config'] = unserialize($payment_info['config']);

        /* 获得订单的流水号，补零到10位 */
        $sp_billno = $payment_notice_id;   
        
        /* 交易日期 */
        $date = to_date($payment_notice['create_time'],'Ymd');
        
        $billno = $payment_notice['notice_sn'];
        $desc = $billno;
        $attach = $payment_info['config']['Mer_key'];
        
		//商户号
		$Mer_code = $payment_info['config']['Mer_code'];
		
		//商户证书：登陆http://merchant.ips.com.cn/商户后台下载的商户证书内容
		$Mer_key = $payment_info['config']['Mer_key'];
		
		//订单金额(保留2位小数)
		$Amount = number_format($money, 2, '.', '');
		
		//币种
		$Currency_Type = "RMB";		
		
		//支付卡种
		$Gateway_Type = "01";		
		
		//语言
		$Lang = "GD";		
		
		//支付结果成功返回的商户URL
		$Merchanturl =SITE_DOMAIN.APP_ROOT.'/index.php?ctl=payment&act=response&class_name=Ips';
		$FailUrl = SITE_DOMAIN.url("shop","payment#done",array("id"=>$payment_notice['order_id']));
		
	    //支付结果错误返回的商户URL
		$ErrorUrl = "";		
		
		//商户数据包
		$Attach ="";		
		
		//显示金额
		$DispAmount = $money*100;
				
		//订单支付接口加密方式
		$OrderEncodeType = "5";		
		
		//交易返回接口加密方式 
		$RetEncodeType = "17";		
		
		//返回方式
		$Rettype = "1";		
		
		//Server to Server 返回页面URL
		$ServerUrl = SITE_DOMAIN.APP_ROOT.'/index.php?ctl=payment&act=notify&class_name=Ips';
		
		//订单支付接口的Md5摘要，原文=订单号+金额+日期+支付币种+商户证书 
		$orge = 'billno'.$billno.'currencytype'.$Currency_Type.'amount'.$Amount.'date'.$date.'orderencodetype'.$OrderEncodeType.$Mer_key ;
		//echo '明文:'.$orge ;
		//$SignMD5 = md5('billno'.$billno.'currencytype'.$Currency_Type.'amount'.$Amount.'date'.$date.'orderencodetype'.$OrderEncodeType.$Mer_key);
		$SignMD5 = md5($orge) ;
        

        /* 交易参数 */
        $parameter = array(
            'Mer_code'             => $Mer_code,                     // 商户号
            'Billno'         => $billno,                  // 商户订单编号
            'Amount'              => $Amount,                       // 订单金额(保留2位小数)
            'Date'      => $date,                          // 订单日期
            'Currency_Type'      => "RMB",  // 币种
            'Gateway_Type'    => "01",             //支付卡种
            'Lang'         => "GD",                  // 语言
            'Merchanturl' => $Merchanturl,  
	        'FailUrl' => $FailUrl,  
	        'ErrorUrl' => $ErrorUrl,
	        'Attach' => $Attach,  
	        'DispAmount' => $DispAmount,  
	        'OrderEncodeType' => $OrderEncodeType,  
	        'RetEncodeType' => $RetEncodeType,  
	        'Rettype' => $Rettype,  
	        'ServerUrl' => $ServerUrl,  
	        'SignMD5' => $SignMD5,            
        );

		$payLinks = '<form style="text-align:center;" action="https://pay.ips.com.cn/ipayment.aspx" target="_blank" style="margin:0px;padding:0px" method="post" >';
	 	foreach ($parameter AS $key=>$val)
        {
            $payLinks  .= "<input type='hidden' name='$key' value='$val' />";
        }
    	if(!empty($payment_info['logo']))
		{
			$payLinks .= "<input type='image' src='".APP_ROOT.$payment_info['logo']."' style='border:solid 1px #ccc;'><div class='blank'></div>";
		}
		$payLinks .= "<input type='submit' class='paybutton' value='前往环讯支付'></form>";
        $code = '<div style="text-align:center">'.$payLinks.'</div>';
		$code.="<br /><div style='text-align:center' class='red'>".$GLOBALS['lang']['PAY_TOTAL_PRICE'].":".format_price($money)."</div>";
        return $code;
	}
	function response($request){
		
        /*取返回参数*/
        $billno = $request['billno'];
		$amount = $request['amount'];
		$mydate = $request['date'];
		$succ = $request['succ'];
		$msg = $request['msg'];
		$attach = $request['attach'];
		$ipsbillno = $request['ipsbillno'];
		$retEncodeType = $request['retencodetype'];
		$currency_type = $request['Currency_type'];
		$signature = $request['signature'];
		$content = 'billno'.$billno.'currencytype'.$currency_type.'amount'.$amount.'date'.$mydate.'succ'.$succ.'ipsbillno'.$ipsbillno.'retencodetype'.$retEncodeType;
		
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where class_name='Ips'");  
		$payment_info['config'] = unserialize($payment_info['config']);
		$payment_info['config']['Mer_key'];
		
		//请在该字段中放置商户登陆merchant.ips.com.cn下载的证书
		$cert = $payment_info['config']['Mer_key'];
		$signature_1ocal = md5($content . $cert);
		
    	
		if ($signature_1ocal == $signature && $succ =="Y")
		{
			$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where notice_sn = '".$billno."'");
			
			$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
			require_once APP_ROOT_PATH."system/libs/cart.php";
			$rs = payment_paid($payment_notice['id'],$ipsbillno);	
		
			$is_paid = intval($GLOBALS['db']->getOne("select is_paid from ".DB_PREFIX."payment_notice where id = '".intval($payment_notice['id'])."'"));
			if ($is_paid == 1){
				app_redirect(url("index","payment#incharge_done",array("id"=>$payment_notice['id']))); //支付成功
			}else{
				app_redirect(url("index","payment#pay",array("id"=>$payment_notice['id'])));
			}
		}
		else
		{
			showErr($GLOBALS['payment_lang']["PAY_FAILED"]);
		}
	}
	function notify($request){
		
        /*取返回参数*/
        $billno = $request['billno'];
		$amount = $request['amount'];
		$mydate = $request['date'];
		$succ = $request['succ'];
		$msg = $request['msg'];
		$attach = $request['attach'];
		$ipsbillno = $request['ipsbillno'];
		$retEncodeType = $request['retencodetype'];
		$currency_type = $request['Currency_type'];
		$signature = $request['signature'];
		$content = 'billno'.$billno.'currencytype'.$currency_type.'amount'.$amount.'date'.$mydate.'succ'.$succ.'ipsbillno'.$ipsbillno.'retencodetype'.$retEncodeType;
		
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where class_name='Ips'");  
		$payment_info['config'] = unserialize($payment_info['config']);
		$payment_info['config']['Mer_key'];
		
		//请在该字段中放置商户登陆merchant.ips.com.cn下载的证书
		$cert = $payment_info['config']['Mer_key'];
		$signature_1ocal = md5($content . $cert);
		
    	
		if ($signature_1ocal == $signature && $succ =="Y")
		{
			$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where notice_sn = '".$billno."'");
			$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
			require_once APP_ROOT_PATH."system/libs/cart.php";
			$rs = payment_paid($payment_notice['id'],$ipsbillno);						
			if($rs)
			{
				$rs = order_paid($payment_notice['order_id']);				
				if($rs)
				{
					//开始更新相应的outer_notice_sn					
					//$GLOBALS['db']->query("update ".DB_PREFIX."payment_notice set outer_notice_sn = '".$ipsbillno."' where id = ".$payment_notice['id']);
					echo "Success";
					die();
				}
				else 
				{
					echo "Success";
					die();
				}
			}
			else
			{
				echo "Success";
				die();
			}
		}
		else
		{
			echo "Failed";
			die();
		}
	}
	
	
	
	function get_display_code(){
		$payment_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where class_name='Ips'");
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
